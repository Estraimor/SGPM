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

// Encabezados de la tabla en Excel (todos los campos de la tabla 'alumno')
$encabezados = [
    'Nº', 'ID Alumno', 'Nombre', 'Apellido', 'DNI', 'Legajo', 'Trabaja_Horario', 
    'Edad', 'Observaciones', 'Celular', 'Estado', 'Usuario', 'Contraseña',
    'Ciudad de Nacimiento', 'Provincia de Nacimiento', 'País de Nacimiento', 
    'CUIL', 'Discapacidad', 'Fecha de Nacimiento', 'Título Secundario', 
    'Escuela Secundaria', 'Materias Adeuda', 'Fecha Estimación', 'Ocupación', 
    'Domicilio Laboral', 'Horario Laboral Desde', 'Horario Laboral Hasta',
    'Calle Domicilio', 'Barrio Domicilio', 'Ciudad Domicilio', 
    'Provincia Domicilio', 'Numeración Domicilio', 'Teléfono de Urgencias', 
    'Correo', 'Carrera', 'Original del Título', 'Fotos', 'Folio', 
    'Fotocopia DNI', 'Fotocopia Partida Nacimiento', 'Constancia CUIL', 'Pago'
];

// Insertar encabezados en la primera fila del archivo Excel
foreach ($encabezados as $index => $encabezado) {
    $column = Coordinate::stringFromColumnIndex($index + 1); // Convertir el índice a una letra de columna
    $sheet->setCellValue($column . '1', $encabezado);
}

// Aplicar negrita y centrar los encabezados
$sheet->getStyle('A1:AP1')->getFont()->setBold(true);
$sheet->getStyle('A1:AP1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Ajustar el ancho de las columnas
foreach (range(1, count($encabezados)) as $columnIndex) {
    $column = Coordinate::stringFromColumnIndex($columnIndex);
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Consultar los registros de la tabla 'alumno' con estado = 3
$sql = "SELECT * FROM alumno WHERE estado = 3";
$result = $conexion->query($sql);

$rowIndex = 2; // Empezar en la fila 2 para los datos
$counter = 1;  // Inicializar el contador

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Insertar datos de cada campo en el archivo Excel
        $sheet->setCellValue('A' . $rowIndex, $counter); // Contador
        $sheet->setCellValue('B' . $rowIndex, $row['idAlumno']);
        $sheet->setCellValue('C' . $rowIndex, $row['nombre_alumno']);
        $sheet->setCellValue('D' . $rowIndex, $row['apellido_alumno']);
        $sheet->setCellValue('E' . $rowIndex, $row['dni_alumno']);
        $sheet->setCellValue('F' . $rowIndex, $row['legajo']);
        $sheet->setCellValue('G' . $rowIndex, $row['Trabaja_Horario']);
        $sheet->setCellValue('H' . $rowIndex, $row['edad']);
        $sheet->setCellValue('I' . $rowIndex, $row['observaciones']);
        $sheet->setCellValue('J' . $rowIndex, $row['celular']);
        $sheet->setCellValue('K' . $rowIndex, $row['estado']);
        $sheet->setCellValue('L' . $rowIndex, $row['usu_alumno']);
        $sheet->setCellValue('M' . $rowIndex, $row['pass_alumno']);
        $sheet->setCellValue('N' . $rowIndex, $row['ciudad_nacimiento']);
        $sheet->setCellValue('O' . $rowIndex, $row['provincia_nacimiento']);
        $sheet->setCellValue('P' . $rowIndex, $row['pais_nacimiento']);
        $sheet->setCellValue('Q' . $rowIndex, $row['cuil']);
        $sheet->setCellValue('R' . $rowIndex, $row['discapacidad']);
        $sheet->setCellValue('S' . $rowIndex, $row['fecha_nacimiento']);
        $sheet->setCellValue('T' . $rowIndex, $row['Titulo_secundario']);
        $sheet->setCellValue('U' . $rowIndex, $row['escuela_secundaria']);
        $sheet->setCellValue('V' . $rowIndex, $row['materias_adeuda']);
        $sheet->setCellValue('W' . $rowIndex, $row['fecha_estimacion']);
        $sheet->setCellValue('X' . $rowIndex, $row['ocupacion']);
        $sheet->setCellValue('Y' . $rowIndex, $row['domicilio_laboral']);
        $sheet->setCellValue('Z' . $rowIndex, $row['horario_laboral_desde']);
        $sheet->setCellValue('AA' . $rowIndex, $row['horario_laboral_hasta']);
        $sheet->setCellValue('AB' . $rowIndex, $row['calle_domicilio']);
        $sheet->setCellValue('AC' . $rowIndex, $row['barrio_domicilio']);
        $sheet->setCellValue('AD' . $rowIndex, $row['ciudad_domicilio']);
        $sheet->setCellValue('AE' . $rowIndex, $row['provincia_domicilio']);
        $sheet->setCellValue('AF' . $rowIndex, $row['numeracion_domicilio']);
        $sheet->setCellValue('AG' . $rowIndex, $row['telefono_urgencias']);
        $sheet->setCellValue('AH' . $rowIndex, $row['correo']);
        $sheet->setCellValue('AI' . $rowIndex, $row['carrera']);
        $sheet->setCellValue('AJ' . $rowIndex, $row['original_titulo']);
        $sheet->setCellValue('AK' . $rowIndex, $row['fotos']);
        $sheet->setCellValue('AL' . $rowIndex, $row['folio']);
        $sheet->setCellValue('AM' . $rowIndex, $row['fotocopia_dni']);
        $sheet->setCellValue('AN' . $rowIndex, $row['fotocopia_partida_nacimiento']);
        $sheet->setCellValue('AO' . $rowIndex, $row['constancia_cuil']);
        $sheet->setCellValue('AP' . $rowIndex, $row['Pago']);

        // Incrementar el índice de la fila y el contador
        $rowIndex++;
        $counter++;
    }
} else {
    echo "No se encontraron registros.";
}

// Limpiar el buffer de salida antes de generar el Excel
if (ob_get_length()) {
    ob_end_clean();
}

// Generar el nombre del archivo Excel
$nombre_archivo = 'Reporte_Alumnos_Estado3_' . date('Ymd') . '.xlsx';

// Configurar el tipo de contenido y la descarga del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombre_archivo . '"');
header('Cache-Control: max-age=0');

// Salida del Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Asegúrate de finalizar el script después de la salida del Excel
exit();
?>
