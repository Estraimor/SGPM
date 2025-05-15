<?php
require './pdf/vendor/setasign/fpdf/fpdf.php';

$conexion = mysqli_connect('localhost', 'u756746073_root', 'POLITECNICOmisiones2023.', 'u756746073_politecnico', 3306);
if (!$conexion) {
    die('Error de conexión a la base de datos.');
}

function fechas_laborales($inicio, $fin) {
    $fechas = [];
    $inicio_ts = strtotime($inicio);
    $fin_ts = strtotime($fin);
    for ($i = $inicio_ts; $i <= $fin_ts; $i += 86400) {
        $dia = date('w', $i); // 0=domingo, 6=sábado
        if ($dia != 0 && $dia != 6) {
            $fechas[] = date('Y-m-d', $i);
        }
    }
    return $fechas;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    $idCarrera = $_POST['carrera'] ?? null;
    $idCurso = $_POST['curso'] ?? null;
    $idComision = $_POST['comision'] ?? null;

    if (!$fecha_inicio || !$fecha_fin || !$idCarrera || !$idCurso || !$idComision) {
        die("Faltan datos obligatorios.");
    }

    $fechas = fechas_laborales($fecha_inicio, $fecha_fin);
    if (empty($fechas)) {
        die("No hay días hábiles en el rango seleccionado.");
    }

    // Obtener nombres
    $nombre_carrera = '';
    $nombre_curso = '';
    $nombre_comision = '';

    $res_car = mysqli_query($conexion, "SELECT nombre_carrera FROM carreras WHERE idCarrera = $idCarrera");
    if ($row = mysqli_fetch_assoc($res_car)) $nombre_carrera = $row['nombre_carrera'];

    $res_cur = mysqli_query($conexion, "SELECT curso FROM cursos WHERE idCursos = $idCurso");
    if ($row = mysqli_fetch_assoc($res_cur)) $nombre_curso = $row['curso'];

    $res_com = mysqli_query($conexion, "SELECT comision FROM comisiones WHERE idComisiones = $idComision");
    if ($row = mysqli_fetch_assoc($res_com)) $nombre_comision = $row['comision'];

    $alumnos = [];

if ($idCurso == 3) {
    $año_fijo = 2023;
    $curso_fijo = 2;
    $sql = "
        SELECT DISTINCT a.legajo, a.apellido_alumno, a.nombre_alumno
        FROM inscripcion_asignatura ia
        INNER JOIN alumno a ON a.legajo = ia.alumno_legajo
        WHERE ia.carreras_idCarrera = ?
        AND ia.Cursos_idCursos = ?
        AND ia.Comisiones_idComisiones = ?
        AND ia.año_inscripcion = ?
        ORDER BY a.apellido_alumno, a.nombre_alumno
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iiii", $idCarrera, $curso_fijo, $idComision, $año_fijo);
    $stmt->execute();
    $result = $stmt->get_result();

} else if ($idCurso == 2) {
    $año_fijo = 2024;
    $curso_fijo = 1;
    $sql = "
        SELECT DISTINCT a.legajo, a.apellido_alumno, a.nombre_alumno
        FROM inscripcion_asignatura ia
        INNER JOIN alumno a ON a.legajo = ia.alumno_legajo
        WHERE ia.carreras_idCarrera = ?
        AND ia.Cursos_idCursos = ?
        AND ia.Comisiones_idComisiones = ?
        AND ia.año_inscripcion = ?
        ORDER BY a.apellido_alumno, a.nombre_alumno
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iiii", $idCarrera, $curso_fijo, $idComision, $año_fijo);
    $stmt->execute();
    $result = $stmt->get_result();

} else {
    $sql = "
        SELECT DISTINCT a.legajo, a.apellido_alumno, a.nombre_alumno
        FROM matriculacion_materias mm
        INNER JOIN materias m ON m.idMaterias = mm.materias_idMaterias
        INNER JOIN alumno a ON a.legajo = mm.alumno_legajo
        WHERE m.carreras_idCarrera = ?
        AND m.cursos_idCursos = ?
        AND m.comisiones_idComisiones = ?
        ORDER BY a.apellido_alumno, a.nombre_alumno
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iii", $idCarrera, $idCurso, $idComision);
    $stmt->execute();
    $result = $stmt->get_result();
}

    $stmt->execute();
    $result = $stmt->get_result();

   function limpiar_caracteres($cadena) {
    $reemplazos = [
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
        'ñ'=>'n', 'Ñ'=>'N',
        'ü'=>'u', 'Ü'=>'U'
    ];
    return strtr($cadena, $reemplazos);
}

while ($row = $result->fetch_assoc()) {
    $nombre_crudo = $row['apellido_alumno'] . ', ' . $row['nombre_alumno'];
    $nombre_limpio = limpiar_caracteres($nombre_crudo);
    $alumnos[] = ['nombre' => $nombre_limpio];
}

    if (empty($alumnos)) {
        die("No se encontraron alumnos para esos filtros.");
    }

    class PDF extends FPDF {
        public $fechas;
        public $titulo;

        function Header() {
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(0, 10, utf8_decode('Instituto Superior Politécnico Misiones Nº 1'), 0, 1, 'C');
            $this->SetFont('Arial', '', 9);
            $this->Cell(0, 7, utf8_decode($this->titulo), 0, 1, 'C');
            $this->Ln(2);

            // Fila 1
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(5, 10, utf8_decode('N°'), 1, 0, 'C');
            $this->Cell(45, 10, utf8_decode('Estudiante'), 1, 0, 'C');

            foreach ($this->fechas as $fecha) {
                $fecha_corta = date('d/m', strtotime($fecha));
                $this->Cell(52, 10, $fecha_corta, 1, 0, 'C');
            }
            $this->Ln();

            // Fila 2: A1–A4
            $this->Cell(5, 7, '', 1, 0);
            $this->Cell(45, 7, '', 1, 0);
            foreach ($this->fechas as $_) {
                for ($i = 1; $i <= 4; $i++) {
                    $this->Cell(13, 7, "A$i", 1, 0, 'C');
                }
            }
            $this->Ln();
        }
    }

    $pdf = new PDF('L', 'mm', 'A3');
    $pdf->SetMargins(5, 10, 5);
    $pdf->fechas = $fechas;
    $pdf->titulo = "$nombre_carrera - $nombre_curso Año - Comisión $nombre_comision";
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 7);

    $contador = 1;
    foreach ($alumnos as $a) {
        $recortado = mb_strimwidth($a['nombre'], 0, 55, '...');
        $pdf->Cell(5, 7, $contador, 1, 0, 'C');
        $pdf->Cell(45, 7, $recortado, 1);
        $contador++;

        foreach ($fechas as $_) {
            for ($i = 0; $i < 4; $i++) {
                $pdf->Cell(13, 7, '', 1);
            }
        }
        $pdf->Ln();
    }

    $nombre_archivo = "Planilla_Manual_" . str_replace('/', '-', $fecha_inicio) . "_al_" . str_replace('/', '-', $fecha_fin) . ".pdf";

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
    $pdf->Output('D', $nombre_archivo);
    exit;
}
?>
