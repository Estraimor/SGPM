<?php
require '../../../indexs/pdf/vendor/setasign/fpdf/fpdf.php';
include '../../../conexion/conexion.php'; // Conexión a la base de datos

// Obtener el legajo desde el formulario (POST o GET)
$legajo = $_POST['legajo'] ?? $_GET['legajo'] ?? '';

if ($legajo) {
    // Consulta para obtener los datos del alumno según el legajo
    $sql = "SELECT * FROM alumno WHERE legajo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $legajo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Asignar variables a partir de los datos de la base de datos
        $nombre_completo = $row['nombre_alumno'] . " " . $row['apellido_alumno'];
        $dni = $row['dni_alumno'];
        $cuil = $row['cuil'];
        $nacionalidad = $row['pais_nacimiento'];
        $domicilio = $row['calle_domicilio'];
        $barrio = $row['barrio_domicilio'];
        $ciudad = $row['ciudad_nacimiento'];
        $provincia = $row['provincia_nacimiento'];
        $telefono_celular = $row['celular'];
        $telefono_urgencias = $row['telefono_urgencias'];
        $observaciones = $row['observaciones'];
        $trabajo_hs = $row['Trabajo_Horario'];
        $ocupacion = $row['ocupacion'];
        $domicilio_laboral = $row['domicilio_laboral'];
        $horario_laboral_desde = $row['horario_laboral_desde'];
        $horario_laboral_hasta = $row['horario_laboral_hasta'];
        $titulo_secundario = $row['Titulo_secundario'];
        $escuela_secundaria = $row['escuela_secundaria'];
        $materias_adeuda = $row['materias_adeuda'];
        $fecha_estimacion = $row['fecha_estimacion'];
        $discapacidad = $row['discapacidad'];
        $original_titulo = $row['original_titulo'];
        $fotos = $row['fotos'];
        $folio = $row['folio'];
        $fotocopia_dni = $row['fotocopia_dni'];
        $fotocopia_partida_nacimiento = $row['fotocopia_partida_nacimiento'];
        $constancia_cuil = $row['constancia_cuil'];
        $correo = $row['correo'];
        $carrera_pdf = $row['carrera'];
        $nombre_inst = 'Instituto Superior Politécnico Misiones Nº 1';

        // Clase personalizada para el PDF
        class PDF extends FPDF {
            function Header() {
                global $nombre_inst, $legajo;

                // Logo
                if (file_exists('../../../imagenes/politecnico.png')) {
                    $this->Image('../../../imagenes/politecnico.png', 10, 10, 28);
                }

                // Título "Inscripción [Año Actual]"
                $current_year = 2025;
                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1', "Inscripción $current_year"), 0, 1, 'C');

                // Subtítulo "Ficha de datos Personales"
                $this->SetFont('Arial', 'B', 14);
                $this->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1', 'Ficha de datos Personales'), 0, 1, 'C');

                $this->Ln(5); // Espacio después del subtítulo

                // Texto "INGRESANTE CICLO LECTIVO" y "LEGAJO N°"
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1', "INGRESANTE CICLO LECTIVO $current_year   LEGAJO N°: $legajo"), 0, 1, 'C');

                // Cuadro para la foto en el margen superior derecho
                $this->SetDrawColor(0, 0, 0); // Color de línea del borde (negro)
                $this->Rect(170, 2, 30, 40); // Ajuste de la posición del cuadro para la foto

                // Texto dentro del cuadro de la foto
                $this->SetXY(170, 15); // Ajustar la posición del texto dentro del cuadro (centrado verticalmente)
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(30, 10, iconv('UTF-8', 'ISO-8859-1', 'FOTO'), 0, 0, 'C');

                $this->Ln(28); // Espacio después del cuadro de la foto
            }

            // Función para crear un rectángulo con líneas punteadas
            function DottedRect($x, $y, $w, $h) {
                $this->SetXY($x, $y);
                $this->SetLineWidth(0.5);
                for ($i = 0; $i < $w; $i += 2) {
                    $this->Line($x + $i, $y, $x + $i + 1, $y); // Línea superior
                    $this->Line($x + $i, $y + $h, $x + $i + 1, $y + $h); // Línea inferior
                }
                for ($i = 0; $i < $h; $i += 2) {
                    $this->Line($x, $y + $i, $x, $y + $i + 1); // Línea izquierda
                    $this->Line($x + $w, $y + $i, $x + $w, $y + $i + 1); // Línea derecha
                }
            }

            // Función para dibujar un checkmark
            function drawCheckmark($x, $y, $size) {
                $this->Line($x, $y, $x + $size / 3, $y + $size / 2); // Línea descendente izquierda
                $this->Line($x + $size / 3, $y + $size / 2, $x + $size, $y - $size / 2); // Línea ascendente derecha
            }

            // Función para dibujar un cuadro vacío
            function drawEmptyBox($x, $y, $size) {
                $this->Rect($x, $y - $size / 2, $size, $size);
            }
        }

        // Crear el PDF
        $pdf = new PDF();
        $pdf->AddPage();

        // Contenido del PDF

        // Encabezado de "Datos Personales"
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(255, 220, 200);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS PERSONALES COMPLETOS"), 1, 1, 'L', true);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "DNI Nº: $dni"), 1);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "CUIL Nº: $cuil"), 1, 1);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Apellidos Completos: " . $row['apellido_alumno']), 1, 1);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Nombres Completos: " . $row['nombre_alumno']), 1, 1);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Ciudad y Fecha de Nacimiento: $ciudad, " . $row['fecha_nacimiento']), 1, 1);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Provincia de Nacimiento: $provincia"), 1);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "País de Nacimiento: Argentina"), 1, 1);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Nacionalidad: $nacionalidad"), 1, 1);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "¿Posee alguna discapacidad? :  $discapacidad"), 1, 1);

        $pdf->Ln(1); // Reducir espacio

        // Encabezado de "Datos Nivel Secundario/Terciario"
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(255, 220, 200);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS NIVEL SECUNDARIO/TERCIARIO"), 1, 1, 'L', true);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(86.66, 6, iconv('UTF-8', 'ISO-8859-1', "Título de Nivel Medio/Superior: $titulo_secundario"), 1, 0);
        $pdf->Cell(40, 6, iconv('UTF-8', 'ISO-8859-1', "Adeuda materias: $materias_adeuda"), 1, 0);
        $pdf->Cell(63.34, 6, iconv('UTF-8', 'ISO-8859-1', "Fecha Estimada: $fecha_estimacion "), 1, 1); 
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Otorgado por Escuela: $escuela_secundaria"), 1, 1); 

        $pdf->Ln(1); // Reducir espacio

        // Encabezado de "Datos Laborales"
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(255, 220, 200);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS LABORALES"), 1, 1, 'L', true);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(60, 6, iconv('UTF-8', 'ISO-8859-1', "Trabaja: " . (!empty($ocupacion) ? "Sí" : "No")), 1);
        $pdf->Cell(130, 6, iconv('UTF-8', 'ISO-8859-1', "Ocupación: $ocupacion"), 1, 1);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Domicilio Laboral: $domicilio_laboral"), 1);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Horario Laboral: $horario_laboral_desde - $horario_laboral_hasta"), 1, 1);

        $pdf->Ln(1); // Reducir espacio

        // Encabezado de "Domicilio Real"
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(255, 220, 200);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DOMICILIO REAL"), 1, 1, 'L', true);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Calle: $domicilio"), 1);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Número: " . $row['numeracion_domicilio']), 1, 1);
        $pdf->Cell(63, 6, iconv('UTF-8', 'ISO-8859-1', "Barrio: $barrio"), 1, 0);
        $pdf->Cell(63, 6, iconv('UTF-8', 'ISO-8859-1', "Provincia: $provincia"), 1, 0);
        $pdf->Cell(64, 6, iconv('UTF-8', 'ISO-8859-1', "Ciudad: $ciudad"), 1, 1);

        $pdf->Ln(1); // Reducir espacio

        // Encabezado de "Datos de Contacto"
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(255, 220, 200);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS DE CONTACTO"), 1, 1, 'L', true);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Teléfonos Celular: $telefono_celular"), 1);
        $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Teléfono de Urgencias: $telefono_urgencias"), 1, 1);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Correo electrónico: $correo"), 1, 1);

        $pdf->Ln(1); // Reducir espacio

        // Encabezado de "Matriculación"
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(255, 220, 200);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "MATRICULACIÓN"), 1, 1, 'L', true);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Carrera: $carrera_pdf"), 1, 1);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Firma del Estudiante: _________________________ "), 1, 1);

        // Certificación
        // Sección de "Documentación Presentada"
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(255, 220, 200);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DOCUMENTACIÓN PRESENTADA"), 0, 1, 'C', true);

