<?php
// COMPETENCIAS.PHP PARA ENTRENADORES
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Obtener la lista de atletas
$sql_atletas = "SELECT id, nombre_completo FROM usuarios WHERE tipo_usuario = 'atleta'";
$result_atletas = $conn->query($sql_atletas);

if (!$result_atletas) {
    die("Error al consultar atletas: " . $conn->error);
}

// Obtener competencias
$sql_competencias = "SELECT c.id, u.nombre_completo, c.nombre_competencia, c.distancia, c.ritmo_estimado, 
                     c.tiempo_estimado, c.fecha_competencia, c.estado, c.tiempo_realizado
                     FROM competencias c
                     INNER JOIN usuarios u ON c.id_atleta = u.id
                     ORDER BY c.fecha_competencia ASC";
$result_competencias = $conn->query($sql_competencias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competencias</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #80EE98, #213A58); /* Nueva paleta */
            color: #2E4156;
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 50px;
            height: 100%;
            background-color: #15919B;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        nav input[type="checkbox"] {
            display: none;
        }

        nav label {
            display: block;
            cursor: pointer;
            padding: 20px;
            font-size: 1.5rem;
            color: white;
        }

        nav .menu-items {
            display: none;
            flex-direction: column;
            background-color: #15919B;
            padding-top: 10px;
            width: 200px;
            position: absolute;
            left: 50px;
            top: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        }

        nav .menu-items a {
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            transition: background-color 0.3s ease;
        }

        nav .menu-items a:hover {
            background-color: #0C6478;
        }

        nav input[type="checkbox"]:checked ~ .menu-items {
            display: flex;
        }

        header {
            text-align: center;
            padding: 20px 0;
            background-color: #15919B;
            color: #FFFFFF;
            margin-left: 50px;
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
        }

        .table-container {
            max-width: 1000px;
            margin: 40px auto;
            background: #09D1C7;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background: #213A58;
            color: #D4D8DD;
        }

        .form-container {
    margin-top: 20px;
    background: #15919B; /* Color secundario */
    padding: 20px;
    border-radius: 20px; /* Bordes redondeados */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra */
    color: #213A58; /* Texto oscuro */
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #213A58; /* Color del texto */
}

select, input, button {
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    width: 100%;
    margin-bottom: 15px;
    box-sizing: border-box;
    font-size: 1rem;
}

button {
    background: #213A58; /* Botón oscuro */
    color: white;
    cursor: pointer;
    border: none;
    transition: background 0.3s ease;
}

button:hover {
    background: #0C6478; /* Botón intermedio */
}

input::placeholder, select::placeholder {
    color: #666; /* Color del texto de marcador */
}


        footer {
            text-align: center;
            margin-top: 40px;
            padding: 10px 0;
            background: #15919B;
            color: #FFFFFF;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<nav>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="fas fa-bars"></label>
    <div class="menu-items">
        <a href="dashboard_entrenador.php">Inicio</a>
        <a href="atletas.php">Atletas</a>
        <a href="status.php">Status</a>
        <a href="registro.php">Registro</a>
    </div>
</nav>
<header>
    <h1>Competencias</h1>
</header>
<div class="table-container">
    <h2>Lista de Competencias</h2>
    <table>
        <thead>
            <tr>
                <th>Atleta</th>
                <th>Nombre de Competencia</th>
                <th>Distancia (km)</th>
                <th>Ritmo Estimado (min/km)</th>
                <th>Tiempo Estimado</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Tiempo Realizado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_competencias->num_rows > 0): ?>
                <?php while ($row = $result_competencias->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_competencia']); ?></td>
                        <td><?php echo htmlspecialchars($row['distancia']); ?></td>
                        <td><?php echo htmlspecialchars($row['ritmo_estimado']); ?></td>
                        <td><?php echo htmlspecialchars($row['tiempo_estimado']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_competencia']); ?></td>
                        <td><?php echo htmlspecialchars($row['estado']); ?></td>
                        <td><?php echo htmlspecialchars($row['tiempo_realizado']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No hay competencias registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="table-container">
    <h2>Agregar Competencia</h2>
    <form action="guardar_competencia.php" method="POST" class="form-container">
        <label for="id_atleta">Seleccionar Atleta:</label>
        <select id="id_atleta" name="id_atleta" required>
            <option value="">-- Selecciona un atleta --</option>
            <?php while ($atleta = $result_atletas->fetch_assoc()): ?>
                <option value="<?php echo $atleta['id']; ?>">
                    <?php echo htmlspecialchars($atleta['nombre_completo']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="nombre_competencia">Nombre de Competencia:</label>
        <input type="text" id="nombre_competencia" name="nombre_competencia" placeholder="Ejemplo: Carrera 10K" required>
        <label for="distancia">Distancia (km):</label>
        <input type="number" id="distancia" name="distancia" step="0.01" placeholder="Ejemplo: 10.5">
        <label for="ritmo_estimado">Ritmo Estimado (min/km):</label>
        <input type="text" id="ritmo_estimado" name="ritmo_estimado" placeholder="Ejemplo: 5:30">
        <label for="tiempo_estimado">Tiempo Estimado (hh:mm:ss):</label>
        <input type="time" id="tiempo_estimado" name="tiempo_estimado" step="1">
        <label for="fecha_competencia">Fecha de la Competencia:</label>
        <input type="date" id="fecha_competencia" name="fecha_competencia" required>
        <button type="submit">Guardar Competencia</button>
    </form>
</div>
<footer>
    © 2024 Salvajes Outdoors. 
</footer>
</body>
</html>
