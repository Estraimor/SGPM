<?php
include '../conexion/conexion.php';
require '../indexs/exel/vendor/autoload.php'; // Ruta ajustada del autoload

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Consultas para contar el número de registros por carrera
$sql_enfermeria = "SELECT COUNT(*) AS total FROM pre_inscripciones WHERE carrera = 'Enfermería'";
$sql_comercializacion = "SELECT COUNT(*) AS total FROM pre_inscripciones WHERE carrera = 'Comercialización y Marketing'";
$sql_acompanante = "SELECT COUNT(*) AS total FROM pre_inscripciones WHERE carrera = 'Acompañante Terapéutico'";
$sql_automatizacion = "SELECT COUNT(*) AS total FROM pre_inscripciones WHERE carrera = 'Automatización y Robótica'";
$sql_total = "SELECT COUNT(*) AS total FROM pre_inscripciones";

// Ejecutar las consultas
$result_enfermeria = $conexion->query($sql_enfermeria)->fetch_assoc()['total'];
$result_comercializacion = $conexion->query($sql_comercializacion)->fetch_assoc()['total'];
$result_acompanante = $conexion->query($sql_acompanante)->fetch_assoc()['total'];
$result_automatizacion = $conexion->query($sql_automatizacion)->fetch_assoc()['total'];
$result_total = $conexion->query($sql_total)->fetch_assoc()['total'];

// Crear un nuevo Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados y datos de las carreras
$sheet->setCellValue('J13', 'Carrera');
$sheet->setCellValue('K13', 'Total');
$sheet->getColumnDimension('J')->setWidth(25); // Ajustar el ancho de la columna
$sheet->setCellValue('J14', 'Enfermería');
$sheet->setCellValue('K14', $result_enfermeria);

$sheet->setCellValue('J15', 'Comercialización y Marketing');
$sheet->setCellValue('K15', $result_comercializacion);

$sheet->setCellValue('J16', 'Acompañante Terapéutico');
$sheet->setCellValue('K16', $result_acompanante);

$sheet->setCellValue('J17', 'Automatización y Robótica');
$sheet->setCellValue('K17', $result_automatizacion);

$sheet->setCellValue('J18', 'Total');
$sheet->setCellValue('K18', $result_total);

// Aplicar negrita y centrar a las celdas de carreras
$sheet->getStyle('J13:K18')->getFont()->setBold(true);
$sheet->getStyle('J13:K18')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Encabezados de la tabla de registros en la primera fila con el contador
$sheet->setCellValue('A1', 'Nº');
$sheet->setCellValue('B1', 'Apellido');
$sheet->setCellValue('C1', 'Nombre');
$sheet->setCellValue('D1', 'DNI');
$sheet->setCellValue('E1', 'Fecha de Nacimiento');
$sheet->setCellValue('F1', 'Edad');
$sheet->setCellValue('G1', 'Celular');
$sheet->setCellValue('H1', 'Correo');
$sheet->setCellValue('I1', 'Carrera');

// Aplicar negrita y centrar para los encabezados de la tabla
$sheet->getStyle('A1:I1')->getFont()->setBold(true);
$sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Agregar filtros a los encabezados
$sheet->setAutoFilter('A1:I1');

// Ajustar el ancho de todas las columnas
$sheet->getColumnDimension('A')->setWidth(8);  // Nº
$sheet->getColumnDimension('B')->setWidth(20); // Apellido
$sheet->getColumnDimension('C')->setWidth(20); // Nombre
$sheet->getColumnDimension('D')->setWidth(12); // DNI
$sheet->getColumnDimension('E')->setWidth(23); // Fecha de Nacimiento
$sheet->getColumnDimension('F')->setWidth(5);  // Edad
$sheet->getColumnDimension('G')->setWidth(15); // Celular
$sheet->getColumnDimension('H')->setWidth(30); // Correo
$sheet->getColumnDimension('I')->setWidth(25); // Carrera

// Llenar los registros de la tabla en Excel
$rowIndex = 2; // Empezamos en la fila 2, debajo de los encabezados
$counter = 1;  // Inicializar el contador
$sql = "SELECT * FROM pre_inscripciones";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calcular la edad
        $fecha_nacimiento = new DateTime($row["fecha_nacimiento"]);
        $fecha_actual = new DateTime();
        $edad = $fecha_actual->diff($fecha_nacimiento)->y;

        // Formatear la fecha de nacimiento en formato día/mes/año
        $fecha_nac_formateada = $fecha_nacimiento->format('d/m/Y');

        // Insertar datos en las celdas con el contador
        $sheet->setCellValue('A' . $rowIndex, $counter); // Contador
        $sheet->setCellValue('B' . $rowIndex, $row['apellido']); // Apellido primero
        $sheet->setCellValue('C' . $rowIndex, $row['nombre']); // Nombre después
        $sheet->setCellValue('D' . $rowIndex, $row['DNI']);
        $sheet->setCellValue('E' . $rowIndex, $fecha_nac_formateada); // Fecha formateada
        $sheet->setCellValue('F' . $rowIndex, $edad);
        $sheet->setCellValue('G' . $rowIndex, $row['celular']);
        $sheet->setCellValue('H' . $rowIndex, $row['correo']);
        $sheet->setCellValue('I' . $rowIndex, $row['carrera']);

        // Incrementar el índice de la fila y el contador
        $rowIndex++;
        $counter++;
    }
} else {
    echo "No se encontraron registros.";
}

// Asegúrate de limpiar el buffer de salida antes de generar el Excel
if (ob_get_length()) {
    ob_end_clean(); // Limpia cualquier contenido del buffer de salida
}

// Generar el nombre del archivo Excel
$nombre_archivo = 'Reporte_preinscripciones_' . date('Ymd') . '.xlsx';

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
