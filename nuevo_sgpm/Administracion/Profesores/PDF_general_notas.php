<?php
require '../../../indexs/pdf/vendor/setasign/fpdf/fpdf.php'; // Incluir la librería FPDF para generar PDFs
include '../../../conexion/conexion.php'; // Incluir la conexión a la base de datos

// Capturar los parámetros de la URL (idCarrera y idMateria)
$idCarrera = $_GET['idCarrera'];
$idMateria = $_GET['idMateria'];

// Consulta para obtener los datos de la materia y la carrera
$query_materia = "
    SELECT m.Nombre as materia, p.nombre_profe, p.apellido_profe, c.nombre_carrera as carrera
    FROM materias m
    INNER JOIN profesor p ON m.profesor_idProrfesor = p.idProrfesor
    INNER JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
    WHERE m.idMaterias = ?
";
$stmt_materia = $conexion->prepare($query_materia);
$stmt_materia->bind_param("i", $idMateria);
$stmt_materia->execute();
$result_materia = $stmt_materia->get_result();
$datos_materia = $result_materia->fetch_assoc();

// Consulta para contar cuántos TPs hay por cuatrimestre
$query_tp_count = "
    SELECT n.cuatrimestre, COUNT(DISTINCT n.numero_evaluacion) AS tp_count
    FROM notas n
    WHERE n.tipo_evaluacion = 1 AND n.materias_idMaterias = ? AND n.carreras_idCarrera = ?
    GROUP BY n.cuatrimestre
";
$stmt_tp_count = $conexion->prepare($query_tp_count);
$stmt_tp_count->bind_param("ii", $idMateria, $idCarrera);
$stmt_tp_count->execute();
$result_tp_count = $stmt_tp_count->get_result();

$tp_count = [1 => 0, 2 => 0]; // Inicializar el contador de TPs por cuatrimestre
while ($row_tp_count = $result_tp_count->fetch_assoc()) {
    $tp_count[$row_tp_count['cuatrimestre']] = $row_tp_count['tp_count'];
}

// Consulta para obtener los alumnos, sus notas y la nota final y condición
$sql1 = "
    SELECT a.legajo, a.apellido_alumno, a.nombre_alumno, 
           n.idnotas, n.numero_evaluacion, n.nota, n.cuatrimestre, n.tipo_evaluacion,
           n.nota_final, n.condicion
    FROM inscripcion_asignatura ia
    INNER JOIN alumno a ON ia.alumno_legajo = a.legajo
    LEFT JOIN notas n ON a.legajo = n.alumno_legajo AND n.materias_idMaterias = $idMateria
    WHERE ia.carreras_idCarrera = $idCarrera AND a.estado = 1
    ORDER BY a.apellido_alumno
";

$result = mysqli_query($conexion, $sql1);
$alumnos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $legajo = $row['legajo'];
    $cuatrimestre = $row['cuatrimestre'];
    $tipoEvaluacion = $row['tipo_evaluacion'];
    $nota = $row['nota'];
    $idnotas = $row['idnotas'];

    // Si el alumno no está en el arreglo, inicializamos su estructura
    if (!isset($alumnos[$legajo])) {
        $alumnos[$legajo] = [
            'nombre_completo' => $row['apellido_alumno'] . ' ' . $row['nombre_alumno'],
            'cuatrimestres' => [
                1 => ['tps' => [], 'parcial' => '', 'recuperatorio' => ''],
                2 => ['tps' => [], 'parcial' => '', 'recuperatorio' => '']
            ],
            'nota_final' => null,
            'condicion' => null
        ];
    }

    // Solo asignar nota_final y condición si no son NULL
    if (!is_null($row['nota_final'])) {
        $alumnos[$legajo]['nota_final'] = $row['nota_final'];
    }
    if (!is_null($row['condicion'])) {
        $alumnos[$legajo]['condicion'] = $row['condicion'];
    }

    // Asignación de las notas por cuatrimestre y tipo de evaluación
    if ($cuatrimestre == 1) {
        if ($tipoEvaluacion == 2) {
            $alumnos[$legajo]['cuatrimestres'][1]['parcial'] = $nota;
        } elseif ($tipoEvaluacion == 3) {
            $alumnos[$legajo]['cuatrimestres'][1]['recuperatorio'] = $nota;
        } else {
            $alumnos[$legajo]['cuatrimestres'][1]['tps'][] = $nota;
        }
    } elseif ($cuatrimestre == 2) {
        if ($tipoEvaluacion == 2) {
            $alumnos[$legajo]['cuatrimestres'][2]['parcial'] = $nota;
        } elseif ($tipoEvaluacion == 3) {
            $alumnos[$legajo]['cuatrimestres'][2]['recuperatorio'] = $nota;
        } else {
            $alumnos[$legajo]['cuatrimestres'][2]['tps'][] = $nota;
        }
    }
}

