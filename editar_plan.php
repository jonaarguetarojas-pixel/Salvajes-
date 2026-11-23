<?php
include "conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

// Obtener el ID del atleta desde la URL
$atleta_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar si el atleta existe
$sql_atleta = "SELECT nombre_completo FROM usuarios WHERE id = $atleta_id AND tipo_usuario = 'atleta'";
$result_atleta = $conn->query($sql_atleta);

if ($result_atleta->num_rows == 0) {
    die("Atleta no encontrado.");
}

$atleta = $result_atleta->fetch_assoc();
$nombre_atleta = $atleta['nombre_completo'];

// Obtener el mes y año actual o seleccionados
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Calcular las fechas del mes
$start_date = sprintf('%04d-%02d-01', $year, $month);
$end_date = date('Y-m-t', strtotime($start_date));

// Consultar actividades del atleta para el mes
$sql_actividades = "SELECT id, fecha, actividad, estado FROM planes_entrenamiento 
                    WHERE id_atleta = $atleta_id AND fecha BETWEEN '$start_date' AND '$end_date'";
$result_actividades = $conn->query($sql_actividades);

$calendar = array();
while ($row = $result_actividades->fetch_assoc()) {
    if (!isset($calendar[$row['fecha']])) {
        $calendar[$row['fecha']] = array();
    }
    $calendar[$row['fecha']][] = $row;
}

// Nombres de los meses en español
$months = array(
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
);
$month_name = isset($months[$month]) ? $months[$month] : '';

// Calcular el mes anterior y el siguiente
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month == 13) {
    $next_month = 1;
    $next_year++;
}

// Días de la semana en español
$week_days = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');

