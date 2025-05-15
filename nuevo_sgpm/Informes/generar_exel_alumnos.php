<?php
//generar_exel_alumnos.php
// Evita mostrar advertencias obsoletas en producción
error_reporting(E_ALL & ~E_DEPRECATED);

require './pdf/vendor/setasign/fpdf/fpdf.php';
include '../../conexion/conexion.php';
mysqli_set_charset($conexion, "utf8");

// Variables globales para el encabezado
$nombre_carrera = '';
$nombre_curso = '';
$nombre_comision = '';
$nombre_inst = 'Instituto Superior Politécnico Misiones Nº 1';

class PDF extends FPDF
{
    function Header()
    {
        global $nombre_carrera, $nombre_curso, $nombre_comision, $nombre_inst;

        $this->Ln(30);
        $xRect = ($this->GetPageWidth() - 190) / 2;
        $this->SetFillColor(189, 213, 234);

        if ($this->PageNo() == 1) {
            $this->Rect($xRect, 10, 180, 50, 'F');
            $this->SetFont('Arial', 'B', 16);
            $this->SetTextColor(20, 13, 79);
            $this->SetXY($xRect, 15);
            $this->Cell(190, 10, mb_convert_encoding($nombre_inst, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

            $this->SetFont('Arial', '', 14);
            $this->SetXY($xRect, 30);
            $this->Cell(190, 10, mb_convert_encoding($nombre_carrera, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
            $this->SetXY($xRect, 40);
            $textoInfo = "Curso: $nombre_curso - Comisión: $nombre_comision";
            $this->Cell(190, 10, mb_convert_encoding($textoInfo, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
            $this->Ln(10);
        }

        $this->Image('../../imagenes/politecnico.png', $xRect + 5, 15, 20);
        $this->SetFillColor(205, 237, 253);
        $this->SetTextColor(0);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(10, 10, 'N', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Apellido', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Nombre', 1, 0, 'C', true);
        $this->Cell(70, 10, 'DNI', 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, mb_convert_encoding('Página ' . $this->PageNo(), 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['carrera'], $_POST['curso'], $_POST['comision'])) {
        global $nombre_carrera, $nombre_curso, $nombre_comision;

        $carrera = $_POST['carrera'];
        $curso = $_POST['curso'];
        $comision = $_POST['comision'];

        // Consulta de datos generales
        $consulta_info = "SELECT c.nombre_carrera, cu.curso, co.comision 
                          FROM carreras c
                          INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                          INNER JOIN cursos cu ON m.cursos_idCursos = cu.idCursos
                          INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones
                          WHERE c.idCarrera = '$carrera' 
                          AND cu.idCursos = '$curso' 
                          AND co.idComisiones = '$comision'
                          LIMIT 1";

        $resultado_info = $conexion->query($consulta_info);

        if ($resultado_info && $resultado_info->num_rows > 0) {
            $fila_info = $resultado_info->fetch_assoc();
            $nombre_carrera  = $fila_info['nombre_carrera'];
            $nombre_curso    = $fila_info['curso'];
            $nombre_comision = $fila_info['comision'];
        }

        // Consulta de alumnos
        $consulta = "SELECT DISTINCT a.apellido_alumno, a.nombre_alumno, a.dni_alumno
                     FROM matriculacion_materias mm
                     INNER JOIN materias m ON mm.materias_idMaterias = m.idMaterias
                     INNER JOIN alumno a ON mm.alumno_legajo = a.legajo
                     WHERE m.carreras_idCarrera = '$carrera'
                     AND m.cursos_idCursos = '$curso'
                     AND m.comisiones_idComisiones = '$comision'
                     ORDER BY a.apellido_alumno";

        $resultado = $conexion->query($consulta);

        if ($resultado && $resultado->num_rows > 0) {
            $pdf = new PDF();
            $pdf->AddPage();

            $contador = 1;
            while ($fila = $resultado->fetch_assoc()) {
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(10, 10, $contador, 1, 0, 'C');
                $pdf->Cell(50, 10, mb_convert_encoding($fila['apellido_alumno'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
                $pdf->Cell(50, 10, mb_convert_encoding($fila['nombre_alumno'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
                $pdf->Cell(70, 10, mb_convert_encoding($fila['dni_alumno'], 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');
                $contador++;
            }

            // ¡CUIDADO! No debe haber ningún echo ni salida antes de Output()
            $pdf->Output('D', 'Alumnos_' . date('Y-m-d') . '.pdf');
            exit;
        } else {
            // No usar echo si luego va Output(); si querés mostrar esto, debe ir por JavaScript o antes del PDF
            die("No se encontraron datos de alumnos para la carrera, curso y comisión seleccionados.");
        }
    } else {
        die("Faltan parámetros: carrera, curso o comisión.");
    }
} else {
    die("Acceso denegado.");
}
