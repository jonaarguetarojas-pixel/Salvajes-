<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Obtener los datos de atletas y sus niveles para la gráfica
$sql_grafica = "SELECT nombre_completo, nivel FROM usuarios WHERE tipo_usuario = 'atleta' ORDER BY nivel DESC";
$result_grafica = $conn->query($sql_grafica);

// Preparar los datos para la gráfica
$athletes = array();
$levels = array();
while ($row = $result_grafica->fetch_assoc()) {
    $athletes[] = $row['nombre_completo'];
    $levels[] = $row['nivel'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status de Atletas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
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

        .table-container {
            max-width: 900px;
            margin: 40px auto;
            background: #09D1C7;
            border-radius: 10px;
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
            background: #15919B;
            color: #FFFFFF;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        .action-button {
            display: inline-block;
            padding: 8px 15px;
            color: white;
            background: #213A58;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }

        .action-button:hover {
            background: #0C6478;
        }

        .chart-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        footer {
            text-align: center;
            margin-top: 50px;
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
        <a href="registro.php">Registro</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</div>
<header>
    <h1>Status de Atletas</h1>
</header>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Nombre del Atleta</th>
                <th>Ver Progreso</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT id, nombre_completo FROM usuarios WHERE tipo_usuario = 'atleta'");
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                    <td>
                        <a href="progreso.php?id=<?php echo $row['id']; ?>" class="action-button">Ver Progreso</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="chart-container">
    <canvas id="athletesChart"></canvas>
</div>

<footer>
    © 2024 Salvajes Outdoors. 
</footer>

<script>
    const ctx = document.getElementById('athletesChart').getContext('2d');
    const athletesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($athletes); ?>,
            datasets: [{
                label: 'Nivel de Atleta',
                data: <?php echo json_encode($levels); ?>,
                backgroundColor: [
                    '#15919B', '#80EE98', '#213A58', '#5CC8FF', '#1E90FF', '#ADD8E6'
                ],
                borderColor: [
                    '#0C6478', '#4F995E', '#152F3A', '#4AA3CC', '#1563A3', '#91BFD1'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nivel'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Atletas'
                    }
                }
            }
        }
    });
</script>
</body>
</html>
