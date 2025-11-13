<?php
require 'db.php';

$inicio = $_GET['fecha_inicio'];
$fin = $_GET['fecha_fin'];

$resumen = [
  'completadas' => 0,
  'pendientes' => 0,
  'por_dia' => [],
  'categorias' => [],
];

// Completadas
$sql1 = "SELECT COUNT(*) as total FROM tareas WHERE completado = 1 AND fecha_completado BETWEEN '$inicio' AND '$fin'";
$res1 = mysqli_query($conn, $sql1);
$resumen['completadas'] = mysqli_fetch_assoc($res1)['total'];

// Pendientes
$sql2 = "SELECT COUNT(*) as total FROM tareas WHERE completado = 0";
$res2 = mysqli_query($conn, $sql2);
$resumen['pendientes'] = mysqli_fetch_assoc($res2)['total'];

// Por día (domingo = 0, sábado = 6)
for ($i = 0; $i < 7; $i++) {
  $sql3 = "SELECT COUNT(*) as total FROM tareas WHERE completado = 1 AND WEEKDAY(fecha_completado) = $i AND fecha_completado BETWEEN '$inicio' AND '$fin'";
  $res3 = mysqli_query($conn, $sql3);
  $resumen['por_dia'][(string)$i] = mysqli_fetch_assoc($res3)['total'];
}

// Categorías del mes
$sql4 = "SELECT categoria, COUNT(*) as total FROM tareas WHERE completado = 1 AND MONTH(fecha_completado) = MONTH('$inicio') GROUP BY categoria";
$res4 = mysqli_query($conn, $sql4);
while ($row = mysqli_fetch_assoc($res4)) {
  $resumen['categorias'][] = $row;
}

echo json_encode($resumen);
?>
