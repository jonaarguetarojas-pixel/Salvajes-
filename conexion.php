<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "salvajes_outdoors";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres
if (!$conn->set_charset("utf8")) {
    die("Error al establecer el conjunto de caracteres UTF-8: " . $conn->error);
}
?>