// Añadir una línea de prueba para verificar la estructura de $alumnos y que contiene los datos de nota_final y condición

// Crear el PDF
class PDF extends FPDF {
    function Header() {
    global $datos_materia;
    $this->SetFont('Arial', 'B', 20);
    $this->Image('../../../imagenes/politecnico.png', 15, 10, 25);
    $this->Image('../../../imagenes/CGE.png', 170, 10, 25);

    // Título
    $year = date('Y');
    $this->Cell(0, 10, 'Planilla de Calificaciones ' . $year, 0, 1, 'C');
    $this->SetFont('Arial', 'B', 14);
    $this->Cell(0, 10, utf8_decode($datos_materia['carrera']), 0, 1, 'C');
    // Ajuste para limitar caracteres en 'Unidad Curricular'
    $anchoMaximo = 120; // Define el ancho máximo en puntos, ajústalo según el tamaño del documento
    $this->SetX((210 - $anchoMaximo) / 2); // Ajusta el margen izquierdo para centrar
    $this->MultiCell($anchoMaximo, 10, 'Unidad Curricular: ' . utf8_decode($datos_materia['materia']), 0, 'C');
    // Nombre del profesor encargado
    $profesor = $datos_materia['nombre_profe'] . ' ' . $datos_materia['apellido_profe'];
    $this->SetFont('Arial', '', 12);
    $this->Cell(0, 10, 'Profesor a cargo: ' . utf8_decode($profesor), 0, 1, 'C');
    
    $this->Ln(10);
}

// Función para el pie de página

function Footer() {
     global $isLastPage, $datos_materia;

    // Mostrar número de página en todas las páginas
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Pag ' . $this->PageNo(), 0, 0, 'C');

    // Solo mostrar la firma en la última página
    if ($isLastPage) {
        // Imagen en el centro de la última página
        $this->SetY(-90); // Ajusta la posición vertical para la imagen
        $this->SetX(80); // Centra la imagen horizontalmente
        $this->Image('../../../imagenes/sello_obal.jpg', 83, $this->GetY(), 40); // Imagen en el centro

        // Línea y texto de Firma en el lado derecho
        $this->SetY(-35);
        $this->SetX(-80);
        $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 60, $this->GetY());
        $this->SetY(-34);
        $this->SetX(-80);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(60, 5, utf8_decode("Firma y Aclaración:\n" . $datos_materia['nombre_profe'] . ' ' . $datos_materia['apellido_profe']), 0, 'C');

        // Imagen de la firma en el lado izquierdo
        $this->SetY(-35); // Mantiene la misma altura que la línea
        $this->SetX(15); // Posición en el lado izquierdo
        $this->Line(15, $this->GetY(), 75, $this->GetY()); // Línea en el lado izquierdo

        $this->SetY(-30); // Ajusta la posición vertical para la imagen de la firma
        $this->SetX(15); // Posición en el lado izquierdo para la imagen
        $this->Image('../../../imagenes/Anibal_sello.jpg', 23, $this->GetY(), 40); // Imagen en el lado izquierdo

        // Texto "Firma y Aclaración" en el lado izquierdo
        $this->SetY(-34);
        $this->SetX(15);
        
    }
}




    function TableHeader($tp_count) {
        $this->SetFont('Arial', 'B', 9);

        // Primera fila del encabezado: Nombre y Cuatrimestres
        $this->Cell(55, 8, '', 0, 0, 'C'); // Celda vacía antes del cuatrimestre

        // Anchos para 1er y 2do Cuatrimestre, que son dinámicos según los TP
        $ancho_1er_cuatri = (8 * $tp_count[1]) + 20; // 8 por cada TP + 20 (para Parcial y Recuperatorio)
        $ancho_2do_cuatri = (8 * $tp_count[2]) + 20;

        $this->SetFont('Arial', 'B', 7);
        $this->Cell($ancho_1er_cuatri, 8, '1er Cuatrimestre', 1, 0, 'C');
        $this->Cell($ancho_2do_cuatri, 8, '2do Cuatrimestre', 1, 0, 'C');
        $this->MultiCell(14, 4, utf8_decode("Cali\nDefinitiva"), 1, 'C');

        // Segunda fila del encabezado: Nombre y Apellido, TP1, TP2, Parcial, Recuperatorio
        $this->SetFont('Arial', 'B', 6);
        $this->Cell(55, 8, 'Nombre y Apellido', 1, 0, 'C');

        // Encabezado dinámico del 1er Cuatrimestre
        for ($i = 1; $i <= $tp_count[1]; $i++) {
            $this->Cell(8, 8, 'TP' . $i, 1, 0, 'C');
        }
        $this->Cell(10, 8, 'Parcial', 1, 0, 'C');
        $this->Cell(10, 8, 'Recup', 1, 0, 'C');

        // Encabezado dinámico del 2do Cuatrimestre
        for ($i = 1; $i <= $tp_count[2]; $i++) {
            $this->Cell(8, 8, 'TP' . $i, 1, 0, 'C');
        }
        $this->Cell(10, 8, 'Parcial', 1, 0, 'C');
        $this->Cell(10, 8, 'Recup', 1, 0, 'C');

        // Celda de Calificación definitiva y Condición
        $this->Cell(14, 8, 'Nota Final', 1, 0, 'C');
        $this->Cell(15, 8, utf8_decode('Condición'), 1, 1, 'C');
    }

    function TableRow($nombre_completo, $calificaciones, $tp_count, $nota_final, $condicion) {
    $this->SetFont('Arial', '', 8);

    // Nombre del alumno
    $this->Cell(55, 8, utf8_decode($nombre_completo), 1);

    // Notas del 1er Cuatrimestre
    foreach ($calificaciones[1]['tps'] as $tp) {
        $this->Cell(8, 8, $tp, 1);
    }
    $this->Cell(10, 8, $calificaciones[1]['parcial'], 1);
    $this->Cell(10, 8, $calificaciones[1]['recuperatorio'], 1);

    // Notas del 2do Cuatrimestre
    foreach ($calificaciones[2]['tps'] as $tp) {
        $this->Cell(8, 8, $tp, 1);
    }
    $this->Cell(10, 8, $calificaciones[2]['parcial'], 1);
    $this->Cell(10, 8, $calificaciones[2]['recuperatorio'], 1);

    // Calificación definitiva y condición
    $this->Cell(14, 8, $nota_final ?? '-', 1);// Asegúrate de que se muestra
    $this->Cell(15, 8, utf8_decode($condicion ?? '-'), 1);  // Asegúrate de que se muestra
    $this->Ln();
}


    
}





// Generar el PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->TableHeader($tp_count);

foreach ($alumnos as $dni => $alumno) {
    $pdf->TableRow(
        $alumno['nombre_completo'], 
        $alumno['cuatrimestres'], 
        $tp_count, 
        $alumno['nota_final'], 
        $alumno['condicion']
    );
}

$pdf->AddPage();
// Marcar que estamos en la última página antes de la salida
$isLastPage = true;

$pdf->Output('D', 'Planilla_Calificaciones_' . utf8_decode($datos_materia['materia']) . '.pdf');

?>
