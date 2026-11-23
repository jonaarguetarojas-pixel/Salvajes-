<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'atleta') {
    header("Location: login.php");
    exit();
}

// Obtener el nivel del atleta desde la sesión
$nivel_atleta = $_SESSION['nivel'];

// Obtener los ritmos correspondientes al nivel del atleta
$sql = "SELECT distancia, ritmo FROM ritmos WHERE nivel = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $nivel_atleta);
    $stmt->execute();
    $stmt->bind_result($distancia, $ritmo);

    // Guardar los resultados en un array
    $ritmos = array();
    while ($stmt->fetch()) {
        $ritmos[] = array('distancia' => $distancia, 'ritmo' => $ritmo);
    }
    $stmt->close();
} else {
    die("Error al preparar la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ritmos de Competencia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #80EE98, #213A58);
            height: 100vh;
            display: flex;
            flex-direction: column;
            color: white;
        }
        header {
    background-color: #15919B;
    color: #FFFFFF;
    padding: 10px 20px;
    display: flex;
    justify-content: center; /* Esto centra el contenido horizontalmente */
    align-items: center;
    text-align: center; /* Asegura que el texto esté centrado */
    position: relative;
}

        header h1 {
            margin: 0;
            font-size: 1.8rem;
            text-transform: uppercase;
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
        .container {
            margin: 20px auto;
            width: 80%;
            max-width: 800px;
            background-color: rgba(30, 65, 86, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin: 0;
            color: white;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid white;
        }
        table th {
            background-color: #2E4156;
            color: white;
        }
        table td {
            background-color: #AAB7B7;
            color: #1A2D42;
        }
        footer {
            text-align: center;
            margin-top: auto;
            padding: 10px 0;
            background: #15919B;
            color: white;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="menu">
    <button><i class="fas fa-bars"></i></button>
    <div class="dropdown">
        <a href="dashboard_atleta.php">Inicio</a>
        <a href="entrenamiento.php.php">Plan de Entrenamiento</a>
        <a href="carreras.php">Carreras</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</div>
<header>
    <h1>Ritmos de Entrenamiento</h1>
</header>
<div class="container">
    <h2>Mis Ritmos</h2>
    <table>
        <tr>
            <th>Distancia</th>
            <th>Ritmo (min/km)</th>
        </tr>
        <?php foreach ($ritmos as $ritmo): ?>
            <tr>
                <td><?php echo htmlspecialchars($ritmo['distancia']); ?></td>
                <td><?php echo htmlspecialchars($ritmo['ritmo']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<footer>
    © 2024 Salvajes Outdoors. 
</footer>
</body>
</html>
