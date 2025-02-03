<?php
// Mostrar todos los errores de PHP
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración y conexión a la base de datos
$server = 'localhost';
$user = 'u756746073_root';
$pass = 'POLITECNICOmisiones2023.';
$bd = 'u756746073_politecnico';
$conexion = mysqli_connect($server, $user, $pass, $bd, '3306');

// Verificar conexión a la base de datos
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Comprobar si se envió el formulario
if (isset($_POST['enviar'])) {
    if (!empty($_POST['nombre_alu']) && !empty($_POST['apellido_alu']) && !empty($_POST['dni_alu']) && !empty($_POST['legajo']) && !empty($_POST['celular']) && !empty($_POST['edad']) && !empty($_POST['observaciones']) && !empty($_POST['inscripcion_carrera'])) {

        // Variables desde el formulario
        $nombre_alu = $_POST['nombre_alu'];
        $apellido_alu = $_POST['apellido_alu'];
        $dni_alu = $_POST['dni_alu'];
        $legajo = $_POST['legajo'];
        $celular = $_POST['celular'];
        $edad = $_POST['edad'];
        $observaciones = $_POST['observaciones'];
        $trabajo_hs = isset($_POST['Trabajo_Horario']) ? $_POST['Trabajo_Horario'] : '';
        $ciudad_nacimiento = isset($_POST['ciudad']) ? $_POST['ciudad'] : '';
        $provincia_nacimiento = isset($_POST['provincia']) ? $_POST['provincia'] : '';
        $pais_nacimiento = isset($_POST['pais']) ? $_POST['pais'] : '';
        $cuil = isset($_POST['cuil']) ? $_POST['cuil'] : '';
        $discapacidad = isset($_POST['discapacidad']) ? $_POST['discapacidad'] : 'No posee';
        $fecha_nacimiento = isset($_POST['edad']) ? $_POST['edad'] : '';
        $titulo_secundario = isset($_POST['titulo_nivel_medio']) ? $_POST['titulo_nivel_medio'] : '';
        $escuela_secundaria = isset($_POST['otorgado_por_escuela']) ? $_POST['otorgado_por_escuela'] : '';
        $materias_adeuda = isset($_POST['materias_adeudadas']) ? $_POST['materias_adeudadas'] : '';
        $fecha_estimacion = isset($_POST['fecha_rendicion']) ? $_POST['fecha_rendicion'] : '';
        $ocupacion = isset($_POST['ocupacion']) ? $_POST['ocupacion'] : '';
        $domicilio_laboral = isset($_POST['domicilio_laboral']) ? $_POST['domicilio_laboral'] : '';
        $horario_laboral_desde = isset($_POST['horario_laboral_desde']) ? $_POST['horario_laboral_desde'] : '';
        $horario_laboral_hasta = isset($_POST['horario_laboral_hasta']) ? $_POST['horario_laboral_hasta'] : '';
        $calle_domicilio = isset($_POST['calle']) ? $_POST['calle'] : '';
        $barrio_domicilio = isset($_POST['barrio']) ? $_POST['barrio'] : '';
        $ciudad_domicilio = isset($_POST['ciudad_domicilio']) ? $_POST['ciudad_domicilio'] : '';
        $provincia_domicilio = isset($_POST['provincia_domicilio']) ? $_POST['provincia_domicilio'] : '';
        $numeracion_domicilio = isset($_POST['numeracion']) ? $_POST['numeracion'] : ''; 
        $telefono_urgencias = isset($_POST['telefono_urgencias']) ? $_POST['telefono_urgencias'] : '';
        $correo = isset($_POST['correo_electronico']) ? $_POST['correo_electronico'] : '';
        $carrera_pdf = isset($_POST['carrera']) ? $_POST['carrera'] : '';

        // Requisitos
        $original_titulo = isset($_POST['requisito1']) ? 1 : 0;
        $fotos = isset($_POST['requisito2']) ? 'Presentó requisito' : 0;
        $folio = isset($_POST['requisito3']) ? 'Presentó requisito' : 0;
        $fotocopia_dni = isset($_POST['requisito4']) ? 'Presentó requisito' : 0;
        $fotocopia_partida_nacimiento = isset($_POST['requisito5']) ? 'Presentó requisito' : 0;
        $constancia_cuil = isset($_POST['requisito6']) ? 'Presentó requisito' : 0;
        $pago = isset($_POST['requisito7']) ? 1 : 0;

        $nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Nombre Usuario';
        $apellido_usuario = isset($_SESSION['apellido']) ? $_SESSION['apellido'] : 'Apellido Usuario';
        $dni_usuario = isset($_SESSION['dni']) ? $_SESSION['dni'] : 'DNI Usuario';

        // Establecer la zona horaria a Buenos Aires, Argentina
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fecha_actual = date('d/m/Y');

        $carrera_seleccionada = '';

        // Asignar carrera seleccionada
        if (isset($_POST['carrera'])) {
            switch ($_POST['carrera']) {
                case 'enfermeria':
                    $carrera_seleccionada = 'Técnico Superior en Enfermería';
                    break;
                case 'acompanamiento-terapeutico':
                    $carrera_seleccionada = 'Técnico Superior en Acompañamiento Terapéutico';
                    break;
                case 'comercializacion-marketing':
                    $carrera_seleccionada = 'Técnico Superior en Comercialización y Marketing';
                    break;
                case 'automatizacion-robotica':
                    $carrera_seleccionada = 'Técnico Superior en Automatización y Robótica';
                    break;
                default:
                    $carrera_seleccionada = 'Carrera no especificada';
                    break;
            }
        }

        // Función para calcular la edad
        function calcularEdad($fecha_nacimiento) {
            $fecha_nacimiento = new DateTime($fecha_nacimiento);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nacimiento);
            return $edad->y;  // Retorna la cantidad de años completos
        }

        $edad_usuario = calcularEdad($fecha_nacimiento);

       // Bucle para verificar y ajustar el legajo y DNI
$intento_insercion = false;
while (!$intento_insercion) {
    // Verificar si el legajo ya existe
    $sql_verificar_legajo = "SELECT COUNT(*) as cantidad FROM alumno WHERE legajo = '$legajo'";
    $resultado_verificar_legajo = mysqli_query($conexion, $sql_verificar_legajo);
    $fila_legajo = mysqli_fetch_assoc($resultado_verificar_legajo);

    // Verificar si el DNI ya existe
    $sql_verificar_dni = "SELECT COUNT(*) as cantidad FROM alumno WHERE dni_alumno = '$dni_alu'";
    $resultado_verificar_dni = mysqli_query($conexion, $sql_verificar_dni);
    $fila_dni = mysqli_fetch_assoc($resultado_verificar_dni);

    if ($fila_legajo['cantidad'] == 0 && $fila_dni['cantidad'] == 0) {
        // Si el legajo y el DNI no existen, procedemos con la inserción
        $sql_insertar = "INSERT INTO alumno (
            nombre_alumno, 
            apellido_alumno, 
            dni_alumno, 
            legajo, 
            Trabaja_Horario, 
            edad, 
            observaciones, 
            celular, 
            estado, 
            ciudad_nacimiento,
            provincia_nacimiento,
            pais_nacimiento,
            cuil,
            discapacidad,
            fecha_nacimiento,
            Titulo_secundario,
            escuela_secundaria,
            materias_adeuda,
            fecha_estimacion,
            ocupacion,
            domicilio_laboral,
            horario_laboral_desde,
            horario_laboral_hasta,
            calle_domicilio,
            barrio_domicilio,
            ciudad_domicilio,
            provincia_domicilio,
            numeracion_domicilio,
            telefono_urgencias,
            correo,
            carrera,
            original_titulo,
            fotos,
            folio,
            fotocopia_dni,
            fotocopia_partida_nacimiento,
            constancia_cuil,
            Pago
        ) VALUES (
            '$nombre_alu', 
            '$apellido_alu', 
            '$dni_alu', 
            '$legajo', 
            '$trabajo_hs', 
            '$edad_usuario', 
            '$observaciones', 
            '$celular', 
            '3', 
            '$ciudad_nacimiento',
            '$provincia_nacimiento',
            '$pais_nacimiento',
            '$cuil',
            '$discapacidad',
            '$edad',
            '$titulo_secundario',
            '$escuela_secundaria',
            '$materias_adeuda',
            '$fecha_estimacion',
            '$ocupacion',
            '$domicilio_laboral',
            '$horario_laboral_desde',
            '$horario_laboral_hasta',
            '$calle_domicilio',
            '$barrio_domicilio',
            '$ciudad_domicilio',
            '$provincia_domicilio',
            '$numeracion_domicilio',
            '$telefono_urgencias',
            '$correo',
            '$carrera_pdf',
            '$original_titulo',
            '$fotos',
            '$folio',
            '$fotocopia_dni',
            '$fotocopia_partida_nacimiento',
            '$constancia_cuil',
            '$pago'
        )";

        // Intentamos la inserción
        if (mysqli_query($conexion, $sql_insertar)) {
            $intento_insercion = true; // Inserción exitosa, salir del bucle
        } else {
            echo "Error al insertar el alumno: " . mysqli_error($conexion);
        }
    } else {
        // Si el legajo ya existe, incrementamos el valor del legajo
        if ($fila_legajo['cantidad'] > 0) {
            $legajo++;
        }

        // Si el DNI ya existe, mostramos un error y salimos del bucle
        if ($fila_dni['cantidad'] > 0) {
            echo "<script>
                alert('Error: El DNI ya existe en el sistema.');
                history.back();
            </script>";
            exit;
        }
    }
}



