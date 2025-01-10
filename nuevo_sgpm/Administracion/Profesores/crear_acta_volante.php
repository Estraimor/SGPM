<?php
require '../../../indexs/pdf/vendor/setasign/fpdf/fpdf.php';
include '../../../conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['materia'])) {
    $materia_id = $_POST['materia'];
    $nombre_inst = utf8_decode('Instituto Superior Politécnico Misiones Nº 1');

    // Consulta para obtener los datos de la materia y el docente
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
        echo "Error: No se encontraron datos para la materia seleccionada.";
        exit;
    }

    // Consulta para obtener los datos de los alumnos inscritos en la materia seleccionada
    $query = "
        SELECT a.dni_alumno, CONCAT(a.apellido_alumno, ' ', a.nombre_alumno) AS nombre_completo
        FROM mesas_finales mf
        INNER JOIN alumno a ON mf.alumno_legajo = a.legajo
        WHERE mf.materias_idMaterias = ?
    ";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $materia_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $alumnos = [];
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = $row;
    }

    // Crear el PDF
    class PDF extends FPDF {
        function Header() {
            global $nombre_inst, $datos_materia;
            $this->SetFont('Arial', 'B', 12);
            $this->Image('../../../imagenes/politecnico.png', 15, 10, 25); // Imagen del logo
            $this->Image('../../../imagenes/CGE.png', 170, 10, 25); // Imagen del CGE en la esquina opuesta
            $this->Cell(0, 10, $nombre_inst, 0, 1, 'C');
            $this->Ln(10);
            $nombre_titu = utf8_decode('ACTA VOLANTE DE EXÁMENES');
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, $nombre_titu , 0, 1, 'C');
            $this->Ln(5);
            
            // Texto y líneas superior e inferior
            $this->SetFont('Arial', '', 12);
            $this->Line(15, 40, 180, 40); // Línea superior
            // Obtén el mes actual
$mesActual = date('n'); // 'n' devuelve el mes sin ceros iniciales (1-12)

// Determina el texto del encabezado según el mes
if ($mesActual == 11 || $mesActual == 12) {
    $encabezado = 'MESA EXAMEN FINAL NOVIEMBRE - DICIEMBRE 2024 - ALUMNOS REGULARES - LIBRES';
} elseif ($mesActual == 2 || $mesActual == 3) {
    $encabezado = 'MESA EXAMEN FINAL FEBRERO - MARZO 2024 - ALUMNOS REGULARES - LIBRES';
} elseif ($mesActual == 7 || $mesActual == 8) {
    $encabezado = 'MESA EXAMEN FINAL JULIO - AGOSTO 2024 - ALUMNOS REGULARES - LIBRES';
} else {
    // Mensaje genérico o de advertencia si estamos fuera de los meses de examen
    $encabezado = 'MESA EXAMEN FINAL - FECHA NO DISPONIBLE - ALUMNOS REGULARES';
}

// Usar el texto determinado en el encabezado del PDF
$this->Cell(0, 8, $encabezado, 0, 1, 'C');

            $this->Line(15, 55, 180, 55); // Línea inferior
            $this->Ln(10);

            // Información de la asignatura, docente, etc.
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 10, 'Asignatura: ' . utf8_decode($datos_materia['materia']), 0, 1);
            $this->Cell(0, 10, 'Docente: ' . utf8_decode($datos_materia['nombre_profe'] . ' ' . $datos_materia['apellido_profe']), 0, 1);
            $this->Cell(0, 10, 'Carrera: ' . utf8_decode($datos_materia['carrera']), 0, 1);
            $this->Cell(0, 10, 'Cantidad llamados: ', 0, 1);
            $this->Ln(5);
        }

        function Footer() {
            global $conexion, $materia_id;

            $this->SetY(-45); // Ajusta la posición del pie de página
            $this->SetFont('Arial', '', 10);

            $totalWidth = $this->GetPageWidth() - 20; // Ancho total disponible
            $lineWidth = ($totalWidth - 40) / 3; // Ancho de cada línea con espacio entre ellas
            $spaceBetween = ($totalWidth - 3 * $lineWidth) / 2; // Espacio entre líneas

            // Consulta SQL para obtener el nombre del profesor
            $query = "SELECT nombre_profe, apellido_profe FROM profesor WHERE idProrfesor = (SELECT profesor_idProrfesor FROM materias WHERE idMaterias = ?)";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $materia_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $profesor = $result->fetch_assoc();

            $nombreCompleto = ($profesor ? utf8_decode($profesor['nombre_profe'] . ' ' . $profesor['apellido_profe']) : 'Información no disponible');

            // Configuración y visualización del nombre del presidente de mesa
            $this->SetX(10 + $lineWidth + $spaceBetween);
            $this->Cell($lineWidth, 10, '', 'T', 0, 'C');
            $this->SetX(10 + $lineWidth + $spaceBetween);
            $this->Cell($lineWidth, 5, 'Presidente de Mesa', 0, 1, 'C'); // Ajuste de la altura
            $this->SetX(10 + $lineWidth + $spaceBetween);
            $this->Cell($lineWidth, 5, $nombreCompleto, 0, 0, 'C'); // Ajuste de la altura

            // Bajar la posición Y para las líneas de los vocales
            $this->SetY($this->GetY() + 10); // Ajusta un poco más abajo para los vocales

            // Vocal 1
            $this->SetX(10);
            $this->Cell($lineWidth, 10, '', 'T', 0, 'C');
            $this->SetX(10);
            $this->Cell($lineWidth, 10, 'Vocal 1', 0, 0, 'C');

            // Vocal 2
            $this->SetX(10 + 2 * ($lineWidth + $spaceBetween));
            $this->Cell($lineWidth, 10, '', 'T', 0, 'C');
            $this->SetX(10 + 2 * ($lineWidth + $spaceBetween));
            $this->Cell($lineWidth, 10, 'Vocal 2', 0, 0, 'C');
        }

        function Table($header, $data) {
            // Anchos de las columnas
            $w = array(5, 25, 90, 20, 20, 25); // Ajuste de los anchos
            $this->SetFont('Arial', 'B', 12);

            // Cabecera
            for ($i = 0; $i < 3; $i++) {
                $this->Cell($w[$i], 14, $header[$i], 1, 0, 'C', false);
            }
            $this->Cell($w[3] + $w[4], 7, utf8_decode('CALIFICACIÓN'), 1, 0, 'C', false);
            $this->Cell($w[5], 14, utf8_decode('CALF. DEF'), 1, 0, 'C', false);
            $this->Ln(7);
            $this->Cell($w[0], 7, '', 0, 0, 'C', false);
            $this->Cell($w[1], 7, '', 0, 0, 'C', false);
            $this->Cell($w[2], 7, '', 0, 0, 'C', false);
            $this->Cell($w[3], 7, utf8_decode('N°'), 1, 0, 'C', false);
            $this->Cell($w[4], 7, utf8_decode('LETRA'), 1, 0, 'C', false);
            $this->Cell($w[5], 7, '', 0, 0, 'C', false);
            $this->Ln();

            // Datos
            $this->SetFont('Arial', '', 12);
            $count = 1;
            foreach ($data as $row) {
                $this->Cell($w[0], 6, $count++, 1);
                $this->Cell($w[1], 6, $row['dni_alumno'], 1);
                $this->Cell($w[2], 6, utf8_decode($row['nombre_completo']), 1);
                $this->Cell($w[3], 6, '', 1); // Espacio para calificación N°
                $this->Cell($w[4], 6, '', 1); // Espacio para calificación LETRA
                $this->Cell($w[5], 6, '', 1); // Espacio para calificación definitiva
                $this->Ln();
            }
        }

        function AddTomoFolioFecha() {
            // Posicionar el cuadro de "TOMO", "FOLIO" y "FECHA" justo debajo de la tabla de datos
            $this->Ln(5); // Añadir espacio después de la tabla de datos
            $this->SetX(10); // Ajusta la posición X para el cuadro
            $this->Cell(30, 7, 'TOMO', 1, 0, 'C');
            $this->Cell(30, 7, '', 1, 1, 'C'); // Espacio para el texto del TOMO
            $this->Cell(30, 7, 'FOLIO', 1, 0, 'C');
            $this->Cell(30, 7, '', 1, 1, 'C'); // Espacio para el texto del FOLIO
            $this->Cell(30, 7, 'FECHA', 1, 0, 'C');
            $this->Cell(30, 7, '', 1, 1, 'C'); // Espacio para el texto de la FECHA
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
