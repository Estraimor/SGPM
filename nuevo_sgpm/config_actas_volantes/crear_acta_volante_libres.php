<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require '../../indexs/pdf/vendor/setasign/fpdf/fpdf.php';
include '../../conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['materia']) && isset($_POST['llamado']) && isset($_POST['tanda']) && isset($_POST['anio'])) {
    $materia_id = $_POST['materia'];
    $llamado = $_POST['llamado'];
    $tanda = $_POST['tanda'];
    $anio = $_POST['anio'];
    $nombre_inst = utf8_decode('Instituto Superior Politécnico Misiones Nº 1');

    // Consulta para obtener los datos de la materia, docente y carrera
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

   // Consulta para obtener los alumnos inscritos según los filtros y condición de "Regular"
    $query = "
        SELECT DISTINCT a.dni_alumno, CONCAT(a.apellido_alumno, ' ', a.nombre_alumno) AS nombre_completo
        FROM mesas_finales mf
        INNER JOIN alumno a ON mf.alumno_legajo = a.legajo
        INNER JOIN notas n ON mf.alumno_legajo = n.alumno_legajo AND mf.materias_idMaterias = n.materias_idMaterias
        INNER JOIN fechas_mesas_finales fmf ON mf.fechas_mesas_finales_idfechas_mesas_finales = fmf.idfechas_mesas_finales
        INNER JOIN tandas t ON fmf.tandas_idtandas = t.idtandas
        WHERE mf.materias_idMaterias = ?
        AND n.condicion = 'Libre'
        AND n.nota IS NOT NULL
        AND t.tanda = ?
        AND t.llamado = ?
        AND YEAR(t.fecha) = ?
    ";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("iiii", $materia_id, $tanda, $llamado, $anio);
    $stmt->execute();
    $result = $stmt->get_result();

    $alumnos = [];
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = $row;
    }

    // Crear el PDF
    class PDF extends FPDF {
        function Header() {
            global $nombre_inst, $datos_materia,$anio,$tanda;
            $this->SetFont('Arial', 'B', 12);
            $this->Image('../../imagenes/politecnico.png', 15, 10, 25); // Imagen del logo
            $this->Image('../../imagenes/CGE.png', 170, 10, 25); // Imagen del CGE en la esquina opuesta
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
    $mesActual = date('n'); // 'n' devuelve el mes sin ceros iniciales
    if ($mesActual == 11 || $mesActual == 12) {
        $encabezado = "MESA EXAMEN FINAL NOVIEMBRE - DICIEMBRE - $anio - ESTUDIANTES LIBRES";
    } elseif ($mesActual == 2 || $mesActual == 3) {
        $encabezado = "MESA EXAMEN FINAL FEBRERO - MARZO - $anio - ESTUDIANTES LIBRES";
    } elseif ($mesActual == 7 || $mesActual == 8) {
        $encabezado = "MESA EXAMEN FINAL JULIO - AGOSTO - $anio - ESTUDIANTES LIBRES";
    } else {
        $encabezado = "MESA EXAMEN FINAL - FECHA NO DISPONIBLE - ESTUDIANTES LIBRES";
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
            $this->Cell(0, 10, utf8_decode('N° Tanda : '). $tanda , 0, 1);
            $this->Ln(5);
        }

       function Footer() {
    global $conexion, $materia_id;

    // Obtener el número de la página actual
    $pageNo = $this->PageNo();
    // Obtener el número total de páginas
    $totalPages = $this->AliasNbPages() === '{nb}' ? $this->PageNo() : $this->AliasNbPages();

    // Solo mostrar el footer en:
    // - La única página si hay una sola
    // - O en la última página si hay varias
    if ($pageNo < $totalPages) {
        return; // No mostrar el footer si no es la última página
    }

    // Obtener la altura actual
    $yPosition = $this->GetY();

    // Determinar si hay espacio suficiente para el footer o si es necesario reducirlo
    $footerHeight = 40; // Altura estimada del footer
    if ($yPosition > ($this->GetPageHeight() - $footerHeight - 20)) {
        if ($pageNo > 1) {
            $this->AddPage(); // Agregar una nueva página si no hay espacio suficiente
        } else {
            $footerHeight = 30; // Reducir el tamaño del footer en la misma página
        }
    }

    // Posicionar el footer
    $this->SetY(-$footerHeight); 
    $this->SetFont('Arial', '', 9); // Tamaño de fuente reducido

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
    $this->Cell($lineWidth, 6, '', 'T', 0, 'C');
    $this->SetX(10 + $lineWidth + $spaceBetween);
    $this->Cell($lineWidth, 4, 'Presidente de Mesa', 0, 1, 'C');
    $this->SetX(10 + $lineWidth + $spaceBetween);
    $this->Cell($lineWidth, 4, $nombreCompleto, 0, 0, 'C');

    // Ajustar para que Vocal 1 y Vocal 2 no se superpongan
    $this->Ln(10);

    // Vocal 1
    $this->SetX(10);
    $this->Cell($lineWidth, 6, '', 'T', 0, 'C');
    $this->SetX(10);
    $this->Cell($lineWidth, 6, 'Vocal 1', 0, 0, 'C');

    // Vocal 2
    $this->SetX(10 + 2 * ($lineWidth + $spaceBetween));
    $this->Cell($lineWidth, 6, '', 'T', 0, 'C');
    $this->SetX(10 + 2 * ($lineWidth + $spaceBetween));
    $this->Cell($lineWidth, 6, 'Vocal 2', 0, 0, 'C');
}




       function Table($header, $data) {
    // Anchos de las columnas
    $w = array(6, 25, 90, 24, 24, 23); // Ajuste de los anchos (CALF. DEF reducido)
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
        $this->Cell($w[3] / 3, 6, '', 1); // Espacio para calificación ORAL N°
        $this->Cell(2 * $w[3] / 3, 6, '', 1); // Espacio para calificación ORAL LETRA
        $this->Cell($w[4] / 3, 6, '', 1); // Espacio para calificación ESCRITA N°
        $this->Cell(2 * $w[4] / 3, 6, '', 1); // Espacio para calificación ESCRITA LETRA
        $this->Cell($w[5], 6, '', 1); // Espacio para calificación definitiva
        $this->Ln();
    }
}

function AddTableHeader($header, $w) {
    // Configurar estilo de fuente para los encabezados principales
    $this->SetFont('Arial', 'B', 12);

    // Dibujar encabezados generales
    $this->Cell($w[0], 21, $header[0], 1, 0, 'C', false);
    $this->Cell($w[1], 21, $header[1], 1, 0, 'C', false);
    $this->Cell($w[2], 21, $header[2], 1, 0, 'C', false);

    // "Calificación" abarcando dos filas (Oral y Escrita)
    $this->Cell($w[3] + $w[4], 7, utf8_decode('CALIFICACIÓN'), 1, 0, 'C', false);
    $this->Cell($w[5], 21, utf8_decode('CALF. DEF'), 1, 0, 'C', false);
    $this->Ln(7);

    // Dibujar subencabezados "Oral" y "Escrita"
    $this->SetFont('Arial', 'B', 10);
    $this->Cell($w[0], 7, '', 0, 0, 'C', false);
    $this->Cell($w[1], 7, '', 0, 0, 'C', false);
    $this->Cell($w[2], 7, '', 0, 0, 'C', false);

    // Subencabezados dentro de "CALIFICACIÓN"
    $this->Cell($w[3], 7, utf8_decode('ORAL'), 1, 0, 'C', false);
    $this->Cell($w[4], 7, utf8_decode('ESCRITO'), 1, 0, 'C', false);

    $this->Ln(7);

    // Subencabezados "N°" y "LETRA" debajo de "Oral" y "Escrita"
    $this->SetFont('Arial', 'B', 9);
    $this->Cell($w[0], 7, '', 0, 0, 'C', false);
    $this->Cell($w[1], 7, '', 0, 0, 'C', false);
    $this->Cell($w[2], 7, '', 0, 0, 'C', false);

    // "N°" y "LETRA" debajo de "Oral"
    $this->Cell($w[3] / 3, 7, utf8_decode('N°'), 1, 0, 'C', false); // N° más pequeño
    $this->Cell(2 * $w[3] / 3, 7, utf8_decode('LETRA'), 1, 0, 'C', false); // LETRA más grande

    // "N°" y "LETRA" debajo de "Escrita"
    $this->Cell($w[4] / 3, 7, utf8_decode('N°'), 1, 0, 'C', false); // N° más pequeño
    $this->Cell(2 * $w[4] / 3, 7, utf8_decode('LETRA'), 1, 0, 'C', false); // LETRA más grande

    // Espacio para "CALF. DEF"
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
