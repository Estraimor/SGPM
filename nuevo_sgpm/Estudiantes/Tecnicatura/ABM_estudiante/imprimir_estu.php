<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require './pdf/vendor/setasign/fpdf/fpdf.php';

$server = 'localhost';
$user = 'u756746073_root';
$pass = 'POLITECNICOmisiones2023.';
$bd = 'u756746073_politecnico';
$conexion = mysqli_connect($server, $user, $pass, $bd, '3306');

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

$nombre_inst = 'Instituto Superior Politécnico Misiones Nº 1';

class PDF extends FPDF {
    function Header() {
    global $nombre_inst, $legajo;

    // Logo
    if (file_exists('../../../../imagenes/politecnico.png')) {
        $this->Image('../../../../imagenes/politecnico.png', 10, 10, 28);
    }

    // Título "Inscripción [Año Actual]"
    $current_year = date('Y');
    $this->SetFont('Arial', 'B', 16); // Tamaño de la fuente del título ajustado
    $this->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1', "Inscripción $current_year"), 0, 1, 'C');

    // Subtítulo "Ficha de datos Personales"
    $this->SetFont('Arial', 'B', 14); // Tamaño de la fuente del subtítulo ajustado
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
}

// Crear el PDF
$pdf = new PDF();
$pdf->AddPage();

// Datos de prueba simulados
$nombre_completo = "Prueba Alumno";
$dni = "12345678";
$cuil = "20-12345678-9";
$fecha_nacimiento = "01/01/2000";
$nacionalidad = "Argentina";
$carrera = "Técnico Superior en Enfermería";
$legajo = "2024001";
$domicilio = "Calle Falsa 123";
$barrio = "Barrio Ejemplo";
$ciudad = "Posadas";
$provincia = "Misiones";
$telefono_celular = "123456789";
$telefono_urgencias = "987654321";
$correo = "prueba@example.com";
$observaciones = "Sin observaciones";
$trabajo_hs = "8 hs diarias";
$ocupacion = "Estudiante";
$domicilio_laboral = "Empresa Ejemplo";
$horario_laboral_desde = "08:00";
$horario_laboral_hasta = "16:00";
$titulo_secundario = "Título Ejemplo";
$escuela_secundaria = "Escuela Ejemplo";
$materias_adeuda = "Ninguna";
$fecha_estimacion = "01/12/2024";
$discapacidad = "Ninguna";

// Definir la variable $trabaja si aún no está definida
$trabaja = true;

// Variables para checkboxes (simulando si están o no completados)
$checkbox1 = true;
$checkbox2 = false;
$checkbox3 = true;
$checkbox4 = false;
$checkbox5 = true;
$checkbox6 = true;

$bg_color_r = 255;
$bg_color_g = 220;
$bg_color_b = 200;

// Estructura del PDF
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor($bg_color_r, $bg_color_g, $bg_color_b); // Aplicar color de fondo
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS PERSONALES COMPLETOS"), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "DNI Nº: $dni"), 1);
$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "CUIL Nº: $cuil"), 1, 1);

$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Apellidos Completos: $nombre_completo"), 1, 1);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Nombres Completos: $nombre_completo"), 1, 1);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Ciudad y Fecha de Nacimiento: $ciudad, $fecha_nacimiento"), 1, 1);
$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Provincia de Nacimiento: $provincia"), 1);
$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "País de Nacimiento: Argentina"), 1, 1);

$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Nacionalidad: $nacionalidad"), 1, 1);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "¿Posee alguna discapacidad? $discapacidad"), 1, 1);

$pdf->Ln(1); // Reducir espacio

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor($bg_color_r, $bg_color_g, $bg_color_b);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS NIVEL SECUNDARIO/TERCIARIO"), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(86.66, 6, iconv('UTF-8', 'ISO-8859-1', "Título de Nivel Medio/Superior: $titulo_secundario"), 1, 0);
$pdf->Cell(40, 6, iconv('UTF-8', 'ISO-8859-1', "Adeuda materias: $materias_adeuda"), 1, 0); // Segunda celda, sin salto de línea
$pdf->Cell(63.34, 6, iconv('UTF-8', 'ISO-8859-1', "Fecha Estimada: "), 1, 1); // Tercera celda, con salto de línea
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Otorgado por Escuela:"), 1, 1); // Celda en la fila siguiente, ocupando todo el ancho

$pdf->Ln(1); // Reducir espacio

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor($bg_color_r, $bg_color_g, $bg_color_b);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS LABORALES"), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(60, 6, iconv('UTF-8', 'ISO-8859-1', "Trabaja: " . ($trabaja ? "Sí" : "No")), 1);
$pdf->Cell(130, 6, iconv('UTF-8', 'ISO-8859-1', "Ocupación: $ocupacion"), 1, 1);

$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Domicilio Laboral: $domicilio_laboral"), 1);
$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Horario Laboral: $horario_laboral_desde - $horario_laboral_hasta"), 1, 1);

