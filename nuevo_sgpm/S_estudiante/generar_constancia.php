<?php
session_start();
include '../../conexion/conexion.php';
require '../../indexs/pdf/vendor/setasign/fpdf/fpdf.php';

class PDF extends FPDF {
    function Header() {
        global $nombre_inst;
        $this->SetFont('Arial', 'B', 12);
        $this->Image('../../imagenes/politecnico.png', 10, 10, 28);
        $this->Cell(0, 10, $nombre_inst, 0, 1, 'C');
        $this->Ln(10);
    }

    

    function truncarTexto($texto, $longitudMaxima) {
        return (strlen($texto) > $longitudMaxima) ? substr($texto, 0, $longitudMaxima) . '...' : $texto;
    }

    function obtenerFechaActual() {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        setlocale(LC_TIME, 'es_ES.UTF-8');
        return strftime('%d de %B de %Y');
    }

    function agregarFirmas() {
        // Imagen de "sello_obal" en el centro de la página
        $this->SetY(-90); // Ajusta la posición vertical para la imagen
        $this->SetX(80); // Centra la imagen horizontalmente
        $this->Image('../../imagenes/sello_obal.jpg', 83, $this->GetY(), 40); // Imagen en el centro

       

       
    }
}

$alumno_legajo = $_SESSION['id'];
if ($alumno_legajo) {
    $nombre_inst = utf8_decode('Instituto Superior Politécnico Misiones Nº 1');

    // Obtener datos del alumno
    $query = "SELECT nombre_alumno, apellido_alumno, dni_alumno FROM alumno WHERE legajo = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $alumno_legajo);
    $stmt->execute();
    $stmt->bind_result($alumno_nombre, $alumno_apellido, $alumno_dni);
    $stmt->fetch();
    $stmt->close();

    // Obtener datos de las materias inscritas con fecha y tanda desde `tandas`
    $query = "SELECT m.Nombre, t.fecha, t.llamado, t.tanda 
          FROM mesas_finales mf
          JOIN materias m ON mf.materias_idMaterias = m.idMaterias
          JOIN fechas_mesas_finales fm ON mf.fechas_mesas_finales_idfechas_mesas_finales = fm.idfechas_mesas_finales
          JOIN tandas t ON fm.tandas_idtandas = t.idtandas
          WHERE mf.alumno_legajo = ?
          AND (
              (MONTH(t.fecha) IN (2, 3)) OR 
              (MONTH(t.fecha) IN (7, 8)) OR 
              (MONTH(t.fecha) IN (11, 12))
          )
          AND YEAR(t.fecha) = YEAR(CURDATE())";
              
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $alumno_legajo);
    $stmt->execute();
    $stmt->bind_result($nombre_materia, $fecha, $llamado, $tanda);

    $materias_inscritas = [];
    while ($stmt->fetch()) {
        $materias_inscritas[] = [
            'Nombre' => $nombre_materia,
            'fecha' => $fecha,
            'llamado' => $llamado,
            'tanda' => $tanda
        ];
    }
    $stmt->close();

    // Definir la zona horaria y obtener el mes actual
date_default_timezone_set('America/Argentina/Buenos_Aires');
$mes_actual = date('n'); // Obtenemos el mes actual en formato numérico

// Texto del título
$nombre_Titulo = 'Constancia de Inscripción a Mesas de Exámenes';

// Determinar los meses en negrita si corresponde
$meses_negrita = '';
if (in_array($mes_actual, [11, 12])) {
    $meses_negrita = "Noviembre - Diciembre";
} elseif (in_array($mes_actual, [2, 3])) {
    $meses_negrita = "Febrero - Marzo";
} elseif (in_array($mes_actual, [7, 8])) {
    $meses_negrita = "Julio - Agosto";
}

// Concatenar el título con los meses en negrita si corresponde
if (!empty($meses_negrita)) {
    $nombre_Titulo .= " " . utf8_decode($meses_negrita);
}

// Generar el PDF de inscripción
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode($nombre_Titulo), 0, 1, 'C');

    // Texto introductorio
    $pdf->SetFont('Arial', '', 12);
    $texto_introductorio = utf8_decode("    El Instituto Superior Politécnico Misiones Nº 1 deja constancia que el estudiante $alumno_nombre $alumno_apellido, DNI: $alumno_dni, Se ha inscripto en las siguientes mesas de exámenes:");
    $pdf->MultiCell(0, 10, $texto_introductorio);
    $pdf->Ln(10);
    
// Configuración del encabezado de la tabla
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(133, 15, 'Unidad curricular', 1, 0, 'C', true); // Ajuste de ancho para "Unidad curricular"
$pdf->Cell(23, 15, 'Fecha', 1, 0, 'C', true);   // Ajuste de ancho para "Fecha"
$pdf->Cell(17, 15, 'Llamado', 1, 0, 'C', true); // Ajuste de ancho para "Llamado"
$pdf->Cell(16, 15, 'Tanda', 1, 1, 'C', true);   // Ajuste de ancho para "Tanda"

// Iteración de las materias inscritas
foreach ($materias_inscritas as $materia) {
    // Guardamos la posición inicial
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Medimos la altura del MultiCell con un cálculo previo
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(133, 7.5, utf8_decode($materia['Nombre']), 1, 'L');
    $altura = $pdf->GetY() - $y; // Calculamos la altura que ocupó el MultiCell

    // Regresamos a la posición inicial para las otras columnas de la misma fila
    $pdf->SetXY($x + 133, $y); // Posición para la columna "Fecha"
    $pdf->Cell(23, $altura, date("d-m-Y", strtotime($materia['fecha'])), 1, 0, 'C');
    $pdf->Cell(17, $altura, $materia['llamado'], 1, 0, 'C');
    $pdf->Cell(16, $altura, $materia['tanda'], 1, 1, 'C');
}


    // Texto de cierre
    $pdf->Ln(10);
    $fecha_actual = $pdf->obtenerFechaActual();
    $texto_final = utf8_decode("    Se extiende la presente el día $fecha_actual en la ciudad de Posadas, Misiones, Argentina.");
    $pdf->MultiCell(0, 10, $texto_final);

    // Agregar firmas
    $pdf->agregarFirmas();

    // Configurar las cabeceras para la descarga del PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Constancia_Inscripcion_Unidades_Curriculares.pdf"');
    $pdf->Output('D', 'Constancia_Inscripcion_Unidades_Curriculares.pdf');
} else {
    echo "No hay datos de inscripción disponibles.";
}
?>