// Fecha actual
$current_date = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Plan - <?php echo htmlspecialchars($nombre_atleta); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #80EE98, #213A58);
            color: #2E4156;
            margin: 0;
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
            background: #09D1C7;
            border-radius: 10px;
            padding: 20px;
        }

        .calendar-header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #FFFFFF;
        }

        .calendar-header a {
            text-decoration: none;
            color: #FFFFFF;
            font-size: 1.2rem;
            margin: 0 10px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        .day-header {
            font-weight: bold;
            text-align: center;
            color: #FFFFFF;
        }

        .day {
            background: #FFFFFF;
            border-radius: 5px;
            padding: 10px;
            min-height: 100px;
            text-align: center;
        }

        .activity {
            padding: 5px;
            border-radius: 3px;
            margin: 5px 0;
        }

        .completado {
            background-color: green;
            color: white;
        }

        .cancelado {
            background-color: red;
            color: white;
        }

        .pendiente {
            background-color: orange;
            color: white;
        }

        .add-option {
            margin-top: 10px;
            padding: 5px;
            background: #28a745;
            color: #fff;
            text-align: center;
            border-radius: 3px;
            cursor: pointer;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background: #15919B;
            color: #FFFFFF;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }
    </style>
    
html
Copiar código
<script>
    function openModal(date) {
        document.getElementById('modal').style.display = 'flex';
        document.getElementById('selected-date').value = date;
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';

        // Limpiar los campos del formulario al cerrar el modal
        document.getElementById('tipo-actividad').value = "";
        document.getElementById('easy-run-fields').style.display = "none";
        document.getElementById('fuerza-field').style.display = "none";
        document.getElementById('intervalos-fields').style.display = "none";
        document.getElementById('actividad-form').reset(); // Resetea el formulario
    }

    function updateForm() {
        const tipoActividad = document.getElementById('tipo-actividad').value;

        // Mostrar u ocultar campos dependiendo del tipo de actividad seleccionado
        document.getElementById('easy-run-fields').style.display = tipoActividad === "Easy Run" ? "block" : "none";
        document.getElementById('fuerza-field').style.display = tipoActividad === "Entrenamiento de Fuerza" ? "block" : "none";
        document.getElementById('intervalos-fields').style.display = tipoActividad === "Intervalos" ? "block" : "none";
    }
</script>
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
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</div>
<header>
    <h1>Editar Plan - <?php echo htmlspecialchars($nombre_atleta); ?></h1>
</header>
<div class="container">
    <div class="calendar-header">
        <a href="?id=<?php echo $atleta_id; ?>&year=<?php echo $prev_year; ?>&month=<?php echo $prev_month; ?>">&laquo; Anterior</a>
        <?php echo $month_name . ' ' . $year; ?>
        <a href="?id=<?php echo $atleta_id; ?>&year=<?php echo $next_year; ?>&month=<?php echo $next_month; ?>">Siguiente &raquo;</a>
    </div>
    <div class="calendar">
        <?php
        foreach ($week_days as $day) {
            echo '<div class="day-header">' . $day . '</div>';
        }

        $first_day_of_month = date('N', strtotime($start_date));
        $days_in_month = date('t', strtotime($start_date));

        for ($i = 1; $i < $first_day_of_month; $i++) {
            echo '<div class="day"></div>';
        }

        for ($i = 1; $i <= $days_in_month; $i++) {
            $current_date_loop = sprintf('%04d-%02d-%02d', $year, $month, $i);

            echo '<div class="day">';
            echo '<div>' . $i . '</div>';

            if (isset($calendar[$current_date_loop])) {
                foreach ($calendar[$current_date_loop] as $activity) {
                    $estado_class = strtolower($activity['estado']);
                    echo '<div class="activity ' . $estado_class . '">';
                    echo htmlspecialchars($activity['actividad']) . ' (' . htmlspecialchars($activity['estado']) . ')';
                    echo '</div>';
                }
            }

            echo '<div class="add-option" onclick="openModal(\'' . $current_date_loop . '\')">+ Agregar</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>
<!-- Modal HTML -->
<div id="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
    <div style="background: white; padding: 20px; border-radius: 10px; max-width: 500px; text-align: center;">
        <h3>Agregar Actividad</h3>
        <form id="actividad-form" action="guardar_actividad.php" method="POST">
            <input type="hidden" name="fecha" id="selected-date">
            <input type="hidden" name="id_atleta" value="<?php echo $atleta_id; ?>">

            <label for="tipo-actividad">Tipo de Actividad:</label>
            <select name="tipo_actividad" id="tipo-actividad" onchange="updateForm()" required>
                <option value="">Selecciona una actividad</option>
                <option value="Easy Run">Easy Run</option>
                <option value="Entrenamiento de Fuerza">Entrenamiento de Fuerza</option>
                <option value="Intervalos">Intervalos</option>
            </select>

            <!-- Campos para Easy Run -->
            <div id="easy-run-fields" style="display: none;">
                <label for="easy-run-tiempo">Tiempo (minutos):</label>
                <input type="text" name="easy_run_tiempo" id="easy-run-tiempo" placeholder="Tiempo (ej: 30 min)">
                <label for="easy-run-distancia">Distancia (km):</label>
                <input type="text" name="easy_run_distancia" id="easy-run-distancia" placeholder="Distancia (ej: 5 km)">
                <label for="easy-run-ritmo">Ritmo (min/km):</label>
                <input type="text" name="easy_run_ritmo" id="easy-run-ritmo" placeholder="Ritmo (ej: 6:00)">
            </div>

            <!-- Campo para Entrenamiento de Fuerza -->
            <div id="fuerza-field" style="display: none;">
                <label for="fuerza-link">Enlace (YouTube):</label>
                <input type="url" name="fuerza_link" id="fuerza-link" placeholder="Enlace al video">
            </div>

            <!-- Campos para Intervalos -->
            <div id="intervalos-fields" style="display: none;">
                <label for="intervalos-distancia">Distancia (km):</label>
                <input type="text" name="intervalos_distancia" id="intervalos-distancia" placeholder="Distancia (ej: 1 km)">
                <label for="intervalos-ritmo">Ritmo (min/km):</label>
                <input type="text" name="intervalos_ritmo" id="intervalos-ritmo" placeholder="Ritmo (ej: 5:30)">
                <label for="intervalos-repeticiones">Repeticiones:</label>
                <input type="number" name="intervalos_repeticiones" id="intervalos-repeticiones" placeholder="Número de repeticiones">
            </div>

            <br>
            <button type="submit">Guardar</button>
            <button type="button" onclick="closeModal()">Cancelar</button>
        </form>
    </div>
</div>

<footer>
    © 2024 Salvajes Outdoors. 
</footer>
</body>
</html>
