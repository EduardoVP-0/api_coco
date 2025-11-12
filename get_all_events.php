<?php
include 'db.php';

$sql = "SELECT * FROM eventos";
$result = $conn->query($sql);

$eventos = [];
while ($row = $result->fetch_assoc()) {
    $row['fecha'] = date('Y-m-d', strtotime($row['fecha']));
$eventos[] = $row;

  $eventos[] = $row;
}

echo json_encode($eventos);
?>
