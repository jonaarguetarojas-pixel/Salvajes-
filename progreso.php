<?php
include "conexion.php";
session_start();

// Verificar si el usuario es entrenador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Obtener el ID del atleta desde la URL
$id_atleta = intval($_GET['id']);

// Obtener el nivel del atleta
$sql_nivel = "SELECT nivel FROM usuarios WHERE id = ?";
$stmt_nivel = $conn->prepare($sql_nivel);

if ($stmt_nivel) {
    $stmt_nivel->bind_param("i", $id_atleta);
    $stmt_nivel->execute();
    $stmt_nivel->bind_result($nivel);
    $stmt_nivel->fetch();
    $stmt_nivel->close();
} else {
    die("Error al preparar la consulta para el nivel del atleta: " . $conn->error);
}

// Asegurarse de que el nivel es válido
if ($nivel < 1 || $nivel > 5) {
    die("El nivel del atleta no es válido.");
}

// Obtener los ritmos según el nivel
$sql_ritmos = "SELECT distancia, ritmo FROM ritmos WHERE nivel = ?";
$stmt_ritmos = $conn->prepare($sql_ritmos);

$ritmos = array(); // Array inicializado para almacenar los ritmos

if ($stmt_ritmos) {
    $stmt_ritmos->bind_param("i", $nivel);
    $stmt_ritmos->execute();
    $stmt_ritmos->bind_result($distancia, $ritmo);

    // Recorrer los resultados y llenar el array
    while ($stmt_ritmos->fetch()) {
        $ritmos[] = array(
            'distancia' => $distancia,
            'ritmo' => $ritmo,
        );
    }

    $stmt_ritmos->close();
} else {
    die("Error al preparar la consulta para los ritmos: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progreso del Atleta</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #80EE98, #213A58);
            color: #2E4156;
        }

        header {
            text-align: center;
            padding: 20px 0;
            background-color: #15919B;
            color: #FFFFFF;
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
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
            max-width: 900px;
            margin: 40px auto;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background: #15919B;
            color: #FFFFFF;
        }

        .form-container {
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 4px 6px 10px rgba(0, 0, 0, 0.5);
        }

        .form-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .form-group label {
            flex: 0 0 120px;
            font-weight: bold;
            margin-right: 10px;
        }

        .form-group input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background: #0056b3;
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
<div class="menu">
    <button><i class="fas fa-bars"></i></button>
    <div class="dropdown">
        <a href="dashboard_entrenador.php">Inicio</a>
        <a href="atletas.php">Atletas</a>
        <a href="competencias.php">Competencias</a>
        <a href="status.php">Status</a>
        <a href="registro.php">Registro</a>
    </div>
</div>
<header>
    <h1>Progreso del Atleta</h1>
</header>
<div class="container">
    <p>Nivel del atleta: <?php echo htmlspecialchars($nivel); ?></p>
    <table>
        <thead>
            <tr>
                <th>Distancia</th>
                <th>Ritmo (min/km)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (is_array($ritmos) && count($ritmos) > 0): ?>
                <?php foreach ($ritmos as $ritmo): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ritmo['distancia']); ?></td>
                    <td><?php echo htmlspecialchars($ritmo['ritmo']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No hay ritmos registrados para este nivel.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="form-container">
        <h2>Agregar nuevo ritmo</h2>
        <form method="post" action="agregar_ritmo.php">
            <div class="form-group">
                <label for="distancia">Distancia:</label>
                <input type="text" name="distancia" id="distancia" required>
            </div>
            <div class="form-group">
                <label for="ritmo">Ritmo (min/km):</label>
                <input type="text" name="ritmo" id="ritmo" required>
            </div>
            <input type="hidden" name="nivel" value="<?php echo htmlspecialchars($nivel); ?>">
            <button type="submit">Agregar</button>
        </form>
    </div>
</div>
<footer>
    © 2024 Salvajes Outdoors. 
</footer>
</body>
</html>
