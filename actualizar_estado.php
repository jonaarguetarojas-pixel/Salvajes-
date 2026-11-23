<?php
include "conexion.php";
session_start();

// Validar si el usuario tiene acceso y si los parámetros están presentes
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'atleta') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id_competencia']) || !isset($_GET['estado'])) {
    die("Parámetros inválidos.");
}

$id_competencia = (int)$_GET['id_competencia'];
$estado = $_GET['estado'];

// Validar el estado proporcionado
$estados_validos = array('Completado', 'Cancelado'); // Usar array() en lugar de []
if (!in_array($estado, $estados_validos)) {
    die("Estado inválido.");
}

// Verificar si el ID de la actividad pertenece al atleta actual
$sql_verificar = "SELECT id FROM planes_entrenamiento WHERE id = ? AND id_atleta = (SELECT id FROM usuarios WHERE nombre_usuario = ?)";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("is", $id_competencia, $_SESSION['usuario']);
$stmt_verificar->execute();
$stmt_verificar->store_result();

if ($stmt_verificar->num_rows == 0) {
    die("Acceso no autorizado o actividad no encontrada.");
}
$stmt_verificar->close();

// Actualizar el estado de la actividad
$sql_actualizar = "UPDATE planes_entrenamiento SET estado = ? WHERE id = ?";
$stmt_actualizar = $conn->prepare($sql_actualizar);
$stmt_actualizar->bind_param("si", $estado, $id_competencia);

if ($stmt_actualizar->execute()) {
    header("Location: entrenamiento.php?year=" . date('Y') . "&month=" . date('m'));
    exit();
} else {
    die("Error al actualizar el estado.");
}
?>