// Obtener el mes actual
    $mesActual = date('n'); // Devuelve el mes en formato numérico (1-12)
    $añoActual = date('Y'); // Devuelve el año actual

    // Si estamos en noviembre (11) o diciembre (12), usar el siguiente año
    $añoInscripcion = ($mesActual >= 11) ? $añoActual + 1 : $añoActual;

    // Continuar con la inscripción en la carrera
    $alumno_legajo = $legajo;
    $carrera_id = $_POST['inscripcion_carrera'];
    $curso = $_POST['curso'];
    $comision = $_POST['comision'];

    $sql_insert = "INSERT INTO inscripcion_asignatura (carreras_idCarrera, alumno_legajo, año_inscripcion, Comisiones_idComisiones, Cursos_idCursos)
                VALUES ('$carrera_id', '$alumno_legajo', '$añoInscripcion', '$comision', '$curso')";

    if (mysqli_query($conexion, $sql_insert)) {
        echo "Inscripción realizada exitosamente.";
    } else {
        // Captura y muestra el error de MySQL con la consulta fallida
        echo "Error al inscribir: " . mysqli_error($conexion) . "<br>Consulta SQL: " . $sql_insert;
    }

    // Obtener todas las materias correspondientes a la carrera, curso y comisión seleccionados
$sqlMaterias = "SELECT idMaterias FROM materias 
WHERE carreras_idCarrera = '$carrera_id' 
AND cursos_idCursos = '$curso' 
AND comisiones_idComisiones = '$comision'";

