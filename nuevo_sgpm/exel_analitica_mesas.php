<?php
include '../conexion/conexion.php';
require '../indexs/exel/vendor/autoload.php'; // Ruta ajustada del autoload

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Crear un nuevo Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados de la tabla en Excel
$encabezados = [
    'Carrera', 'Curso', 'Comisión', 'Materia', 
    'Aprobados', 'No Aprobados', 'Ausentes', 'Porcentaje Aprobados'
];

// Consultar los registros agrupados
$sql = "
SELECT 
    cr.nombre_carrera AS carrera, 
    c.curso AS curso, 
    cm.comision AS comision, 
    m.Nombre AS materia, 
    SUM(CASE WHEN nf.nota >= 6 THEN 1 ELSE 0 END) AS aprobados,
    SUM(CASE WHEN nf.nota < 6 AND nf.nota IS NOT NULL THEN 1 ELSE 0 END) AS no_aprobados,
    SUM(CASE WHEN nf.nota IS NULL AND nf.tomo IS NOT NULL AND nf.folio IS NOT NULL THEN 1 ELSE 0 END) AS ausentes,
    COUNT(nf.alumno_legajo) AS total,
    ROUND(SUM(CASE WHEN nf.nota >= 6 THEN 1 ELSE 0 END) / COUNT(nf.alumno_legajo) * 100, 2) AS porcentaje_aprobados
FROM nota_examen_final nf
JOIN materias m ON nf.materias_idMaterias = m.idMaterias
JOIN cursos c ON m.cursos_idCursos = c.idCursos
JOIN comisiones cm ON m.comisiones_idComisiones = cm.idComisiones
JOIN carreras cr ON m.carreras_idCarrera = cr.idCarrera
WHERE m.cursos_idCursos NOT IN (3)
GROUP BY cr.nombre_carrera, c.curso, cm.comision, m.Nombre
ORDER BY cr.nombre_carrera, c.curso, cm.comision, m.Nombre
";

$result = $conexion->query($sql);

$datos = [];
$aprobados = 0;
$no_aprobados = 0;
$ausentes = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
        $aprobados += $row['aprobados'];
        $no_aprobados += $row['no_aprobados'];
        $ausentes += $row['ausentes'];
    }
} else {
    echo "No se encontraron registros.";
    exit;
}

// Contador al principio del archivo Excel
$sheet->setCellValue('A1', 'Total Aprobados: ' . $aprobados);
$sheet->setCellValue('A2', 'Total No Aprobados: ' . $no_aprobados);
$sheet->setCellValue('A3', 'Total Ausentes: ' . $ausentes);

// Insertar encabezados en la fila 5 (dejando espacio para el contador)
foreach ($encabezados as $index => $encabezado) {
    $column = Coordinate::stringFromColumnIndex($index + 1);
    $sheet->setCellValue($column . '5', $encabezado);
}

// Aplicar negrita y centrar los encabezados
$sheet->getStyle('A5:H5')->getFont()->setBold(true);
$sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Ajustar el ancho de las columnas
foreach (range(1, count($encabezados)) as $columnIndex) {
    $column = Coordinate::stringFromColumnIndex($columnIndex);
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Llenar los datos en el archivo Excel
$rowIndex = 6; // Empezar en la fila 6 para los datos
foreach ($datos as $row) {
    $sheet->fromArray(array_values($row), null, 'A' . $rowIndex);
    $rowIndex++;
}

// Generar el gráfico de barras
$grafico_path = '../graficos/grafico_porcentajes.png';

if (!file_exists('../graficos')) {
    mkdir('../graficos', 0777, true);
}

$chartData = [
    'Aprobados' => $aprobados,
    'No Aprobados' => $no_aprobados,
    'Ausentes' => $ausentes
];

$labels = array_keys($chartData);
$values = array_values($chartData);

$grafico = imagecreatetruecolor(500, 300);
$background = imagecolorallocate($grafico, 255, 255, 255);
$colors = [
    imagecolorallocate($grafico, 50, 150, 50),   // Verde para aprobados
    imagecolorallocate($grafico, 200, 50, 50),   // Rojo para no aprobados
    imagecolorallocate($grafico, 150, 150, 150)  // Gris para ausentes
];

imagefilledrectangle($grafico, 0, 0, 500, 300, $background);

$barWidth = 80;
$gap = 50;
$x = 50;

foreach ($values as $index => $value) {
    $height = $value * 2;
    imagefilledrectangle($grafico, $x, 250 - $height, $x + $barWidth, 250, $colors[$index]);
    imagestring($grafico, 5, $x + 10, 260, $labels[$index], $colors[$index]);
    $x += $barWidth + $gap;
}

imagepng($grafico, $grafico_path);
imagedestroy($grafico);

// Limpiar el buffer de salida antes de generar el Excel
if (ob_get_length()) {
    ob_end_clean();
}

// Generar el nombre del archivo Excel
$nombre_archivo = 'Reporte_Notas_' . date('Ymd') . '.xlsx';

// Configurar el tipo de contenido y la descarga del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombre_archivo . '"');
header('Cache-Control: max-age=0');

// Salida del Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Finalizar el script
exit();
?>
