<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Recibir datos del formulario
$id_atleta = isset($_POST['id_atleta']) ? intval($_POST['id_atleta']) : null;
$nombre_competencia = isset($_POST['nombre_competencia']) ? trim($_POST['nombre_competencia']) : null;
$distancia = isset($_POST['distancia']) && $_POST['distancia'] !== '' ? floatval($_POST['distancia']) : null;
$ritmo_estimado = isset($_POST['ritmo_estimado']) && $_POST['ritmo_estimado'] !== '' ? trim($_POST['ritmo_estimado']) : null;
$tiempo_estimado = isset($_POST['tiempo_estimado']) && $_POST['tiempo_estimado'] !== '' ? trim($_POST['tiempo_estimado']) : null;
$fecha_competencia = isset($_POST['fecha_competencia']) && $_POST['fecha_competencia'] !== '' ? $_POST['fecha_competencia'] : null;
$estado = "Pendiente";

// Validar datos obligatorios
if (!$id_atleta || !$nombre_competencia || !$fecha_competencia) {
    die("Error: Todos los campos obligatorios deben ser llenados.");
}

// Preparar consulta SQL
$sql = "INSERT INTO competencias (id_atleta, nombre_competencia, distancia, ritmo_estimado, tiempo_estimado, fecha_competencia, estado) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Usar valores nulos si los campos son opcionales
$stmt->bind_param(
    "issssss",
    $id_atleta,
    $nombre_competencia,
    $distancia,
    $ritmo_estimado,
    $tiempo_estimado,
    $fecha_competencia,
    $estado
);

// Ejecutar la consulta
if ($stmt->execute()) {
    header("Location: competencias.php");
    exit();
} else {
    die("Error al guardar la competencia: " . $stmt->error);
}

$stmt->close();
?>
