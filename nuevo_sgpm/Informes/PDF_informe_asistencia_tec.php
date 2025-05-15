<?php
require './pdf/vendor/setasign/fpdf/fpdf.php';

$conexion = mysqli_connect('localhost', 'u756746073_root', 'POLITECNICOmisiones2023.', 'u756746073_politecnico', 3306);
if (!$conexion) {
    die('Error de conexión a la base de datos.');
}

function obtenerNombre($conexion, $tabla, $campoID, $campoNombre, $id) {
    $stmt = $conexion->prepare("SELECT $campoNombre FROM $tabla WHERE $campoID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    return $resultado[$campoNombre] ?? '';
}

class PDF extends FPDF {
    function Header() {
        global $nombre_inst, $subtitulo, $fecha_inicio, $fecha_fin, $fechas_con_asistencia;

        if ($this->PageNo() == 1) {
            $this->SetFillColor(189, 213, 234);
            $this->Rect(10, 7, 410, 41, 'F'); // A3 más ancho
            $this->SetXY(15, 8);
            $this->SetFont('Arial', 'B', 14);
            $this->Image('../../imagenes/politecnico.png', 12, 12, 30);
            $this->Cell(0, 20, mb_convert_encoding($nombre_inst, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
            $this->SetFont('Arial', 'I', 11);
            $this->Cell(0, 10, mb_convert_encoding($subtitulo, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, 'Desde: ' . $fecha_inicio . ' hasta: ' . $fecha_fin, 0, 1, 'C');
            $this->Ln(5);
        }
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 7, 'Alumno', 1, 0, 'C');
        foreach ($fechas_con_asistencia as $fecha) {
            $this->Cell(20, 7, date('d/m', strtotime($fecha)), 1, 0, 'C');
        }
        $this->Ln();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    $idCarrera = $_POST['carrera'] ?? null;
    $idCurso = $_POST['curso'] ?? null;
    $idComision = $_POST['comision'] ?? null;

    if ($fecha_inicio && $fecha_fin && $idCarrera && $idCurso && $idComision) {
        $nombre_carrera = obtenerNombre($conexion, 'carreras', 'idCarrera', 'nombre_carrera', $idCarrera);
        $nombre_curso = obtenerNombre($conexion, 'cursos', 'idCursos', 'curso', $idCurso);
        $nombre_comision = obtenerNombre($conexion, 'comisiones', 'idComisiones', 'comision', $idComision);

        $subtitulo = "$nombre_carrera - $nombre_curso Año - Comisión $nombre_comision";
        $nombre_inst = 'Instituto Superior Politécnico Misiones Nº 1';

        // Fechas únicas donde hubo asistencia
        $stmt_fechas = $conexion->prepare("
            SELECT DISTINCT fecha
            FROM asistencia
            WHERE fecha BETWEEN ? AND ?
        ");
        $stmt_fechas->bind_param('ss', $fecha_inicio, $fecha_fin);
        $stmt_fechas->execute();
        $res_fechas = $stmt_fechas->get_result();

        $fechas_con_asistencia = [];
        while ($row = $res_fechas->fetch_assoc()) {
            $fechas_con_asistencia[] = $row['fecha'];
        }

        if (empty($fechas_con_asistencia)) {
            die('No hay registros de asistencia.');
        }

        // Crear PDF A3 Horizontal
        $pdf = new PDF('L', 'mm', 'A3');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 9);

        // Traer asistencias de alumnos
        $stmt_asistencias = $conexion->prepare("
            SELECT CONCAT(a2.apellido_alumno, ' ', a2.nombre_alumno) AS nombre_completo,
                   a.fecha,
                   a.asistencia
            FROM asistencia a
            INNER JOIN alumno a2 ON a2.legajo = a.alumno_legajo
            INNER JOIN inscripcion_asignatura ia ON ia.alumno_legajo = a.alumno_legajo
            WHERE ia.carreras_idCarrera = ?
              AND ia.cursos_idCursos = ?
              AND ia.comisiones_idComisiones = ?
              AND a.fecha BETWEEN ? AND ?
            ORDER BY a2.apellido_alumno, a2.nombre_alumno, a.fecha
        ");
        $stmt_asistencias->bind_param('iiiss', $idCarrera, $idCurso, $idComision, $fecha_inicio, $fecha_fin);
        $stmt_asistencias->execute();
        $res_asistencias = $stmt_asistencias->get_result();

        $asistencias = [];

        while ($fila = $res_asistencias->fetch_assoc()) {
            $nombre = mb_convert_encoding($fila['nombre_completo'], 'ISO-8859-1', 'UTF-8');
            $fecha = $fila['fecha'];
            $asistencia = $fila['asistencia'];

            $letra = match($asistencia) {
                1 => 'P',
                2 => 'A',
                3 => 'J',
                default => '-',
            };

            $asistencias[$nombre][$fecha][] = $letra;
        }

        foreach ($asistencias as $nombre => $fechas) {
            $pdf->Cell(50, 7, mb_strimwidth($nombre, 0, 40, '...'), 1);
            foreach ($fechas_con_asistencia as $fecha) {
                if (!empty($fechas[$fecha])) {
                    $texto = implode('|', $fechas[$fecha]);
                } else {
                    $texto = '-';
                }
                $pdf->Cell(20, 7, mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            }
            $pdf->Ln();
        }

        // Nombre del archivo
        $nombre_archivo = 'Asistencia_' . str_replace(' ', '_', $subtitulo) . "_{$fecha_inicio}_al_{$fecha_fin}.pdf";

        // Salida
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', mb_convert_encoding($nombre_archivo, 'ISO-8859-1', 'UTF-8')) . '"');
        header('Cache-Control: max-age=0');
        $pdf->Output('D', str_replace(' ', '_', mb_convert_encoding($nombre_archivo, 'ISO-8859-1', 'UTF-8')));
        exit;
    } else {
        echo "Error: Faltan datos.";
    }
} else {
    echo "Error: Método no válido.";
}
?>
