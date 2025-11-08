<?php
include 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Marca el inicio del script
file_put_contents("debug.txt", "Inicio del script\n", FILE_APPEND);

// Captura el JSON recibido
$jsonInput = file_get_contents("php://input");
file_put_contents("debug.txt", "JSON recibido: " . $jsonInput . "\n", FILE_APPEND);

// Verifica conexión a la base de datos
if (!$conn || $conn->connect_error) {
  file_put_contents("debug.txt", "Error de conexión: " . $conn->connect_error . "\n", FILE_APPEND);
  echo json_encode(["success" => false, "error" => "Conexión fallida"]);
  exit;
}

// Decodifica el JSON
$data = json_decode($jsonInput);

if (!$data || !isset($data->titulo)) {
  file_put_contents("debug.txt", "Error: JSON inválido o incompleto\n", FILE_APPEND);
  echo json_encode(["success" => false, "error" => "No se recibió JSON válido"]);
  exit;
}

// Escapa los datos
$titulo = $conn->real_escape_string($data->titulo);
$fecha = $conn->real_escape_string($data->fecha);
$hora_inicio = $conn->real_escape_string($data->hora_inicio);
$hora_cierre = $conn->real_escape_string($data->hora_cierre);
$recordatorio = $conn->real_escape_string($data->recordatorio);
$descripcion = $conn->real_escape_string($data->descripcion);
$categoria = $conn->real_escape_string($data->categoria);

// Construye la consulta
$sql = "INSERT INTO eventos (titulo, fecha, hora_inicio, hora_cierre, recordatorio, descripcion, categoria)
        VALUES ('$titulo', '$fecha', '$hora_inicio', '$hora_cierre', '$recordatorio', '$descripcion', '$categoria')";

// Guarda la consulta en el log
file_put_contents("debug.txt", "Consulta SQL: $sql\n", FILE_APPEND);

// Ejecuta la consulta
if ($conn->query($sql) === TRUE) {
  file_put_contents("debug.txt", "Inserción exitosa\n", FILE_APPEND);
  echo json_encode(["success" => true]);
} else {
  file_put_contents("debug.txt", "Error MySQL: " . $conn->error . "\n", FILE_APPEND);
  echo json_encode(["success" => false, "error" => $conn->error]);
}
?>
