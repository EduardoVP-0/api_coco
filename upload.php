<?php
header('Content-Type: application/json');

$target_dir = "uploads/";
if (!file_exists($target_dir)) {
  mkdir($target_dir, 0777, true);
}

if (isset($_FILES["file"])) {
  $filename = basename($_FILES["file"]["name"]);
  $target_file = $target_dir . time() . "_" . $filename;

  if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    echo json_encode(["success" => true, "path" => $target_file]);
  } else {
    echo json_encode(["success" => false, "error" => "Error al subir el archivo"]);
  }
} else {
  echo json_encode(["success" => false, "error" => "No se recibió archivo"]);
}
?>
