<?php
session_start();
include "conexion.php";

// Validar que el usuario está autenticado como atleta
if (!isset($_SESSION['id_atleta']) || $_SESSION['tipo'] != 'atleta') {
    die('<p>Error: No se encontró el ID del atleta en la sesión. Por favor, inicia sesión como atleta.</p><a href="dashboard_atleta.php">Volver al Panel</a>');
}

$id_atleta = $_SESSION['id_atleta'];

// Consultar las competencias asignadas al atleta
$sql = "SELECT id, nombre_competencia, distancia, ritmo_estimado, tiempo_estimado, fecha_competencia, estado 
        FROM competencias WHERE id_atleta = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('<p>Error al preparar la consulta de competencias: ' . $conn->error . '</p>');
}

$stmt->bind_param("i", $id_atleta);
$stmt->execute();
$stmt->bind_result($id_competencia, $nombre_competencia, $distancia, $ritmo_estimado, $tiempo_estimado, $fecha_competencia, $estado);

$competencias = "";
while ($stmt->fetch()) {
    $disabled = ($estado === 'Completado' || $estado === 'Cancelado') ? 'disabled' : '';
    $competencias .= "<tr>
        <td>" . htmlspecialchars($nombre_competencia) . "</td>
        <td>" . htmlspecialchars($distancia) . " km</td>
        <td>" . htmlspecialchars($ritmo_estimado) . " min/km</td>
        <td>" . htmlspecialchars($tiempo_estimado) . "</td>
        <td>" . htmlspecialchars($fecha_competencia) . "</td>
        <td><span class='" . ($estado === 'Completado' ? 'completado' : ($estado === 'Cancelado' ? 'cancelado' : 'pendiente')) . "'>" . htmlspecialchars($estado) . "</span></td>
        <td>
            <form action='actualizar_estado_compe.php' method='POST' style='display:inline-block;'>
                <input type='hidden' name='id_competencia' value='" . htmlspecialchars($id_competencia) . "'>
                <input type='hidden' name='estado' value='Completado'>
                <input type='text' name='tiempo_realizado' placeholder='hh:mm:ss' pattern='[0-9]{2}:[0-9]{2}:[0-9]{2}' title='Formato hh:mm:ss' $disabled required>
                <button type='submit' class='btn-completado' $disabled>Completado</button>
            </form>
            <form action='actualizar_estado_compe.php' method='POST' style='display:inline-block;'>
                <input type='hidden' name='id_competencia' value='" . htmlspecialchars($id_competencia) . "'>
                <input type='hidden' name='estado' value='Cancelado'>
                <button type='submit' class='btn-cancelado' $disabled>Cancelado</button>
            </form>
        </td>
    </tr>";
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Carreras</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Diseño personalizado */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #80EE98, #213A58);
        }
        header {
            background-color: #15919B;
            color: #FFFFFF;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 1.8rem;
            text-transform: uppercase;
            margin-left: 750px;
        }
        .menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 60px;
            height: 100%;
            background-color: #15919B;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .menu button {
            background-color: #15919B;
            border: none;
            color: white;
            font-size: 1.5rem;
            padding: 15px;
            cursor: pointer;
            margin-top: 10px;
        }
        .menu button:hover {
            background-color: #0C6478;
        }
        .dropdown {
            display: none;
            position: absolute;
            left: 60px;
            top: 0;
            background-color: #15919B;
            border-radius: 0 10px 10px 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            padding-top: 10px;
            z-index: 1;
        }
        .menu:hover .dropdown {
            display: block;
        }
        .dropdown a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .dropdown a:hover {
            background-color: #0C6478;
        }
        .table-container {
            max-width: 900px;
            margin: 40px auto;
            background: #C0C8CA;
            border-radius: 10px;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #15919B;
            color: #FFFFFF;
        }
        .completado {
            color: green;
            font-weight: bold;
        }
        .cancelado {
            color: red;
            font-weight: bold;
        }
        .btn-completado {
            color: white;
            background: green;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .btn-cancelado {
            color: white;
            background: red;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .btn-completado:disabled, .btn-cancelado:disabled {
            background: gray;
            cursor: not-allowed;
        }
        footer {
            text-align: center;
            padding: 10px 0;
            background: #15919B;
            color: #FFFFFF;
            font-size: 0.9rem;
            margin-top: 450px;
        }
    </style>
</head>
<body>
    <div class="menu">
        <button><i class="fas fa-bars"></i></button>
        <div class="dropdown">
            <a href="dashboard_atleta.php">Inicio</a>
            <a href="entrenamiento.php">Plan de Entrenamiento</a>
            <a href="ritmos.php">Ritmos</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>
    <header>
        <h1>Mis Carreras</h1>
    </header>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre de Competencia</th>
                    <th>Distancia</th>
                    <th>Ritmo Estimado</th>
                    <th>Tiempo Estimado</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $competencias ?: "<tr><td colspan='7'>No tienes competencias asignadas.</td></tr>"; ?>
            </tbody>
        </table>
    </div>
    <footer>
        © 2024 Salvajes Outdoors.
    </footer>
</body>
</html>
