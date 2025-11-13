<?php
include "db.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$titulo = $data['titulo'];
$categoria = $data['categoria'];
$fecha = $data['fecha_vencimiento'];
$hora = $data['hora'];
$recordatorio = $data['recordatorio'];
$nota = $data['nota'];
$adjunto = $data['adjunto_path'];

$sql = "UPDATE tareas SET 
    titulo = '$titulo',
    categoria = '$categoria',
    fecha_vencimiento = '$fecha',
    hora = '$hora',
    recordatorio = '$recordatorio',
    nota = '$nota',
    adjunto_path = '$adjunto'
    WHERE id = $id";

$conn->query($sql);
echo json_encode(["success" => true]);