$pdf->Ln(1); // Reducir espacio

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor($bg_color_r, $bg_color_g, $bg_color_b);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DOMICILIO REAL"), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Calle: $domicilio"), 1);
$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Número:"), 1, 1);

$pdf->Cell(63, 6, iconv('UTF-8', 'ISO-8859-1', "Barrio: $barrio"), 1, 0);
$pdf->Cell(63, 6, iconv('UTF-8', 'ISO-8859-1', "Provincia: $provincia"), 1, 0);
$pdf->Cell(64, 6, iconv('UTF-8', 'ISO-8859-1', "Ciudad: $ciudad"), 1, 1);



$pdf->Ln(1); // Reducir espacio

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor($bg_color_r, $bg_color_g, $bg_color_b);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS DE CONTACTO"), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Teléfonos Celular: $telefono_celular"), 1);
$pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Teléfono de Urgencias: $telefono_urgencias"), 1, 1);

$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Correo electrónico: $correo"), 1, 1);

$pdf->Ln(1); // Reducir espacio

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor($bg_color_r, $bg_color_g, $bg_color_b);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "MATRICULACIÓN"), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Carrera: $carrera"), 1, 1);

$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Firma del Estudiante: _________________________ "), 1, 1);

$pdf->Ln(2); // Reducir el espacio antes de la siguiente sección

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(255, 220, 200); // Color de fondo similar al de la imagen
$pdf->Cell(190, 5, iconv('UTF-8', 'ISO-8859-1', "DOCUMENTACIÓN PRESENTADA"), 0, 1, 'C', true);

$pdf->SetFont('Arial', '', 7); // Reducir tamaño de fuente

// Ancho de la primera columna (requisitos)
$col1_width = 134; // Ajuste del ancho para alinear mejor

// Ancho de la segunda columna (Apellidos y Nombres, DNI, Fecha)
$col2_width = 60;

// Ajuste de altura de las filas
$row_height = 5; // Ajuste para uniformidad

// Inicializar la posición Y
$y_position = $pdf->GetY();

// Función para dibujar un checkmark
function drawCheckmark($pdf, $x, $y, $size) {
    // Línea descendente izquierda
    $pdf->Line($x, $y, $x + $size / 3, $y + $size / 2);
    // Línea ascendente derecha
    $pdf->Line($x + $size / 3, $y + $size / 2, $x + $size, $y - $size / 2);
}

// Función para dibujar un cuadro vacío
function drawEmptyBox($pdf, $x, $y, $size) {
    $pdf->Rect($x, $y - $size / 2, $size, $size);
}
// Tamaño del checkmark/cuadro vacío
$checkSize = 3; // Tamaño pequeño para ajustar mejor dentro de la celda

// Ajuste en las coordenadas para eliminar el espacio
$checkOffsetX = 0.5; // Desplazar casi al borde izquierdo
$checkOffsetY = 2;  // Desplazar un poco hacia abajo
$textOffsetX = 0; // Reducir el espacio entre el checkbox y el texto

// Primera fila con el checkbox y los datos
$pdf->SetXY(10, $y_position);
$pdf->SetX(10); // Iniciar posición de escritura sin espacio
$pdf->Cell($checkSize + 2, $row_height, '', 1); // Crear una celda pequeña para el checkbox
$pdf->SetXY(10 + $checkOffsetX, $y_position); // Ajuste fino para el checkbox dentro de la celda
if ($checkbox1) {
    drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
} else {
    drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
}
$pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
$pdf->MultiCell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "Original y copia de Título Secundario o constancia de Título en trámite."), 1, 'L');
$new_y_position = $pdf->GetY();
$cell_height = $new_y_position - $y_position;
$pdf->SetXY(140, $y_position);
$pdf->Cell($col2_width, $cell_height, iconv('UTF-8', 'ISO-8859-1', "Apellidos y Nombres:"), 1, 1);

// Segunda fila
$y_position = $new_y_position;
$pdf->SetXY(10, $y_position);
$pdf->SetX(10); // Iniciar posición de escritura sin espacio
$pdf->Cell($checkSize + 2, $row_height, '', 1); // Crear una celda pequeña para el checkbox
$pdf->SetXY(10 + $checkOffsetX, $y_position); // Ajuste fino para el checkbox dentro de la celda
if ($checkbox2) {
    drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
} else {
    drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
}
$pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
$pdf->Cell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "Dos fotos de tamaño 4 x 4 cm."), 1, 0, 'L');
$pdf->Cell($col2_width, $row_height, iconv('UTF-8', 'ISO-8859-1', "DNI:"), 1, 1);

// Tercera fila
$y_position = $pdf->GetY();
$pdf->SetXY(10, $y_position);
$pdf->SetX(10); // Iniciar posición de escritura sin espacio
$pdf->Cell($checkSize + 2, $row_height, '', 1); // Crear una celda pequeña para el checkbox
$pdf->SetXY(10 + $checkOffsetX, $y_position); // Ajuste fino para el checkbox dentro de la celda
if ($checkbox3) {
    drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
} else {
    drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
}
$pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
$pdf->Cell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "1 Folio A4"), 1, 0, 'L');
$pdf->Cell($col2_width, $row_height, iconv('UTF-8', 'ISO-8859-1', "Fecha:"), 1, 1);

