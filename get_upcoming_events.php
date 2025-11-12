<?php
include 'db.php';

// Establecer la zona horaria si es necesario
date_default_timezone_set('America/Mexico_City');

// Obtener la fecha actual
$fechaActual = date('Y-m-d');

// Consulta: eventos a partir de hoy, ordenados por fecha y hora
$sql = "SELECT * FROM eventos 
        WHERE fecha >= '$fechaActual' 
        ORDER BY fecha ASC, hora_inicio ASC 
        LIMIT 3";

$result = $conn->query($sql);

$eventos = [];
while ($row = $result->fetch_assoc()) {
  $eventos[] = $row;
}

echo json_encode($eventos);
?>
