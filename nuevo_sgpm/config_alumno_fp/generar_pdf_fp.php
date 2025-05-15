<?php
//generar_pdf_fp.php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../indexs/pdf/vendor/setasign/fpdf/fpdf.php';
include '../../conexion/conexion.php'; // Conexión a la base de datos

$legajo = $_POST['legajo'] ?? $_GET['legajo'] ?? '';
$es_tecnicatura = false;
$datos_tecnicatura = [];
$datos_fp = [];

if (!$legajo) {
    die("No se ha proporcionado un legajo válido.");
}

// Buscar en la tabla alumnos_fp
$stmt_fp = $conexion->prepare("SELECT * FROM alumnos_fp WHERE legajo_afp = ?");
$stmt_fp->bind_param("i", $legajo);
$stmt_fp->execute();
$res_fp = $stmt_fp->get_result();
$datos_fp = $res_fp->fetch_assoc();
$stmt_fp->close();

if (!$datos_fp) {
    die("No se encontró el alumno con ese legajo en Formación Profesional.");
}

// Si tiene alumno_legajo vinculado, buscar en la tabla alumno
if (!empty($datos_fp['alumno_legajo'])) {
    $stmtTec = $conexion->prepare("SELECT * FROM alumno WHERE legajo = ?");
    $stmtTec->bind_param("i", $datos_fp['alumno_legajo']);
    $stmtTec->execute();
    $resTec = $stmtTec->get_result();

    if ($resTec && $resTec->num_rows > 0) {
        $es_tecnicatura = true;
        $datos_tecnicatura = $resTec->fetch_assoc();
    }

    $stmtTec->close();
}

// Variables comunes para PDF
if ($es_tecnicatura) {
    $nombre_alu = $datos_tecnicatura['nombre_alumno'] ?? '';
    $apellido_alu = $datos_tecnicatura['apellido_alumno'] ?? '';
    $dni_alu = $datos_tecnicatura['dni_alumno'] ?? '';
    $cuil = $datos_tecnicatura['cuil'] ?? '';
    $edad = $datos_tecnicatura['edad'] ?? '';
    $fecha_nacimiento = $datos_tecnicatura['fecha_nacimiento'] ?? '';
    $discapacidad = $datos_tecnicatura['discapacidad'] ?? '';
    $titulo_secundario = $datos_tecnicatura['Titulo_secundario'] ?? '';
    $escuela_secundaria = $datos_tecnicatura['escuela_secundaria'] ?? '';
    $materias_adeuda = $datos_tecnicatura['materias_adeuda'] ?? '';
    $fecha_estimacion = $datos_tecnicatura['fecha_estimacion'] ?? '';
    $ocupacion = $datos_tecnicatura['ocupacion'] ?? '';
    $trabajo_hs = $datos_tecnicatura['Trabaja_Horario'] ?? '';
    $domicilio_laboral = $datos_tecnicatura['domicilio_laboral'] ?? '';
    $horario_laboral_desde = $datos_tecnicatura['horario_laboral_desde'] ?? '';
    $horario_laboral_hasta = $datos_tecnicatura['horario_laboral_hasta'] ?? '';
    $calle_domicilio = $datos_tecnicatura['calle_domicilio'] ?? '';
    $barrio_domicilio = $datos_tecnicatura['barrio_domicilio'] ?? '';
    $ciudad_domicilio = $datos_tecnicatura['ciudad_domicilio'] ?? '';
    $provincia_domicilio = $datos_tecnicatura['provincia_domicilio'] ?? '';
    $numeracion_domicilio = $datos_tecnicatura['numeracion_domicilio'] ?? '';
    $telefono_urgencias = $datos_tecnicatura['telefono_urgencias'] ?? '';
    $correo = $datos_tecnicatura['correo'] ?? '';
    $ciudad_nacimiento = $datos_tecnicatura['ciudad_nacimiento'] ?? '';
    $provincia_nacimiento = $datos_tecnicatura['provincia_nacimiento'] ?? '';
    $pais_nacimiento = $datos_tecnicatura['pais_nacimiento'] ?? '';
    $celular = $datos_tecnicatura['celular'] ?? '';
    $observaciones = $datos_tecnicatura['observaciones'] ?? '';
} else {
    $nombre_alu = $datos_fp['nombre_afp'] ?? '';
    $apellido_alu = $datos_fp['apellido_afp'] ?? '';
    $dni_alu = $datos_fp['dni_afp'] ?? '';
    $cuil = $datos_fp['cuil'] ?? '';
    $edad = $datos_fp['edad'] ?? '';
    $fecha_nacimiento = $datos_fp['fecha_nacimiento'] ?? '';
    $discapacidad = $datos_fp['discapacidad'] ?? '';
    $titulo_secundario = $datos_fp['Titulo_secundario'] ?? '';
    $escuela_secundaria = $datos_fp['escuela_secundaria'] ?? '';
    $materias_adeuda = $datos_fp['materias_adeuda'] ?? '';
    $fecha_estimacion = $datos_fp['fecha_estimacion'] ?? '';
    $ocupacion = $datos_fp['ocupacion'] ?? '';
    $trabajo_hs = $datos_fp['Trabaja_Horario'] ?? '';
    $domicilio_laboral = $datos_fp['domicilio_laboral'] ?? '';
    $horario_laboral_desde = $datos_fp['horario_laboral_desde'] ?? '';
    $horario_laboral_hasta = $datos_fp['horario_laboral_hasta'] ?? '';
    $calle_domicilio = $datos_fp['calle_domicilio'] ?? '';
    $barrio_domicilio = $datos_fp['barrio_domicilio'] ?? '';
    $ciudad_domicilio = $datos_fp['ciudad_domicilio'] ?? '';
    $provincia_domicilio = $datos_fp['provincia_domicilio'] ?? '';
    $numeracion_domicilio = $datos_fp['numeracion_domicilio'] ?? '';
    $telefono_urgencias = $datos_fp['telefono_urgencias'] ?? '';
    $correo = $datos_fp['correo'] ?? '';
    $ciudad_nacimiento = $datos_fp['ciudad_nacimiento'] ?? '';
    $provincia_nacimiento = $datos_fp['provincia_nacimiento'] ?? '';
    $pais_nacimiento = $datos_fp['pais_nacimiento'] ?? '';
    $celular = $datos_fp['celular'] ?? '';
    $observaciones = $datos_fp['observaciones'] ?? '';
}