// Cuarta fila
$y_position = $pdf->GetY();
$pdf->SetXY(10, $y_position);
$pdf->SetX(10); // Iniciar posición de escritura sin espacio
$pdf->Cell($checkSize + 2, $row_height, '', 1); // Crear una celda pequeña para el checkbox
$pdf->SetXY(10 + $checkOffsetX, $y_position); // Ajuste fino para el checkbox dentro de la celda
if ($checkbox4) {
    drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
} else {
    drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
}
$pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
$pdf->Cell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "Fotocopia de ambos lados del DNI"), 1, 0, 'L');
$pdf->Cell($col2_width, $row_height, '', 1, 1); // Celda vacía para alinear correctamente

// Quinta fila
$y_position = $pdf->GetY();
$pdf->SetXY(10, $y_position);
$pdf->SetX(10); // Iniciar posición de escritura sin espacio
$pdf->Cell($checkSize + 2, $row_height, '', 1); // Crear una celda pequeña para el checkbox
$pdf->SetXY(10 + $checkOffsetX, $y_position); // Ajuste fino para el checkbox dentro de la celda
if ($checkbox5) {
    drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
} else {
    drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
}
$pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
$pdf->Cell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "Fotocopia de la partida de nacimiento."), 1, 0, 'L');
$pdf->Cell($col2_width, $row_height, '', 1, 1); // Celda vacía para alinear correctamente

// Sexta fila
$y_position = $pdf->GetY();
$pdf->SetXY(10, $y_position);
$pdf->SetX(10); // Iniciar posición de escritura sin espacio
$pdf->Cell($checkSize + 2, $row_height, '', 1); // Crear una celda pequeña para el checkbox
$pdf->SetXY(10 + $checkOffsetX, $y_position); // Ajuste fino para el checkbox dentro de la celda
if ($checkbox6) {
    drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
} else {
    drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
}
$pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
$pdf->Cell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "Constancia de CUIL."), 1, 0, 'L');
$pdf->Cell($col2_width, $row_height, '', 1, 1); // Celda vacía para alinear correctamente







// Observaciones


// Certificación (encerrar en un recuadro)
$pdf->Ln(1);
$pdf->SetFont('Arial', '', 7); // Reducir tamaño de fuente

// Iniciar una celda con borde y agregar salto de línea
$pdf->MultiCell(190, 4, iconv('UTF-8', 'ISO-8859-1', "La Rectoría del Instituto Superior Politécnico Misiones Nº 1 certifica que los datos anteriores son exactos y extiende la presente a pedido del/la interesado/a."), 1, 'L');

// Aporte voluntario y firma con recuadros punteados
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 7);

// Recuadro punteado para el texto
$x = 10; 
$y = $pdf->GetY();
$w = 140; // Ancho reducido para evitar que el texto se salga
$h = 20; // Reducir altura para ajustar mejor
$pdf->DottedRect($x, $y, $w, $h);
$pdf->SetXY($x, $y);
$pdf->MultiCell($w, 5, iconv('UTF-8', 'ISO-8859-1', "Aporto voluntariamente, por única vez, la suma de \$15.000 para cubrir gastos de limpieza (lavandina, cera, trapos de piso, etc.) y gastos administrativos (hojas, carpetas, tóner, etc.)."));

/// Recuadro punteado para la firma
$x_signature = $x + $w + 1; // Ajuste para el segundo recuadro punteado
$pdf->DottedRect($x_signature, $y, 49, $h);

// Ajustar la posición del texto dentro del recuadro punteado
$pdf->SetXY($x_signature + 1, $y); // Desplazar ligeramente para asegurarse de que el texto comience en el margen
$pdf->Cell(48, 10, iconv('UTF-8', 'ISO-8859-1', "Firma y Aclaración:"), 0, 1, 'L', false);


$pdf->Ln(13); // Espacio antes de la sección de observaciones
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "OBSERVACIONES"), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 7);
$pdf->MultiCell(190, 6, iconv('UTF-8', 'ISO-8859-1', "$observaciones"), 1, 'L');
// Asegúrate de limpiar el buffer de salida antes de generar el PDF
if (ob_get_length()) {
    ob_end_clean(); // Limpia cualquier contenido del buffer de salida
}

// Generar el nombre del archivo PDF
$nombre_archivo = 'Reporte_' . $nombre_completo . '_' . $legajo . '.pdf';

// Configurar el tipo de contenido y la descarga del archivo
header('Content-Type: application/pdf');
header('Content-Disposition: attachment;filename="' . $nombre_archivo . '"');
header('Cache-Control: max-age=0');

// Salida del PDF
$pdf->Output('D', $nombre_archivo);

// Asegúrate de finalizar el script después de la salida del PDF
exit();

?>
