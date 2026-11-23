<?php
require('fdpf/fpdf.php');
include "conexion.php";

// Función para traducir número de mes a nombre en español
function getMonthName($month) {
    $months = array(
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo',
        6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
        10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    );
    return isset($months[$month]) ? $months[$month] : 'Mes desconocido';
}

// Crear documento PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Informe Mensual de Entrenamientos', 0, 1, 'C');
$pdf->Ln(10);

// Obtener todos los atletas
$sql_atletas = "SELECT id, nombre_completo FROM usuarios WHERE tipo_usuario = 'atleta'";
$result_atletas = $conn->query($sql_atletas);

while ($atleta = $result_atletas->fetch_assoc()) {
    $id_atleta = $atleta['id'];
    $nombre = $atleta['nombre_completo'];

    // Agregar título del atleta
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode("Atleta: $nombre"), 0, 1);
    $pdf->Ln(5);

    // Resumen de entrenamientos
    $sql_resumen = "SELECT 
                        MONTH(fecha) as mes, 
                        SUM(estado = 'Completado') as completados,
                        SUM(estado = 'Pendiente') as pendientes,
                        SUM(estado = 'Cancelado') as cancelados
                    FROM planes_entrenamiento
                    WHERE id_atleta = $id_atleta
                    GROUP BY MONTH(fecha)";
    $result_resumen = $conn->query($sql_resumen);

    // Variables para gráficos
    $data = array();
    while ($row = $result_resumen->fetch_assoc()) {
        $data[] = array(
            'mes' => getMonthName($row['mes']),
            'completados' => $row['completados'],
            'pendientes' => $row['pendientes'],
            'cancelados' => $row['cancelados']
        );
    }

    // Dibujar gráfico de barras
    $x = 20; // Posición inicial X
    $y = $pdf->GetY() + 10; // Posición inicial Y
    $barWidth = 15; // Ancho de cada barra
    $barSpacing = 5; // Espaciado entre barras
    $groupSpacing = 20; // Espaciado entre grupos
    $maxHeight = 50; // Altura máxima de las barras

    // Calcular valores máximos
    $maxValue = 0;
    foreach ($data as $item) {
        $maxValue = max($maxValue, $item['completados'], $item['pendientes'], $item['cancelados']);
    }

    // Dibujar las barras para cada mes
    foreach ($data as $item) {
        $heightCompleted = ($item['completados'] / $maxValue) * $maxHeight;
        $heightPending = ($item['pendientes'] / $maxValue) * $maxHeight;
        $heightCancelled = ($item['cancelados'] / $maxValue) * $maxHeight;

        // Barra completados
        $pdf->SetFillColor(0, 128, 0); // Verde
        $pdf->Rect($x, $y + $maxHeight - $heightCompleted, $barWidth, $heightCompleted, 'F');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($x, $y + $maxHeight - $heightCompleted - 5);
        $pdf->Cell($barWidth, 5, $item['completados'] . ' (' . round(($item['completados'] / $maxValue) * 100, 1) . '%)', 0, 0, 'C');
        $pdf->SetXY($x, $y + $maxHeight + 2);
        $pdf->Cell($barWidth, 5, 'Completados', 0, 0, 'C');

        // Barra pendientes
        $pdf->SetFillColor(255, 165, 0); // Naranja
        $pdf->Rect($x + $barWidth + $barSpacing, $y + $maxHeight - $heightPending, $barWidth, $heightPending, 'F');
        $pdf->SetXY($x + $barWidth + $barSpacing, $y + $maxHeight - $heightPending - 5);
        $pdf->Cell($barWidth, 5, $item['pendientes'] . ' (' . round(($item['pendientes'] / $maxValue) * 100, 1) . '%)', 0, 0, 'C');
        $pdf->SetXY($x + $barWidth + $barSpacing, $y + $maxHeight + 2);
        $pdf->Cell($barWidth, 5, 'Pendientes', 0, 0, 'C');

        // Barra cancelados
        $pdf->SetFillColor(255, 0, 0); // Rojo
        $pdf->Rect($x + 2 * ($barWidth + $barSpacing), $y + $maxHeight - $heightCancelled, $barWidth, $heightCancelled, 'F');
        $pdf->SetXY($x + 2 * ($barWidth + $barSpacing), $y + $maxHeight - $heightCancelled - 5);
        $pdf->Cell($barWidth, 5, $item['cancelados'] . ' (' . round(($item['cancelados'] / $maxValue) * 100, 1) . '%)', 0, 0, 'C');
        $pdf->SetXY($x + 2 * ($barWidth + $barSpacing), $y + $maxHeight + 2);
        $pdf->Cell($barWidth, 5, 'Cancelados', 0, 0, 'C');

        // Etiqueta del mes
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($x, $y + $maxHeight + 10);
        $pdf->Cell(3 * $barWidth + 2 * $barSpacing, 5, utf8_decode($item['mes']), 0, 0, 'C');

        // Mover posición para el próximo grupo de barras
        $x += 3 * $barWidth + 2 * $barSpacing + $groupSpacing;
    }

    $pdf->Ln($maxHeight + 20); // Espacio entre gráficos
}

$pdf->Output();
?>