$resultMaterias = mysqli_query($conexion, $sqlMaterias);

if (mysqli_num_rows($resultMaterias) > 0) {
// Insertar cada materia en la tabla de matriculación
while ($materia = mysqli_fetch_assoc($resultMaterias)) {
$materia_id = $materia['idMaterias'];
$sqlInsert = "INSERT INTO matriculacion_materias (alumno_legajo, materias_idMaterias, año_matriculacion)
      VALUES ('$alumno_legajo', '$materia_id', '$añoInscripcion')";
mysqli_query($conexion, $sqlInsert);
}

echo "Inscripción realizada correctamente.";
} else {
echo "No hay materias asociadas a esta carrera, curso y comisión.";
}


            // Generar el PDF con los datos recibidos por POST
            require './pdf/vendor/setasign/fpdf/fpdf.php';

            $nombre_inst = 'Instituto Superior Politécnico Misiones Nº 1';

            class PDF extends FPDF {
                function Header() {
                    global $nombre_inst, $legajo;

                    // Logo
                    if (file_exists('../../../../imagenes/politecnico.png')) {
                        $this->Image('../../../../imagenes/politecnico.png', 10, 10, 28);
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
} else {
        echo "<script>alert('¡Campos Vacíos!'); window.location.href = '../../../index.php';</script>";
    }
} else {
    echo "Formulario no enviado correctamente.";
}
?>
