<?php
include "conexion.php";
session_start();

// Validar que el usuario esté autenticado como atleta
if (!isset($_SESSION['id_atleta']) || $_SESSION['tipo'] != 'atleta') {
    die('<p>Error: No tienes permiso para realizar esta acción. Por favor, inicia sesión como atleta.</p><a href="login.php">Iniciar Sesión</a>');
}

// Verificar si los parámetros necesarios están presentes
if (!isset($_POST['id_competencia']) || !isset($_POST['estado'])) {
    die('<p>Error: Parámetros inválidos. Por favor, verifica e inténtalo de nuevo.</p><a href="dashboard_atleta.php">Volver</a>');
}

// Obtener y limpiar los valores de los parámetros
$id_competencia = (int)$_POST['id_competencia'];
$estado = $_POST['estado'];
$tiempo_realizado = isset($_POST['tiempo_realizado']) ? $_POST['tiempo_realizado'] : null;

// Validar los estados permitidos
$estados_validos = array('Completado', 'Cancelado'); // Usar array() para compatibilidad con PHP antiguo
if (!in_array($estado, $estados_validos)) {
    die('<p>Error: Estado inválido. Solo se permiten "Completado" o "Cancelado".</p><a href="dashboard_atleta.php">Volver</a>');
}

// Verificar que la competencia pertenece al atleta actual
$sql_verificar = "SELECT id FROM competencias WHERE id = ? AND id_atleta = ?";
$stmt_verificar = $conn->prepare($sql_verificar);
if (!$stmt_verificar) {
    die('<p>Error al preparar la consulta de verificación: ' . $conn->error . '</p>');
}

$stmt_verificar->bind_param("ii", $id_competencia, $_SESSION['id_atleta']);
$stmt_verificar->execute();
$stmt_verificar->store_result();

if ($stmt_verificar->num_rows === 0) {
    die('<p>Error: No tienes permiso para actualizar esta competencia.</p><a href="dashboard_atleta.php">Volver</a>');
}
$stmt_verificar->close();

// Actualizar el estado de la competencia en la base de datos
$sql_actualizar = "UPDATE competencias SET estado = ?, tiempo_realizado = ? WHERE id = ?";
$stmt_actualizar = $conn->prepare($sql_actualizar);
if (!$stmt_actualizar) {
    die('<p>Error al preparar la consulta de actualización: ' . $conn->error . '</p>');
}

$stmt_actualizar->bind_param("ssi", $estado, $tiempo_realizado, $id_competencia);
if ($stmt_actualizar->execute()) {
    // Redirigir al panel del atleta después de la actualización
    header("Location: dashboard_atleta.php");
    exit();
} else {
    die('<p>Error al actualizar la competencia: ' . $stmt_actualizar->error . '</p><a href="dashboard_atleta.php">Volver</a>');
}
$stmt_actualizar->close();
?>