// Requisitos para checkboxes
$original_titulo = $datos_fp['original_titulo'] ?? 0;
$fotos = $datos_fp['fotos'] ?? 0;
$folio = $datos_fp['folio'] ?? 0;
$fotocopia_dni = $datos_fp['fotocopia_dni'] ?? 0;
$fotocopia_partida_nacimiento = $datos_fp['fotocopia_partida_nacimiento'] ?? 0;
$constancia_cuil = $datos_fp['constancia_cuil'] ?? 0;
$pago = $datos_fp['Pago'] ?? 0;

// Carreras desde la tabla inscripcion_fp según legajo_afp
$carrera_pdf = [];

$stmtCarreras = $conexion->prepare("
    SELECT carreras.nombre_carrera 
    FROM inscripcion_fp 
    INNER JOIN carreras ON inscripcion_fp.carreras_idCarrera = carreras.idCarrera 
    WHERE inscripcion_fp.alumnos_fp_legajo_afp = ? AND inscripcion_fp.estado = 1
");
$stmtCarreras->bind_param("i", $legajo);
$stmtCarreras->execute();
$resCarreras = $stmtCarreras->get_result();

while ($fila = $resCarreras->fetch_assoc()) {
    $carrera_pdf[] = $fila['nombre_carrera'];
}
$stmtCarreras->close();

$carrera_pdf = implode(', ', $carrera_pdf);


// Edad exacta
function calcularEdad($fecha_nacimiento) {
    if (empty($fecha_nacimiento)) {
        return '-';
    }
    $fecha = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    return $hoy->diff($fecha)->y;
}
$edad_usuario = calcularEdad($fecha_nacimiento);

// Variables extra de usuario administrativo
$nombre_usuario = $_SESSION['nombre'] ?? '';
$apellido_usuario = $_SESSION['apellido'] ?? '';
$dni_usuario = $_SESSION['dni'] ?? '';

// Fecha actual
$fecha_actual = date("Y-m-d");



            // Generar el PDF con los datos recibidos por POST

            $nombre_inst = 'Instituto Superior Politécnico Misiones Nº 1';

            class PDF extends FPDF {
                function Header() {
                    global $nombre_inst, $legajo;

                    // Logo
                    if (file_exists('../../imagenes/politecnico.png')) {
                        $this->Image('../../imagenes/politecnico.png', 10, 10, 28);
                    }

                    // Título "Inscripción [Año Actual]"
                    $current_year = 2025;
                    $this->SetFont('Arial', 'B', 16);
                    $this->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1', "Inscripción FP $current_year"), 0, 1, 'C');

                    // Subtítulo "Ficha de datos Personales"
                    $this->SetFont('Arial', 'B', 14);
                    $this->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1', 'Ficha de datos Personales'), 0, 1, 'C');

                    $this->Ln(5); // Espacio después del subtítulo

                    // Texto "INGRESANTE CICLO LECTIVO" y "LEGAJO N°"
                    $this->SetFont('Arial', 'B', 12);
                    $this->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1', "INGRESANTE CICLO LECTIVO  $current_year   LEGAJO N°: $legajo"), 0, 1, 'C');

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

            // Utilizar las mismas variables de POST para los datos del PDF
            $nombre_completo = $nombre_alu . " " . $apellido_alu;
            $dni = $dni_alu;
            $cuil = $cuil;
            $nacionalidad = $pais_nacimiento; // Asumiendo nacionalidad, si no está en el formulario
            $domicilio = $calle_domicilio;
            $barrio = $barrio_domicilio;
            $ciudad = $ciudad_domicilio;
            $provincia = $provincia_domicilio;
            $telefono_celular = $celular;
            $telefono_urgencias = $telefono_urgencias;
            $observaciones = $observaciones;
            $trabajo_hs = $trabajo_hs;
            $ocupacion = $ocupacion;
            $domicilio_laboral = $domicilio_laboral;
            $horario_laboral_desde = $horario_laboral_desde;
            $horario_laboral_hasta = $horario_laboral_hasta;
            $titulo_secundario = $titulo_secundario;
            $escuela_secundaria = $escuela_secundaria;
            $materias_adeuda = $materias_adeuda;
            $fecha_estimacion = $fecha_estimacion;
            $discapacidad = $discapacidad;

            // Estructura del PDF
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(255, 220, 200); // Color de fondo similar al de la imagen
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS PERSONALES COMPLETOS"), 1, 1, 'L', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "DNI Nº: $dni"), 1);
            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "CUIL Nº: $cuil"), 1, 1);

            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Apellidos Completos: $apellido_alu"), 1, 1);
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Nombres Completos: $nombre_alu"), 1, 1);
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Ciudad y Fecha de Nacimiento: $ciudad, $fecha_nacimiento (Edad: $edad_usuario años)"), 1, 1);
            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Provincia de Nacimiento: $provincia"), 1);
            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "País de Nacimiento: Argentina"), 1, 1);

            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Nacionalidad: $nacionalidad"), 1, 1);
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "¿Posee alguna discapacidad? :  $discapacidad"), 1, 1);

            $pdf->Ln(1); // Reducir espacio

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(255, 220, 200);
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS NIVEL SECUNDARIO/TERCIARIO"), 1, 1, 'L', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(86.66, 6, iconv('UTF-8', 'ISO-8859-1', "Título de Nivel Medio/Superior: $titulo_secundario"), 1, 0);
            $pdf->Cell(40, 6, iconv('UTF-8', 'ISO-8859-1', "Adeuda materias: $materias_adeuda"), 1, 0);
            $pdf->Cell(63.34, 6, iconv('UTF-8', 'ISO-8859-1', "Fecha Estimada: $fecha_estimacion "), 1, 1); 
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Otorgado por Escuela: $escuela_secundaria"), 1, 1); 

            $pdf->Ln(1); // Reducir espacio

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(255, 220, 200);
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS LABORALES"), 1, 1, 'L', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(60, 6, iconv('UTF-8', 'ISO-8859-1', "Trabaja: " . (!empty($ocupacion) ? "Sí" : "No")), 1);
            $pdf->Cell(130, 6, iconv('UTF-8', 'ISO-8859-1', "Ocupación: $ocupacion"), 1, 1);

            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Domicilio Laboral: $domicilio_laboral"), 1);
            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Horario Laboral: $horario_laboral_desde - $horario_laboral_hasta"), 1, 1);

            $pdf->Ln(1); // Reducir espacio

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(255, 220, 200);
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DOMICILIO REAL"), 1, 1, 'L', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Calle: $domicilio"), 1);
            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Número: $numeracion_domicilio"), 1, 1);

            $pdf->Cell(63, 6, iconv('UTF-8', 'ISO-8859-1', "Barrio: $barrio"), 1, 0);
            $pdf->Cell(63, 6, iconv('UTF-8', 'ISO-8859-1', "Provincia: $provincia"), 1, 0);
            $pdf->Cell(64, 6, iconv('UTF-8', 'ISO-8859-1', "Ciudad: $ciudad"), 1, 1);

            $pdf->Ln(1); // Reducir espacio

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(255, 220, 200);
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "DATOS DE CONTACTO"), 1, 1, 'L', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Teléfonos Celular: $telefono_celular"), 1);
            $pdf->Cell(95, 6, iconv('UTF-8', 'ISO-8859-1', "Teléfono de Urgencias: $telefono_urgencias"), 1, 1);

            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Correo electrónico: $correo"), 1, 1);

            $pdf->Ln(1); // Reducir espacio

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(255, 220, 200);
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "MATRICULACIÓN"), 1, 1, 'L', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(190, 6, iconv('UTF-8', 'ISO-8859-1', "Carrera: $carrera_pdf"), 1, 1);

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
            if ($original_titulo) {
                drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
            } else {
                drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
            }
            $pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
            $pdf->MultiCell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "Original y copia de Título Secundario o constancia de Título en trámite."), 1, 'L');
            $new_y_position = $pdf->GetY();
            $cell_height = $new_y_position - $y_position;
            $pdf->SetXY(140, $y_position);
           $pdf->Cell($col2_width, $cell_height, iconv('UTF-8', 'ISO-8859-1', "Apellidos y Nombres: $apellido_usuario, $nombre_usuario"), 1, 1);

            // Segunda fila
            $y_position = $new_y_position;
            $pdf->SetXY(10, $y_position);
            $pdf->SetX(10); // Iniciar posición de escritura sin espacio
            $pdf->Cell($checkSize + 2, $row_height, '', 1); // Crear una celda pequeña para el checkbox
            $pdf->SetXY(10 + $checkOffsetX, $y_position); // Ajuste fino para el checkbox dentro de la celda
            if ($fotos) {
                drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
            } else {
                drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
            }
            $pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
            $pdf->Cell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "Dos fotos de tamaño 4 x 4 cm."), 1, 0, 'L');
            $pdf->Cell($col2_width, $row_height, iconv('UTF-8', 'ISO-8859-1', "DNI: $dni_usuario"), 1, 1);

            // Tercera fila
            $y_position = $pdf->GetY();
            $pdf->SetXY(10, $y_position);
            $pdf->SetX(10); // Iniciar posición de escritura sin espacio
            $pdf->Cell($checkSize + 2, $row_height, '', 1); // Crear una celda pequeña para el checkbox
            $pdf->SetXY(10 + $checkOffsetX, $y_position); // Ajuste fino para el checkbox dentro de la celda
            if ($folio) {
                drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
            } else {
                drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
            }
            $pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
            $pdf->Cell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "1 Folio A4"), 1, 0, 'L');
            $pdf->Cell($col2_width, $row_height, iconv('UTF-8', 'ISO-8859-1', "Fecha: $fecha_actual"), 1, 1);

            // Cuarta fila
            $y_position = $pdf->GetY();
            $pdf->SetXY(10, $y_position);
            $pdf->SetX(10); // Iniciar posición de escritura sin espacio
            $pdf->Cell($checkSize + 2, $row_height, '', 1); // Crear una celda pequeña para el checkbox
            $pdf->SetXY(10 + $checkOffsetX, $y_position); // Ajuste fino para el checkbox dentro de la celda
            if ($fotocopia_dni) {
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
            if ($fotocopia_partida_nacimiento) {
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
            if ($constancia_cuil) {
                drawCheckmark($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
            } else {
                drawEmptyBox($pdf, $pdf->GetX(), $y_position + $checkOffsetY, $checkSize);
            }
            $pdf->SetXY(12 + $checkSize + $textOffsetX, $y_position); // Reducir el desplazamiento para pegar el texto
            $pdf->Cell($col1_width - ($checkSize + 6), $row_height, iconv('UTF-8', 'ISO-8859-1', "Constancia de CUIL."), 1, 0, 'L');
            $pdf->Cell($col2_width, $row_height, '', 1, 1); // Celda vacía para alinear correctamente
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

            // Recuadro punteado para la firma
            $x_signature = $x + $w + 1; // Ajuste para el segundo recuadro punteado
            $pdf->DottedRect($x_signature, $y, 49, $h);

            // Ajustar la posición del texto dentro del recuadro punteado
            $pdf->SetXY($x_signature + 1, $y); // Desplazar ligeramente para asegurarse de que el texto comience en el margen
            $pdf->Cell(48, 10, iconv('UTF-8', 'ISO-8859-1', "Firma y Aclaración:"), 0, 1, 'L', false);



            // Observaciones
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

        exit;
