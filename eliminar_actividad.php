<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Verificar que se haya proporcionado un ID de actividad
if (isset($_GET['id'])) {
    $id_actividad = intval($_GET['id']);

    // Consultar la fecha de la actividad para verificar si es una fecha pasada
    $sql_check_date = "SELECT fecha FROM planes_entrenamiento WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check_date);

    if ($stmt_check) {
        $stmt_check->bind_param("i", $id_actividad);
        $stmt_check->execute();
        $stmt_check->bind_result($fecha_actividad);
        $stmt_check->fetch();
        $stmt_check->close();

        // Verificar si la fecha de la actividad es anterior a la fecha actual
        $fecha_actual = date('Y-m-d');
        if ($fecha_actividad < $fecha_actual) {
            echo "No puedes eliminar actividades en días anteriores a la fecha actual.";
            exit();
        }

        // Si la fecha es válida, proceder a eliminar la actividad
        $sql = "DELETE FROM planes_entrenamiento WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id_actividad);

            if ($stmt->execute()) {
                // Redirigir de vuelta al calendario del atleta
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            } else {
                echo "Error al eliminar la actividad: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conn->error;
        }
    } else {
        echo "Error al preparar la consulta para verificar la fecha: " . $conn->error;
    }
} else {
    echo "No se proporcionó un ID de actividad válido.";
}
?>
