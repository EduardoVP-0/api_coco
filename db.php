<?php
$host = "localhost";
$port = 3307;
$user = "root";
$pass = "root"; // o "root" si estás seguro
$db = "coco_fiscal";

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}
?>
