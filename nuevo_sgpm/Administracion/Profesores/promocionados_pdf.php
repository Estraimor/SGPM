<?php
require '../../../indexs/pdf/vendor/setasign/fpdf/fpdf.php';
include '../../../conexion/conexion.php';

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

function convertir($texto) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
}

if (isset($_GET['materia'])) {
    $materia_id = $_GET['materia'];
    $anio_actual = date("Y");
    $nombre_inst = convertir('Instituto Superior Politécnico Misiones Nº 1');

    $query_materia = "
        SELECT m.Nombre as materia, p.nombre_profe, p.apellido_profe, c.nombre_carrera as carrera
        FROM materias m
        INNER JOIN profesor p ON m.profesor_idProrfesor = p.idProrfesor
        INNER JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
        WHERE m.idMaterias = ?
    ";
    $stmt_materia = $conexion->prepare($query_materia);
    $stmt_materia->bind_param("i", $materia_id);
    $stmt_materia->execute();
    $result_materia = $stmt_materia->get_result();
    $datos_materia = $result_materia->fetch_assoc();

    if (!$datos_materia) {
        die("Error: No se encontraron datos para la materia seleccionada.");
    }

    $query = "
        SELECT a.dni_alumno, CONCAT(a.apellido_alumno, ' ', a.nombre_alumno) AS nombre_completo, n.nota_final
        FROM notas n
        INNER JOIN alumno a ON n.alumno_legajo = a.legajo
        WHERE n.materias_idMaterias = ? AND n.condicion = 'Promocion' AND YEAR(n.fecha) = ?
    ";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ii", $materia_id, $anio_actual);
    $stmt->execute();
    $result = $stmt->get_result();

    $alumnos = [];
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = $row;
    }

    class PDF extends FPDF {
        function Header() {
            global $nombre_inst, $datos_materia;
            $this->SetFont('Arial', 'B', 12);
            $this->Image('../../../imagenes/politecnico.png', 15, 10, 25);
            $this->Image('../../../imagenes/CGE.png', 170, 10, 25);
            $this->Cell(0, 10, $nombre_inst, 0, 1, 'C');
            $this->Ln(10);
            $nombre_titu = convertir('ACTA VOLANTE DE EXÁMENES');
            $this->Cell(0, 10, $nombre_titu, 0, 1, 'C');
            $this->Ln(5);

            $this->SetFont('Arial', '', 13);
            $this->Line(15, 40, 180, 40);
            $this->Cell(0, 10, convertir('Estudiantes Promocionados ' . date("Y")), 0, 1, 'C');
            $this->Line(15, 55, 180, 55);
            $this->Ln(10);

            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 10, 'Asignatura: ' . convertir($datos_materia['materia']), 0, 1);
            $this->Cell(0, 10, 'Docente: ' . convertir($datos_materia['nombre_profe'] . ' ' . $datos_materia['apellido_profe']), 0, 1);
            $this->Cell(0, 10, 'Carrera: ' . convertir($datos_materia['carrera']), 0, 1);
            $this->Ln(5);
        }

       function Footer() {
    global $conexion, $materia_id;

    // Configuración del tamaño del footer
    $this->SetY(-40); // Coloca el footer a 40 puntos desde el final de la página
    $this->SetFont('Arial', '', 9); // Tamaño y estilo de la fuente

    // Ancho total disponible para las columnas
    $totalWidth = $this->GetPageWidth() - 20;
    $lineWidth = ($totalWidth - 40) / 3;
    $spaceBetween = ($totalWidth - 3 * $lineWidth) / 2;

    // Consulta SQL para obtener el nombre del profesor
    $query = "SELECT nombre_profe, apellido_profe FROM profesor WHERE idProrfesor = (SELECT profesor_idProrfesor FROM materias WHERE idMaterias = ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $materia_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profesor = $result->fetch_assoc();

    $nombreCompleto = ($profesor ? convertir($profesor['nombre_profe'] . ' ' . $profesor['apellido_profe']) : 'Información no disponible');

    // Configuración del Presidente de Mesa
    $this->SetX(10 + $lineWidth + $spaceBetween);
    $this->Cell($lineWidth, 6, '', 'T', 0, 'C'); // Línea superior
    $this->Ln(2); // Espacio entre la línea y el texto
    $this->SetX(10 + $lineWidth + $spaceBetween);
    $this->Cell($lineWidth, 4, 'Presidente de Mesa', 0, 1, 'C');
    $this->SetX(10 + $lineWidth + $spaceBetween);
    $this->Cell($lineWidth, 4, $nombreCompleto, 0, 0, 'C');

    // Separación entre Presidente de Mesa y Vocales
    $this->Ln(10);

    // Configuración de Vocal 1
    $this->SetX(10);
    $this->Cell($lineWidth, 6, '', 'T', 0, 'C'); // Línea superior
    $this->Ln(2); // Espacio entre la línea y el texto
    $this->SetX(10);
    $this->Cell($lineWidth, 4, 'Vocal 1', 0, 0, 'C');

    // Configuración de Vocal 2
    $this->SetX(10 + 2 * ($lineWidth + $spaceBetween));
    $this->Cell($lineWidth, 6, '', 'T', 0, 'C'); // Línea superior
    $this->Ln(2); // Espacio entre la línea y el texto
    $this->SetX(10 + 2 * ($lineWidth + $spaceBetween));
    $this->Cell($lineWidth, 4, 'Vocal 2', 0, 0, 'C');
}






        function Table($header, $data) {
    // Anchos de las columnas
    $w = array(6, 25, 90, 20, 20, 25); // Ajuste de los anchos
    $this->SetFont('Arial', 'B', 12);

    // Cabecera
    $this->AddTableHeader($header, $w);

    // Datos
    $count = 1;
    foreach ($data as $row) {
        // Verificar si queda suficiente espacio en la página para otra fila
        if ($this->GetY() > ($this->GetPageHeight() - 60)) {
            $this->AddPage(); // Añadir nueva página
            $this->AddTableHeader($header, $w); // Repetir encabezado de la tabla
        }

        // Configurar estilo de fuente para las filas
        $this->SetFont('Arial', '', 12);

        // Dibujar fila de datos
        $this->Cell($w[0], 6, $count++, 1);
        $this->Cell($w[1], 6, $row['dni_alumno'], 1);
        $this->Cell($w[2], 6, utf8_decode($row['nombre_completo']), 1);
        $this->Cell($w[3], 6, '', 1); // Espacio para calificación N°
        $this->Cell($w[4], 6, '', 1); // Espacio para calificación LETRA
        $this->Cell($w[5], 6, $row['nota_final'], 1); // Espacio para calificación definitiva
        $this->Ln();
    }
}

