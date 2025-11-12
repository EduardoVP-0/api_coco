<?php
include 'db.php';

// Obtener el cuerpo del JSON
$data = json_decode(file_get_contents("php://input"), true);

// Validar que exista el ID
if (!isset($data['id'])) {
  http_response_code(400);
  echo json_encode(["error" => "Falta el ID del evento"]);
  exit;
}

// Extraer campos
$id = $data['id'];
$titulo = $data['titulo'];
$fecha = $data['fecha'];
$hora_inicio = $data['hora_inicio'];
$hora_cierre = $data['hora_cierre'];
$recordatorio = $data['recordatorio'];
$descripcion = $data['descripcion'];
$categoria = $data['categoria'];

// Actualizar en la base de datos
$sql = "UPDATE eventos SET 
          titulo = '$titulo',
          fecha = '$fecha',
          hora_inicio = '$hora_inicio',
          hora_cierre = '$hora_cierre',
          recordatorio = '$recordatorio',
          descripcion = '$descripcion',
          categoria = '$categoria'
        WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
  echo json_encode(["success" => true]);
} else {
  http_response_code(500);
  echo json_encode(["error" => $conn->error]);
}
?>
