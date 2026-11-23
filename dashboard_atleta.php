<?php
session_start();

// Verificar si el usuario es un atleta
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'atleta') {
    header("Location: login.php");
    exit();
}

// Evitar caché para prevenir acceso al panel sin iniciar sesión nuevamente
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Obtener el nombre completo del atleta desde la sesión
$nombre_completo = $_SESSION['nombre_completo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Atleta</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #80EE98, #213A58); /* Paleta nueva */
            color: #2E4156;
        }

        header {
            text-align: center;
            padding: 20px 0;
            background-color: #15919B; /* Nuevo color */
            color: #FFFFFF; /* Texto blanco */
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
            text-transform: uppercase;
        }

        header h2 {
            font-size: 1.5rem;
            margin: 5px 0;
            font-weight: lighter;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* Dos columnas */
            gap: 30px; /* Espaciado entre tarjetas */
            margin: 40px auto;
            max-width: 900px;
            padding: 0 20px;
        }

        /* Centrar la tarjeta de la última fila */
        .container > div:nth-child(3) {
            grid-column: span 2; /* Ocupa toda la fila */
            justify-self: center; /* Centra la tarjeta */
        }

        .card {
            background: #09D1C7; /* Nuevo color */
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            padding: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 80%; 
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .card .icon {
            font-size: 5rem;
            color: #213A58; /* Color oscuro */
            margin-bottom: 15px;
        }

        .card h2 {
            font-size: 2rem;
            margin: 10px 0;
            color: #213A58; /* Color oscuro */
        }

        .card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            font-size: 1rem;
            text-decoration: none;
            color: white;
            background-color: #213A58; /* Color oscuro */
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .card a:hover {
            background-color: #0C6478; /* Color intermedio */
        }

        footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.9rem;
            color: #666;
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
        <h1>Bienvenido, <?php echo htmlspecialchars($nombre_completo); ?></h1>
        <h2>Panel de Control</h2>
    </header>
    <div class="container">
        <!-- Card Plan de Entrenamiento -->
        <div class="card">
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <h2>Plan de Entrenamiento</h2>
            <a href="entrenamiento.php">Ir al Calendario</a>
        </div>
        <!-- Card Próximas Carreras -->
        <div class="card">
            <div class="icon"><i class="fas fa-flag"></i></div>
            <h2>Próximas Carreras</h2>
            <a href="carreras.php">Carreras</a>
        </div>
        <!-- Card Ritmos -->
        <div class="card">
            <div class="icon"><i class="fas fa-running"></i></div>
            <h2>Ritmos</h2>
            <a href="ritmos.php">Training Paces</a>
        </div>
        <br><br><br><br><br><br><br><br><br><br>
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
