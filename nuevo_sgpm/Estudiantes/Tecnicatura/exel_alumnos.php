<?php
session_start();
include '../../../conexion/conexion.php';
require '../../../indexs/exel/vendor/autoload.php'; // Asegúrate de ajustar la ruta del autoload según corresponda

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use DateTime;

// Crear un nuevo Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados de la tabla
$sheet->setCellValue('A1', 'Nº'); // Contador
$sheet->setCellValue('B1', 'Legajo');
$sheet->setCellValue('C1', 'Apellido');
$sheet->setCellValue('D1', 'Nombre');
$sheet->setCellValue('E1', 'DNI');
$sheet->setCellValue('F1', 'Celular');
$sheet->setCellValue('G1', 'Carrera');
$sheet->setCellValue('H1', 'Edad');
$sheet->setCellValue('I1', 'Fecha Nacimiento'); // Columna para día, mes y año

// Aplicar negrita y centrar los encabezados
$sheet->getStyle('A1:I1')->getFont()->setBold(true);
$sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Ajustar el ancho de las columnas
$sheet->getColumnDimension('A')->setWidth(5);  // Contador
$sheet->getColumnDimension('B')->setWidth(7);  // Legajo
$sheet->getColumnDimension('C')->setWidth(16); // Apellido
$sheet->getColumnDimension('D')->setWidth(15); // Nombre
$sheet->getColumnDimension('E')->setWidth(10); // DNI
$sheet->getColumnDimension('F')->setWidth(12); // Celular
$sheet->getColumnDimension('G')->setWidth(28); // Carrera
$sheet->getColumnDimension('H')->setWidth(8);  // Edad
$sheet->getColumnDimension('I')->setWidth(15); // Fecha de Nacimiento

// Aplicar los filtros a los encabezados (A1:I1)
$sheet->setAutoFilter('A1:I1');

// Consultar los datos
$profesor_id = $_SESSION["id"];
$sql1 = "SELECT a.*, c.nombre_carrera, a.edad
         FROM inscripcion_asignatura ia
         INNER JOIN alumno a ON ia.alumno_legajo = a.legajo
         INNER JOIN preceptores p ON p.carreras_idCarrera = ia.carreras_idCarrera
         INNER JOIN carreras c ON ia.carreras_idCarrera = c.idCarrera
         WHERE p.profesor_idProrfesor = $profesor_id AND a.estado = '1'";
$query1 = mysqli_query($conexion, $sql1);

// Inicializar contador
$contador = 1;

// Llenar los datos en el Excel
$rowIndex = 2; // Empezar a insertar los datos desde la fila 2
while ($datos = mysqli_fetch_assoc($query1)) {
    // Obtener la edad de la base de datos
    $edad = $datos['edad'];
    
    // Calcular la fecha de nacimiento estimada
    $fecha_actual = new DateTime();  // Fecha actual
    $anio_nacimiento = $fecha_actual->format('Y') - $edad;  // Restar la edad al año actual
    
    // Estimar que nació el 1 de enero del año calculado (se puede ajustar si hay más información)
    $fecha_nacimiento = DateTime::createFromFormat('Y-m-d', $anio_nacimiento . '-01-01');
    $fecha_nac_formateada = $fecha_nacimiento->format('d/m/Y'); // Formato día/mes/año

    // Insertar el contador en la columna A
    $sheet->setCellValue('A' . $rowIndex, $contador);
    
    // Insertar el resto de los datos
    $sheet->setCellValue('B' . $rowIndex, $datos['legajo']);
    $sheet->setCellValue('C' . $rowIndex, $datos['apellido_alumno']);
    $sheet->setCellValue('D' . $rowIndex, $datos['nombre_alumno']);
    $sheet->setCellValue('E' . $rowIndex, $datos['dni_alumno']);
    $sheet->setCellValue('F' . $rowIndex, $datos['celular']);
    $sheet->setCellValue('G' . $rowIndex, $datos['nombre_carrera']);
    $sheet->setCellValue('H' . $rowIndex, $edad); // Insertar la edad
    $sheet->setCellValue('I' . $rowIndex, $fecha_nac_formateada); // Insertar la fecha de nacimiento

    // Incrementar el contador y el índice de la fila
    $contador++;
    $rowIndex++;
}

// Limpiar el buffer de salida antes de generar el Excel
if (ob_get_length()) {
    ob_end_clean();
}

// Generar el nombre del archivo Excel
$nombre_archivo = 'Reporte_Estudiantes_' . date('Ymd') . '.xlsx';

// Configurar el tipo de contenido y la descarga del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombre_archivo . '"');
header('Cache-Control: max-age=0');

// Salida del Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Asegúrate de finalizar el script
exit();
?>
