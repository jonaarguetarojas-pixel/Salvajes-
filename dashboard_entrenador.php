<?php
session_start();

// Verificar si el usuario es un entrenador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Establecer encabezados para deshabilitar el almacenamiento en caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache")
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Entrenador</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Íconos de Font Awesome -->
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #80EE98, #213A58); /* Nueva paleta */
            color: #2E4156;
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

        .container {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* Dos columnas */
            gap: 20px;
            margin: 40px auto;
            max-width: 1200px;
            padding: 20px;
        }

        .card {
            background: #09D1C7; /* Color secundario */
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            padding: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .card .icon {
            font-size: 4rem;
            color: #213A58; /* Color oscuro */
            margin-bottom: 15px;
        }

        .card h2 {
            font-size: 1.8rem;
            margin: 10px 0;
            color: #213A58; /* Color oscuro */
        }

        .card p {
            font-size: 1rem;
            color: #213A58;
            margin-bottom: 20px;
        }

        .card a {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            text-decoration: none;
            color: white;
            background-color: #213A58; /* Botón oscuro */
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .card a:hover {
            background-color: #0C6478; /* Botón intermedio */
        }

        footer {
            text-align: center;
            margin-top: 40px;
            padding: 10px 0;
            background: #15919B; /* Color principal */
            color: #FFFFFF;
            font-size: 0.9rem;
        }
        .logout-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #213A58; /* Color oscuro */
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #0C6478; /* Color intermedio */
        }

        .logout-btn i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido Entrenador</h1>
    </header>
    <div class="container">
        <!-- Card Atletas -->
        <div class="card">
            <div class="icon"><i class="fas fa-running"></i></div>
            <h2>Atletas</h2>
            <p>Planificación de entrenamientos individuales de los atletas.</p>
            <a href="atletas.php">Ir a Atletas</a>
        </div>
        <!-- Card Competencias -->
        <div class="card">
            <div class="icon"><i class="fas fa-flag-checkered"></i></div>
            <h2>Competencias</h2>
            <p>Futuras competencias de los atletas.</p>
            <a href="competencias.php">Ir a Competencias</a>
        </div>
        <!-- Card Status -->
        <div class="card">
            <div class="icon"><i class="fas fa-chart-line"></i></div>
            <h2>Status</h2>
            <p>Avances y logros de cada atleta de manera semanal.</p>
            <a href="status.php">Ir a Status</a>
        </div>
        <!-- Card Registro -->
        <div class="card">
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <h2>Registro</h2>
            <p>Registrar nuevos atletas o entrenadores en el sistema.</p>
            <a href="registro.php">Ir a Registro</a>
        </div>
        <br><br><br><br><br><br><br><br><br><br><br><br>
    </div>
    <footer>
        © 2024 Salvajes Outdoors. 
    </footer>
    <form action="logout.php" method="POST">
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Cerrar Sesión
        </button>
    </form>
</body>
</html>
