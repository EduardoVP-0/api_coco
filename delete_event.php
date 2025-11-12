<?php
include 'db.php';

// Validar que se recibió el ID
if (!isset($_GET['id'])) {
  http_response_code(400);
  echo json_encode(["error" => "Falta el ID del evento"]);
  exit;
}

$id = $_GET['id'];

// Eliminar el evento
$sql = "DELETE FROM eventos WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
  echo json_encode(["success" => true]);
} else {
  http_response_code(500);
  echo json_encode(["error" => $conn->error]);
}
?>