$pdf->SetFont('Arial', '', 8);
$row_height = 6; // Altura de cada fila

// Función para determinar si el requisito fue presentado
function requisito_presentado($campo) {
    return (!empty($campo) && $campo !== '0') ? 'Presentó requisito' : 'No presentó requisito';
}

// Original Título
$pdf->Cell(63, $row_height, iconv('UTF-8', 'ISO-8859-1', "Original Título: " . requisito_presentado($row['original_titulo'])), 1, 0);
$pdf->Cell(63, $row_height, iconv('UTF-8', 'ISO-8859-1', "Fotos: " . requisito_presentado($row['fotos'])), 1, 0);
$pdf->Cell(64, $row_height, iconv('UTF-8', 'ISO-8859-1', "Pago: " . ($row['Pago'] == '1' ? 'Sí pagó' : 'No pagó')), 1, 1);

// Folio
$pdf->Cell(63, $row_height, iconv('UTF-8', 'ISO-8859-1', "Folio: " . requisito_presentado($row['folio'])), 1, 0);
$pdf->Cell(63, $row_height, iconv('UTF-8', 'ISO-8859-1', "Fotocopia DNI: " . requisito_presentado($row['fotocopia_dni'])), 1, 0);
$pdf->Cell(64, $row_height, iconv('UTF-8', 'ISO-8859-1', "Fotocopia Partida Nacimiento: " . requisito_presentado($row['fotocopia_partida_nacimiento'])), 1, 1);

