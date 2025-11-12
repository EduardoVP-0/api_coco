<?php
include "db.php";
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $tarea_id = $_GET['tarea_id'];
        $sql = "SELECT * FROM subtareas WHERE tarea_id = $tarea_id";
        $result = $conn->query($sql);
        $subtareas = [];
        while ($row = $result->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['completado'] = (int)$row['completado'];
            $subtareas[] = $row;
        }
        echo json_encode($subtareas);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $tarea_id = $data['tarea_id'];
        $titulo = $data['titulo'];

        $sql = "INSERT INTO subtareas (tarea_id, titulo) VALUES ($tarea_id, '$titulo')";
        $conn->query($sql);
        echo json_encode(["success" => true, "id" => $conn->insert_id]);
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $_PUT);
        $id = $_PUT['id'];
        $completado = $_PUT['completado'];

        $sql = "UPDATE subtareas SET completado = $completado WHERE id = $id";
        $conn->query($sql);
        echo json_encode(["success" => true]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $sql = "DELETE FROM subtareas WHERE id = $id";
        $conn->query($sql);
        echo json_encode(["success" => true]);
        break;
}