function AddTableHeader($header, $w) {
    // Configurar estilo de fuente para los encabezados
    $this->SetFont('Arial', 'B', 12);

    for ($i = 0; $i < 3; $i++) {
        $this->Cell($w[$i], 14, $header[$i], 1, 0, 'C', false);
    }
    $this->Cell($w[3] + $w[4], 7, utf8_decode('CALIFICACIÓN'), 1, 0, 'C', false);
    $this->Cell($w[5], 14, utf8_decode('CALF. DEF'), 1, 0, 'C', false);
    $this->Ln(7);

    // Subencabezados de calificación
    $this->Cell($w[0], 7, '', 0, 0, 'C', false);
    $this->Cell($w[1], 7, '', 0, 0, 'C', false);
    $this->Cell($w[2], 7, '', 0, 0, 'C', false);
    $this->Cell($w[3], 7, utf8_decode('N°'), 1, 0, 'C', false);
    $this->Cell($w[4], 7, utf8_decode('LETRA'), 1, 0, 'C', false);
    $this->Cell($w[5], 7, '', 0, 0, 'C', false);
    $this->Ln();
}

        function AddTomoFolioFecha() {
    // Obtener la fecha actual de Buenos Aires
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fechaActual = date('d/m/Y'); // Formato DD/MM/AAAA

    // Posicionar el cuadro de "TOMO", "FOLIO" y "FECHA" justo debajo de la tabla de datos
    $this->Ln(5); // Añadir espacio después de la tabla de datos
    $this->SetX(10); // Ajusta la posición X para el cuadro

    // Celda para TOMO
    $this->Cell(30, 7, 'TOMO', 1, 0, 'C');
    $this->Cell(30, 7, '', 1, 1, 'C'); // Espacio para el texto del TOMO

    // Celda para FOLIO
    $this->Cell(30, 7, 'FOLIO', 1, 0, 'C');
    $this->Cell(30, 7, '', 1, 1, 'C'); // Espacio para el texto del FOLIO

    // Celda para FECHA
    $this->Cell(30, 7, 'FECHA', 1, 0, 'C');
    $this->Cell(30, 7, $fechaActual, 1, 1, 'C'); // Mostrar la fecha actual
        }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $nombreTitulo = utf8_decode('ACTA VOLANTE DE EXÁMENES');
    $enumeracion = utf8_decode('N°');

    // Encabezados de la tabla
    $header = array($enumeracion, 'D.N.I', 'NOMBRE COMPLETO', 'CALIFICACIÓN');

    // Generar la tabla en el PDF
    $pdf->Table($header, $alumnos);

    // Añadir el cuadro de TOMO, FOLIO, FECHA
    $pdf->AddTomoFolioFecha();

    // Salida del PDF como descarga
    $pdf->Output('D', 'Acta_Volante_Examenes_' . utf8_decode($datos_materia['materia']) . '_' . utf8_decode($datos_materia['carrera']) . '.pdf');
} else {
    echo "Error: Materia no seleccionada.";
}
?>
