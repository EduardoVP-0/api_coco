<?php
// Headers para CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// SIMPLE TEST - SIN BASE DE DATOS
$json = file_get_contents('php://input');

// LOG TODO
file_put_contents('debug_log.txt', 
    "=== " . date('Y-m-d H:i:s') . " ===\n" .
    "METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n" .
    "CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'NO SET') . "\n" .
    "JSON_RECIBIDO: " . $json . "\n" .
    "LONGITUD: " . strlen($json) . "\n" .
    "JSON_ERROR: " . json_last_error_msg() . "\n\n",
    FILE_APPEND
);

if (empty($json)) {
    echo json_encode([
        "success" => false, 
        "message" => "JSON VACÍO - Longitud: " . strlen($json)
    ]);
    exit;
}

$input = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        "success" => false, 
        "message" => "JSON INVÁLIDO - Error: " . json_last_error_msg()
    ]);
    exit;
}

// SI LLEGA AQUÍ, EL JSON ES VÁLIDO
echo json_encode([
    "success" => true, 
    "message" => "✅ JSON VÁLIDO RECIBIDO",
    "campos_recibidos" => array_keys($input),
    "numero_campos" => count($input),
    "datos_ejemplo" => [
        'nombre' => $input['nombre'] ?? 'NO_RECIBIDO',
        'rfc' => $input['rfc'] ?? 'NO_RECIBIDO',
        'lada' => $input['lada'] ?? 'NO_RECIBIDO'
    ]
]);
?>