// Constancia CUIL
$pdf->Cell(190, $row_height, iconv('UTF-8', 'ISO-8859-1', "Constancia CUIL: " . requisito_presentado($row['constancia_cuil'])), 1, 1);


        // Observaciones
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "OBSERVACIONES"), 1, 1, 'L', true);

        $pdf->SetFont('Arial', '', 7);
        $pdf->MultiCell(190, 6, iconv('UTF-8', 'ISO-8859-1', "$observaciones"), 1, 'L');


        // Aporte voluntario y firma con recuadros punteados
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 7);

            // Recuadro punteado para el texto
            $x = 10; 
            $y = $pdf->GetY();
            $w = 140; // Ancho reducido para evitar que el texto se salga
            $h = 20; // Reducir altura para ajustar mejor
            $pdf->DottedRect($x, $y, $w, $h);
            $pdf->SetXY($x, $y);
            $pdf->MultiCell($w, 5, iconv('UTF-8', 'ISO-8859-1', "Aporto voluntariamente, por única vez, la suma de \$15.000 para cubrir gastos de limpieza (lavandina, cera, trapos de piso, etc.) y gastos administrativos (hojas, carpetas, tóner, etc.)."));

            // Recuadro punteado para la firma
            $x_signature = $x + $w + 1; // Ajuste para el segundo recuadro punteado
            $pdf->DottedRect($x_signature, $y, 49, $h);

            // Ajustar la posición del texto dentro del recuadro punteado
            $pdf->SetXY($x_signature + 1, $y); // Desplazar ligeramente para asegurarse de que el texto comience en el margen
            $pdf->Cell(48, 10, iconv('UTF-8', 'ISO-8859-1', "Firma y Aclaración:"), 0, 1, 'L', false);

        // Generar el archivo PDF
        $nombre_archivo = 'Reporte_' . $nombre_completo . '_' . $legajo . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="' . $nombre_archivo . '"');
        header('Cache-Control: max-age=0');
        $pdf->Output('D', $nombre_archivo);

        exit;
    } else {
        echo "No se encontró el alumno con el legajo proporcionado.";
    }
} else {
    echo "No se ha proporcionado un legajo válido.";
}
