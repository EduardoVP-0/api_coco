<?php
include "db.php";
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $estado = $_GET['completado'] ?? '0';
        $categoria = $_GET['categoria'] ?? '';
        $hoy = date('Y-m-d');

        $sql = "SELECT * FROM tareas WHERE completado = $estado";
        if ($estado == '1') {
            $sql .= " AND fecha_completado = '$hoy'";
        }
        if ($categoria != '') {
            $sql .= " AND categoria = '$categoria'";
        }

        $result = $conn->query($sql);
        $tareas = [];
        while ($row = $result->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['completado'] = (int)$row['completado'];
            $tareas[] = $row;
        }
        echo json_encode($tareas);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $titulo = $data['titulo'];
        $categoria = $data['categoria'];
        $fecha = $data['fecha_vencimiento'];
        $hora = $data['hora'];
        $recordatorio = $data['recordatorio'];
        $nota = $data['nota'];
        $adjunto = $data['adjunto_path'];

        $sql = "INSERT INTO tareas (titulo, categoria, fecha_vencimiento, hora, recordatorio, nota, adjunto_path)
            VALUES ('$titulo', '$categoria', '$fecha', '$hora', '$recordatorio', '$nota', '$adjunto')";
        $conn->query($sql);
        echo json_encode(["success" => true, "id" => $conn->insert_id]);
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $_PUT);
        $id = $_PUT['id'];
        $completado = $_PUT['completado'];
        $fecha_completado = $completado == '1' ? date('Y-m-d') : 'NULL';

        $sql = "UPDATE tareas SET completado = $completado, fecha_completado = $fecha_completado WHERE id = $id";
        $conn->query($sql);
        echo json_encode(["success" => true]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $sql = "DELETE FROM tareas WHERE id = $id";
        $conn->query($sql);
        echo json_encode(["success" => true]);
        break;
}
