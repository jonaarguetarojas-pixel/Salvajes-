<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_completo = trim($_POST['nombre_completo']);
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $contraseña = trim($_POST['contraseña']);
    $tipo_usuario = $_POST['tipo_usuario'];
    $nivel = intval($_POST['nivel']);

    $sql = "INSERT INTO usuarios (nombre_completo, nombre_usuario, contraseña, tipo_usuario, nivel)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssi", $nombre_completo, $nombre_usuario, $contraseña, $tipo_usuario, $nivel);
        if ($stmt->execute()) {
            $mensaje = "Usuario registrado con éxito.";
        } else {
            $error = "Error al registrar el usuario: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error al preparar la consulta: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Asegúrate de incluir esto -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #80EE98, #213A58); /* Nueva paleta */
            margin: 0;
            padding: 0;
            color: #2E4156;
        }
        header {
            background-color: #15919B; /* Color principal */
            color: #FFFFFF;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            font-size: 1.5rem;
            margin: 0;
        }
        header h1 i {
    margin-right: 80px; /* Espacio entre el ícono y el texto */
}

        .menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 50px;
            height: 100%;
            background-color: #15919B; /* Color principal */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .menu button {
            background-color: #15919B;
            border: none;
            color: white;
            width: 100%;
            height: 50px;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .menu button:hover {
            background-color: #0C6478;
        }
        .dropdown {
            display: none;
            flex-direction: column;
            background-color: #15919B;
            width: 200px;
            position: absolute;
            left: 50px;
            top: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            padding-top: 10px;
        }
        .dropdown a {
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            transition: background-color 0.3s ease;
        }
        .dropdown a:hover {
            background-color: #0C6478;
        }
        .menu:hover .dropdown {
            display: flex;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #09D1C7; /* Fondo del formulario */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #213A58;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            color: #213A58;
            margin-bottom: 5px;
        }
        input, select {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background-color: #213A58;
            color: white;
            padding: 10px;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0C6478;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        footer {
            text-align: center;
            margin-top: 250px;
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
        <a href="progreso.php">Progreso</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</div>
<header>
    <h1><i class="fas fa-user-plus"></i> Registro de Usuario</h1>
</header>
<div class="container">
    <?php if (isset($mensaje)) echo "<p class='message success'>$mensaje</p>"; ?>
    <?php if (isset($error)) echo "<p class='message error'>$error</p>"; ?>
    <form method="post" action="">
        <label for="nombre_completo">Nombre Completo:</label>
        <input type="text" id="nombre_completo" name="nombre_completo" required>
        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required>
        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" required>
        <label for="tipo_usuario">Tipo de Usuario:</label>
        <select id="tipo_usuario" name="tipo_usuario" required>
            <option value="atleta">Atleta</option>
            <option value="entrenador">Entrenador</option>
        </select>
        <label for="nivel">Nivel:</label>
        <input type="number" id="nivel" name="nivel" min="1" max="10" required>
        <button type="submit">Registrar</button>
    </form>
</div>
<footer>
    © 2024 Salvajes Outdoors. 
</footer>
</body>
</html>
