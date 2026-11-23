<?php
include "conexion.php";
session_start();

// Verificar que el usuario sea entrenador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Validar si se recibieron los parámetros necesarios
if (isset($_GET['id']) && isset($_GET['actividad'])) {
    $id_actividad = intval($_GET['id']); // Sanitiza el ID recibido
    $nueva_actividad = $conn->real_escape_string($_GET['actividad']);

    // Verificar si la actividad existe
    $sql_check = "SELECT fecha, id_atleta FROM planes_entrenamiento WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);

    if ($stmt_check) {
        $stmt_check->bind_param("i", $id_actividad);
        $stmt_check->execute();
        $stmt_check->bind_result($fecha_actividad, $id_atleta);
        if ($stmt_check->fetch()) {
            $fecha_actual = date('Y-m-d');
            if ($fecha_actividad < $fecha_actual) {
                $stmt_check->close();
                die("No puedes modificar actividades en días anteriores a la fecha actual.");
            }

            // Cerramos el statement anterior para evitar conflictos
            $stmt_check->close();

            // Actualizar la actividad
            $sql_update = "UPDATE planes_entrenamiento SET actividad = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);

            if ($stmt_update) {
                $stmt_update->bind_param("si", $nueva_actividad, $id_actividad);

                if ($stmt_update->execute()) {
                    // Redirigir al plan de entrenamiento del atleta
                    header("Location: editar_plan.php?id=" . $id_atleta);
                    exit();
                } else {
                    die("Error al actualizar la actividad: " . $stmt_update->error);
                }
                $stmt_update->close();
            } else {
                die("Error al preparar la consulta de actualización: " . $conn->error);
            }
        } else {
            $stmt_check->close();
            die("Actividad no encontrada o ID inválido.");
        }
    } else {
        die("Error al preparar la consulta para verificar la actividad: " . $conn->error);
    }
} else {
    die("Parámetros inválidos.");
}
?>
