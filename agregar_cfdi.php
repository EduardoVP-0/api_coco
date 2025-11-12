<?php
include 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// OBTENER LOS DATOS JSON DEL REQUEST - ESTO ES LO QUE FALTA
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "No se recibieron datos JSON"]);
    exit;
}

// Extraer datos del CFDI
$folio_fiscal = $input['folio_fiscal'] ?? '';
$folio = $input['folio'] ?? '';
$fecha = $input['fecha'] ?? '';
$serie = $input['serie'] ?? '';
$hora = $input['hora'] ?? '';
$uso_cfdi = $input['uso_cfdi'] ?? '';
$moneda = $input['moneda'] ?? '';
$forma_pago = $input['forma_pago'] ?? '';
$rfc_emisor = $input['rfc_emisor'] ?? '';
$nombre_emisor = $input['nombre_emisor'] ?? '';
$rfc_receptor = $input['rfc_receptor'] ?? '';
$nombre_receptor = $input['nombre_receptor'] ?? '';
$codigo_postal = $input['codigo_postal'] ?? '';

// Extraer datos del primer producto (puedes modificar para múltiples productos)
$clave_producto = $input['clave_producto'] ?? '';
$cantidad_producto = $input['cantidad_producto'] ?? '';
$unidad_producto = $input['unidad_producto'] ?? '';
$valor_unitario_producto = $input['valor_unitario_producto'] ?? '0';
$importe_producto = $input['importe_producto'] ?? '0';
$descripcion_producto = $input['descripcion_producto'] ?? '';
$impuesto_producto = $input['impuesto_producto'] ?? '';
$tipo_impuesto_producto = $input['tipo_impuesto_producto'] ?? '';
$tasa_cuota_producto = $input['tasa_cuota_producto'] ?? '0';
$importe_impuesto_producto = $input['importe_impuesto_producto'] ?? '0';

// DEBUG: Mostrar datos recibidos
error_log("Datos recibidos: " . print_r($input, true));

// Validar campos obligatorios
if (empty($rfc_emisor) || empty($nombre_emisor) || empty($rfc_receptor) || empty($nombre_receptor)) {
    echo json_encode(["success" => false, "message" => "Campos obligatorios faltantes"]);
    exit;
}

// Preparar y ejecutar INSERT
$sql = "INSERT INTO CFDI (
    folio_fiscal, folio, fecha, serie, hora, uso_cfdi, moneda, forma_pago,
    rfc_emisor, nombre_emisor, rfc_receptor, nombre_receptor, codigo_postal,
    clave_producto, cantidad_producto, unidad_producto, valor_unitario_producto,
    importe_producto, descripcion_producto, impuesto_producto, 
    tipo_impuesto_producto, tasa_cuota_producto, importe_impuesto_producto
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
    exit;
}

// Convertir a números para el bind_param
$valor_unitario_num = floatval($valor_unitario_producto);
$importe_num = floatval($importe_producto);
$tasa_cuota_num = floatval($tasa_cuota_producto);
$importe_impuesto_num = floatval($importe_impuesto_producto);

$stmt->bind_param(
    "sssssssssssssssssdsssdd",
    $folio_fiscal, $folio, $fecha, $serie, $hora, $uso_cfdi, $moneda, $forma_pago,
    $rfc_emisor, $nombre_emisor, $rfc_receptor, $nombre_receptor, $codigo_postal,
    $clave_producto, $cantidad_producto, $unidad_producto, $valor_unitario_num,
    $importe_num, $descripcion_producto, $impuesto_producto,
    $tipo_impuesto_producto, $tasa_cuota_num, $importe_impuesto_num
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "CFDI guardado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al guardar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>