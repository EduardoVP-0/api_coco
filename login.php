<?php
include 'db.php';

header('Content-Type: application/json');

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuario WHERE email = '$email' AND password = '$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Usuario o contraseña incorrectos"]);
}
?>
