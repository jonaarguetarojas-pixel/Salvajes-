<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'atleta') {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$id_atleta = $_SESSION['usuario'];
$date = $_POST['date'];
$status = $_POST['status'];

$sql = "UPDATE planes_entrenamiento SET estado = ? 
        WHERE id_atleta = (SELECT id FROM usuarios WHERE nombre_usuario = ?) AND fecha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $status, $id_atleta, $date);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>
