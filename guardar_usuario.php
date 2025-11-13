<?php
// Headers para CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Configuración de conexión DIRECTA
$host = "localhost";
$port = 3307;
$user = "root";
$pass = "root";
$dbname = "coco_fiscal";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos: " . $conn->connect_error]);
    exit;
}

// Obtener datos JSON
$json = file_get_contents('php://input');

if (empty($json)) {
    echo json_encode(["success" => false, "message" => "No se recibieron datos"]);
    exit;
}

$input = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Datos JSON inválidos: " . json_last_error_msg()]);
    exit;
}

// Extraer TODOS los campos individualmente
$nombre = $input['nombre'] ?? '';
$rfc = $input['rfc'] ?? '';
$regimen_fiscal = $input['regimenFiscal'] ?? '';
$email = $input['email'] ?? '';
$pass = $input['pass'] ?? '';
$lada = $input['lada'] ?? '';
$celular = $input['celular'] ?? '';
$fecha_nacimiento_input = $input['fechaNacimiento'] ?? '';

// Extraer campos de domicilio individualmente
$calle = $input['calle'] ?? '';
$colonia = $input['colonia'] ?? '';
$numero = $input['numero'] ?? '';
$cp = $input['cp'] ?? '';
$ciudad = $input['ciudad'] ?? '';
$estado = $input['estado'] ?? '';
$pais = $input['pais'] ?? '';

// FUNCIÓN PARA CONVERTIR FORMATO DE FECHA
function convertirFecha($fecha_input) {
    if (empty($fecha_input)) {
        return null;
    }
    
    // Intentar convertir formato d/M/YYYY a YYYY-MM-DD
    $fecha_convertida = DateTime::createFromFormat('d/m/Y', $fecha_input);
    
    if ($fecha_convertida === false) {
        // Si falla, intentar otro formato común
        $fecha_convertida = DateTime::createFromFormat('m/d/Y', $fecha_input);
    }
    
    if ($fecha_convertida === false) {
        // Si aún falla, devolver null
        return null;
    }
    
    return $fecha_convertida->format('Y-m-d');
}

// Convertir fecha al formato MySQL
$fecha_nacimiento = convertirFecha($fecha_nacimiento_input);

// Validar campos obligatorios
if (empty($nombre) || empty($rfc) || empty($regimen_fiscal) || empty($email) || empty($pass)) {
    echo json_encode(["success" => false, "message" => "Faltan campos obligatorios"]);
    exit;
}

// Preparar INSERT CORREGIDO
$sql = "INSERT INTO usuario (
    nombre, rfc, regimen_fiscal, email, lada, celular, fecha_nacimiento,
    calle, colonia, numero, cp, ciudad, estado, pais, password
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}

// Bind parameters - usar NULL si la fecha está vacía o no se pudo convertir
$fecha_nacimiento_bind = empty($fecha_nacimiento) ? null : $fecha_nacimiento;

$bind_result = $stmt->bind_param(
    "sssssssssssssss",  // 15 "s" para 15 parámetros
    $nombre, $rfc, $regimen_fiscal, $email, $lada, $celular, $fecha_nacimiento_bind,
    $calle, $colonia, $numero, $cp, $ciudad, $estado, $pais, $pass
);

if (!$bind_result) {
    echo json_encode(["success" => false, "message" => "Error en los parámetros: " . $stmt->error]);
    $stmt->close();
    exit;
}

// Ejecutar la consulta
if ($stmt->execute()) {
    $id_insertado = $stmt->insert_id;
    echo json_encode([
        "success" => true, 
        "message" => "Cuenta registrada correctamente",
        "id_usuario" => $id_insertado
    ]);
} else {
    if ($conn->errno == 1062) {
        echo json_encode(["success" => false, "message" => "El RFC o Email ya están registrados"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar los datos: " . $stmt->error]);
    }
}

$stmt->close();
$conn->close();
?>