<?php
//CAMBIAR EL NOMBRE DE LA TABLA


// Incluir la conexión primero
include 'db.php';

// Headers para CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Verificar si la conexión se estableció correctamente
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error de conexión a la base de datos: " . $conn->connect_error
    ]);
    exit;
}

// Obtener el contenido JSON del request
$json = file_get_contents('php://input');
$input = json_decode($json, true);

// Verificar si se pudo decodificar el JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "message" => "JSON inválido: " . json_last_error_msg()
    ]);
    exit;
}

if ($input === null) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "message" => "No se recibieron datos JSON válidos"
    ]);
    exit;
}

// Extraer datos con valores por defecto
$puntuacion = isset($input['puntuacion']) ? intval($input['puntuacion']) : 0;
$comentario = isset($input['comentario']) ? trim($input['comentario']) : '';

// Validar campos obligatorios
if ($puntuacion == 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "message" => "La puntuación es obligatoria y debe ser un número entre 1 y 5"
    ]);
    exit;
}

// Validar rango de puntuación
if ($puntuacion < 1 || $puntuacion > 5) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "message" => "La puntuación debe estar entre 1 y 5. Recibido: " . $puntuacion
    ]);
    exit;
}

// Preparar la consulta SQL
$sql = "INSERT INTO calificacion_curso (puntuacion, comentario) VALUES (?, ?)";

// Preparar statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error preparando la consulta: " . $conn->error
    ]);
    exit;
}

// Bind parameters
if (!$stmt->bind_param("is", $puntuacion, $comentario)) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error en bind_param: " . $stmt->error
    ]);
    $stmt->close();
    exit;
}

// Ejecutar la consulta
if ($stmt->execute()) {
    // Éxito
    echo json_encode([
        "success" => true, 
        "message" => "Calificación guardada correctamente",
        "id_insertado" => $stmt->insert_id
    ]);
} else {
    // Error en la ejecución
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error ejecutando la consulta: " . $stmt->error
    ]);
}

// Cerrar statement y conexión
$stmt->close();
$conn->close();
?>