<?php
include "conexion.php";
session_start();

// Verificar si el usuario es entrenador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Obtener lista de atletas
$sql = "SELECT id, nombre_completo FROM usuarios WHERE tipo_usuario = 'atleta'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Atletas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #80EE98, #213A58); /* Nueva paleta */
            color: #2E4156;
            flex-direction: column; /* Apila el contenido en columnas */
            height: 100%; /* Asegura que ocupen toda la altura del viewport */
        }

        header {
            text-align: center;
            padding: 20px 0;
            background-color: #15919B; /* Color principal */
            color: #FFFFFF; /* Texto blanco */
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
            text-transform: uppercase;
        }

        /* MENU DESPLEGABLE EN EL LADO IZQUIERDO */
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
        }/*TERMINA MENU*/

        .container {
            flex: 1;
        max-width: 1000px;
        margin: 0 auto;
        background: #09D1C7; /* Color secundario */
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        overflow: hidden; /* Evita que los elementos sobresalgan */
        }


        .athlete-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .athlete-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin: 0 auto 10px auto; /* Solo margen inferior y centrado */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        width: calc(100% - 20px); /* Reducir el ancho ligeramente para evitar bordes que sobresalgan */
        box-sizing: border-box; /* Asegura que el padding y border se incluyan en el ancho */
        }



        .athlete-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .athlete-item img {
            width: 40px;
            height: 40px;
            margin-right: 15px;
        }

        .athlete-item a {
            color: white;
            text-decoration: none;
            background: #213A58; /* Botón oscuro */
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .athlete-item a:hover {
            background: #0C6478; /* Botón intermedio */
        }

        .generate-report {
            display: block;
            text-align: center;
            background: #28a745;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2rem;
            margin: 20px auto 0 auto;
            max-width: 300px;
        }

        .generate-report:hover {
            background: #1e7e34;
        }

        footer {
            text-align: center;
            margin-top: 460px;
            padding: 10px 0;
            background: #15919B; /* Color principal */
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
        <a href="competencias.php">Competencias</a>
        <a href="status.php">Status</a>
        <a href="registro.php">Registro</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</div>
<header>
    <h1>Atletas</h1>
</header>
<br><br>
<div class="container">
    <ul class="athlete-list">
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="athlete-item">
                <div>
                    <img src="https://cdn-icons-png.flaticon.com/512/616/616490.png" alt="Atleta">
                    <?php echo $row['nombre_completo']; ?>
                </div>
                <a href="editar_plan.php?id=<?php echo $row['id']; ?>">Editar Plan</a>
            </li>
        <?php endwhile; ?>
    </ul>
<!-- Botón para generar el informe PDF -->
<a href="generar_informe.php" class="generate-report">Generar Informe PDF</a>
</div>
<footer>
    © 2024 Salvajes Outdoors. 
</footer>
</body>
</html>
