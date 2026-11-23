<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'atleta') {
    header("Location: login.php");
    exit();
}

$id_atleta = $_SESSION['usuario'];

// Obtener mes y año actuales desde la URL, o usar el mes/año actual como predeterminado
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// Calcular mes y año anteriores y siguientes
$prev_month = $month - 1;
$next_month = $month + 1;
$prev_year = $year;
$next_year = $year;

if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year--;
}
if ($next_month == 13) {
    $next_month = 1;
    $next_year++;
}

// Obtener el plan de entrenamiento para el mes actual
$start_date = sprintf('%04d-%02d-01', $year, $month);
$end_date = date('Y-m-t', strtotime($start_date));

$sql = "SELECT id, fecha, actividad, estado FROM planes_entrenamiento 
        WHERE id_atleta = (SELECT id FROM usuarios WHERE nombre_usuario = ?) 
        AND fecha BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $id_atleta, $start_date, $end_date);
$stmt->execute();
$stmt->bind_result($id, $fecha, $actividad, $estado);

$calendar = array();
while ($stmt->fetch()) {
    if (!isset($calendar[$fecha])) {
        $calendar[$fecha] = array();
    }
    $calendar[$fecha][] = array(
        'id' => $id,
        'actividad' => $actividad,
        'estado' => $estado
    );
}
$stmt->close();

// Nombres de los meses en español
$months = array(
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
);
$month_name = $months[$month];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de Entrenamiento</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Íconos -->
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #80EE98, #213A58);
            color: #2E4156;
        }

        header {
            background-color: #15919B;
            color: #FFFFFF;
            padding: 15px;
            text-align: center;
            font-size: 1.5rem;
            text-transform: uppercase;
            position: relative;
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

        .calendar-container {
            max-width: 900px;
            margin: 20px auto;
            background: #FFFFFF;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header a {
            color: #15919B;
            text-decoration: none;
            font-size: 1.2rem;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        .day {
            background: #F0F0F0;
            color: #2E4156;
            padding: 10px;
            border-radius: 5px;
            min-height: 100px;
            text-align: center;
            position: relative;
        }

        .activity {
            background: #007BFF;
            color: white;
            padding: 5px;
            border-radius: 5px;
            margin: 5px 0;
        }

        .btn-completed {
            background: green;
            color: white;
            padding: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn-canceled {
            background: red;
            color: white;
            padding: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .icon-status {
            font-size: 1.2rem;
            margin-left: 5px;
        }

        .completed {
            color: green;
        }

        .canceled {
            color: red;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: white;
            background-color: #15919B;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="menu">
        <button><i class="fas fa-bars"></i></button>
        <div class="dropdown">
            <a href="dashboard_atleta.php">Inicio</a>
            <a href="carreras.php">Próximas Carreras</a>
            <a href="ritmos.php">Ritmos</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>
    <header>
        Plan de Entrenamiento
    </header>
    <div class="calendar-container">
        <div class="calendar-header">
            <a href="?year=<?php echo $prev_year; ?>&month=<?php echo $prev_month; ?>">&#9664; Anterior</a>
            <h2><?php echo $month_name . ' ' . $year; ?></h2>
            <a href="?year=<?php echo $next_year; ?>&month=<?php echo $next_month; ?>">Siguiente &#9654;</a>
        </div>
        <div class="calendar">
            <?php for ($i = 1; $i <= date('t', strtotime($start_date)); $i++): ?>
                <?php $date = sprintf('%04d-%02d-%02d', $year, $month, $i); ?>
                <div class="day">
                    <strong><?php echo $i; ?></strong>
                    <?php if (isset($calendar[$date])): ?>
                        <?php foreach ($calendar[$date] as $activity): ?>
                            <div class="activity">
                                <?php echo htmlspecialchars($activity['actividad']); ?>
                                <?php if ($activity['estado'] == 'pendiente'): ?>
                                    <form action="actualizar_estado.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="id_competencia" value="<?php echo $activity['id']; ?>">
                                        <input type="hidden" name="estado" value="Completado">
                                        <button type="submit" class="btn-completed">Completado</button>
                                    </form>
                                    <form action="actualizar_estado.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="id_competencia" value="<?php echo $activity['id']; ?>">
                                        <input type="hidden" name="estado" value="Cancelado">
                                        <button type="submit" class="btn-canceled">Cancelado</button>
                                    </form>
                                <?php else: ?>
                                    <i class="icon-status <?php echo $activity['estado'] == 'completado' ? 'fas fa-check completed' : 'fas fa-times canceled'; ?>"></i>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <footer>
        © 2024 Salvajes Outdoors. 
    </footer>
</body>
</html>
