<?php
// ASIGNAR_COMPETENCIA.PHP
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Obtener el ID del atleta desde la URL
$atleta_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar si el atleta existe
$sql = "SELECT nombre_completo FROM usuarios WHERE id = $atleta_id AND tipo_usuario = 'atleta'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Error: Atleta no encontrado.");
}

$atleta = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Competencia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #D4D8DD, #1A2D42);
            color: #2E4156;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #C0C8CA;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #1A2D42;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            color: #1A2D42;
        }
        input, select, button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        button {
            background: #007BFF;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007BFF;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .field-required::after {
            content: "*";
            color: red;
            margin-left: 5px;
        }
        .note {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Asignar Competencia</h1>
        <form action="guardar_competencia.php" method="POST">
            <input type="hidden" name="id_atleta" value="<?php echo $atleta_id; ?>"> <!-- Enviar el ID del atleta -->
            <div>
                <label>Atleta:</label>
                <p><?php echo htmlspecialchars($atleta['nombre_completo']); ?></p>
            </div>
            <div>
                <label for="nombre_competencia" class="field-required">Nombre de Competencia:</label>
                <input type="text" id="nombre_competencia" name="nombre_competencia" placeholder="Ejemplo: Carrera 10K" required>
            </div>
            <div>
                <label for="distancia">Distancia (km):</label>
                <input type="number" step="0.01" id="distancia" name="distancia" placeholder="Ejemplo: 10.5" min="0">
                <p class="note">Este campo es opcional.</p>
            </div>
            <div>
                <label for="ritmo_estimado">Ritmo Estimado (min/km):</label>
                <input type="text" id="ritmo_estimado" name="ritmo_estimado" placeholder="Ejemplo: 5:30">
                <p class="note">Introduce el ritmo en formato mm:ss (opcional).</p>
            </div>
            <div>
                <label for="tiempo_estimado">Tiempo Estimado (hh:mm:ss):</label>
                <input type="time" id="tiempo_estimado" name="tiempo_estimado" step="1">
                <p class="note">Introduce el tiempo en formato hh:mm:ss (opcional).</p>
            </div>
            <div>
                <label for="fecha_competencia" class="field-required">Fecha de la Competencia:</label>
                <input type="date" id="fecha_competencia" name="fecha_competencia" required>
            </div>
            <button type="submit">Guardar Competencia</button>
        </form>
        <a href="competencias.php" class="back-link">Volver</a>
    </div>
</body>
</html>
