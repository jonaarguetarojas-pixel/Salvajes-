<?php
include "conexion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_atleta = $_POST['id_atleta'];
    $fecha = $_POST['fecha'];
    $tipo_actividad = $_POST['tipo_actividad'];

    if ($tipo_actividad == "Easy Run") {
        $tiempo = $_POST['easy_run_tiempo'];
        $distancia = $_POST['easy_run_distancia'];
        $ritmo = $_POST['easy_run_ritmo'];
        $actividad = "Easy Run - Tiempo: $tiempo, Distancia: $distancia km, Ritmo: $ritmo min/km";
    } elseif ($tipo_actividad == "Entrenamiento de Fuerza") {
        $enlace = $_POST['fuerza_link'];
        $actividad = "Enlace: $enlace";
    } elseif ($tipo_actividad == "Intervalos") {
        $distancia = $_POST['intervalos_distancia'];
        $ritmo = $_POST['intervalos_ritmo'];
        $repeticiones = $_POST['intervalos_repeticiones'];
        $actividad = "Intervalos - Distancia: $distancia km, Ritmo: $ritmo min/km, Repeticiones: $repeticiones";
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO planes_entrenamiento (id_atleta, fecha, actividad, estado) 
            VALUES (?, ?, ?, 'pendiente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $id_atleta, $fecha, $actividad);

    if ($stmt->execute()) {
        header("Location: editar_plan.php?id=$id_atleta");
    } else {
        echo "Error al guardar la actividad: " . $conn->error;
    }
}
?>
