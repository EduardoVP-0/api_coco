<?php
include 'db.php';

$fecha = $_GET['fecha'];

$sql = "SELECT * FROM eventos WHERE fecha = '$fecha'";
$result = $conn->query($sql);

$eventos = [];
while ($row = $result->fetch_assoc()) {
  $eventos[] = $row;
}

echo json_encode($eventos);
?>
