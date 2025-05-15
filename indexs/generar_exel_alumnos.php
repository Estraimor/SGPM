<?php
require './pdf/vendor/setasign/fpdf/fpdf.php';
include '../conexion/conexion.php';

$nombre_carrera = '';
$nombre_curso = '';
$nombre_comision = '';
$nombre_materia = '';
$nombre_inst = utf8_decode('Instituto Superior Politécnico Misiones Nº 1');

class PDF extends FPDF
{
    function Header()
    {
        global $nombre_carrera, $nombre_curso, $nombre_comision, $nombre_materia, $nombre_inst;

        $this->Ln(30);
        $xRect = ($this->GetPageWidth() - 190) / 2;
        $this->SetFillColor(189, 213, 234);

        if ($this->PageNo() == 1) {
            $this->Rect($xRect, 10, 180, 60, 'F');
            $this->SetFont('Arial', 'B', 16);
            $this->SetTextColor(20, 13, 79);
            $this->SetXY($xRect, 15);
            $this->Cell(190, 10, $nombre_inst, 0, 1, 'C');

            $this->SetFont('Arial', '', 14);
            $this->SetXY($xRect, 30);
            $this->Cell(190, 10, $nombre_carrera, 0, 1, 'C');

            $this->SetXY($xRect, 40);
        $this->Cell(190, 10, utf8_decode("Curso: $nombre_curso - Comisión: $nombre_comision"), 0, 1, 'C');


            $this->SetXY($xRect, 50);
            $this->Cell(190, 10, "Unidad Curricular: $nombre_materia", 0, 1, 'C');

            $this->Ln(2);
        }

        $this->Image('../imagenes/politecnico.png', $xRect + 5, 15, 20);
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
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['carrera'], $_POST['curso'], $_POST['comision'], $_POST['materia'])) {
        global $nombre_carrera, $nombre_curso, $nombre_comision, $nombre_materia;

        $carrera  = $_POST['carrera'];
        $curso    = $_POST['curso'];
        $comision = $_POST['comision'];
        $materia  = $_POST['materia'];

        $consulta_info = "SELECT c.nombre_carrera, cu.curso, co.comision, m.Nombre AS nombre_materia
                          FROM materias m
                          INNER JOIN carreras c   ON c.idCarrera = m.carreras_idCarrera
                          INNER JOIN cursos cu    ON cu.idCursos = m.cursos_idCursos
                          INNER JOIN comisiones co ON co.idComisiones = m.comisiones_idComisiones
                          WHERE c.idCarrera = '$carrera' 
                            AND cu.idCursos = '$curso' 
                            AND co.idComisiones = '$comision'
                            AND m.idMaterias = '$materia'
                          LIMIT 1";

        $resultado_info = $conexion->query($consulta_info);
        if ($resultado_info->num_rows > 0) {
            $fila_info = $resultado_info->fetch_assoc();
            $nombre_carrera  = utf8_decode($fila_info['nombre_carrera']);
            $nombre_curso    = utf8_decode($fila_info['curso']);
            $nombre_comision = utf8_decode($fila_info['comision']);
if (strpos($nombre_comision, 'Ã') !== false) {
    $nombre_comision = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $fila_info['comision']);
}
            $nombre_materia  = utf8_decode($fila_info['nombre_materia']);
        }

        $consulta = "SELECT DISTINCT a.apellido_alumno, a.nombre_alumno, a.dni_alumno
                     FROM matriculacion_materias mm
                     INNER JOIN alumno a ON mm.alumno_legajo = a.legajo
                     WHERE mm.materias_idMaterias = '$materia'
                     ORDER BY a.apellido_alumno";

        $resultado = $conexion->query($consulta);

        if ($resultado && $resultado->num_rows > 0) {
            $pdf = new PDF();
            $pdf->AddPage();

            $contador = 1;
            while ($fila = $resultado->fetch_assoc()) {
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(10, 10, $contador, 1, 0, 'C');
                $pdf->Cell(50, 10, utf8_decode($fila['apellido_alumno']), 1, 0, 'C');
                $pdf->Cell(50, 10, utf8_decode($fila['nombre_alumno']), 1, 0, 'C');
                $pdf->Cell(70, 10, utf8_decode($fila['dni_alumno']), 1, 1, 'C');
                $contador++;
            }

            $pdf->Output('D', 'Alumnos_' . date('Y-m-d') . '.pdf');
            exit;
        } else {
            echo "No se encontraron alumnos en esa materia.";
        }
    } else {
        echo "Faltan datos en el formulario.";
    }
} else {
    echo "Acceso denegado.";
}
?>