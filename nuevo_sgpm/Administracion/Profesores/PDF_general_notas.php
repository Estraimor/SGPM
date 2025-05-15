<?php
// PDF_general_notas.php

// Mostrar errores durante el desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 0) Iniciar buffer para evitar "headers already sent"
ob_start();

require '../../../indexs/pdf/vendor/setasign/fpdf/fpdf.php';
include '../../../conexion/conexion.php';

// Helper para convertir UTF‑8 → ISO‑8859‑1 (FPDF)
function conv(string $s): string {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $s);
}

// 1) Capturar parámetros EXACTOS de la URL
$idCarrera   = isset($_GET['idCarrera'])   ? (int)$_GET['idCarrera']   : 0;
$idMateria   = isset($_GET['idMateria'])   ? (int)$_GET['idMateria']   : 0;
$curso       = isset($_GET['cursoId'])     ? (int)$_GET['cursoId']     : 0;
$comision    = isset($_GET['comisionId'])  ? (int)$_GET['comisionId']  : 0;
$anio        = isset($_GET['anio'])        ? (int)$_GET['anio']        : 0;

// 2) Leer datos de cabecera: carrera, materia, curso, comisión, profesor
$stmt = $conexion->prepare("
    SELECT 
      c.nombre_carrera    AS carreraNombre,
      m.Nombre            AS materiaNombre,
      cu.curso            AS curso,
      co.comision         AS comision,
      p.nombre_profe      AS nombre_profe,
      p.apellido_profe    AS apellido_profe
    FROM materias m
    JOIN carreras   c  ON m.carreras_idCarrera       = c.idCarrera
    JOIN cursos     cu ON m.cursos_idCursos          = cu.idCursos
    JOIN comisiones co ON m.comisiones_idComisiones   = co.idComisiones
    JOIN profesor   p  ON m.profesor_idProrfesor     = p.idProrfesor
    WHERE m.idMaterias = ?
");
$stmt->bind_param('i', $idMateria);
$stmt->execute();
$header = $stmt->get_result()->fetch_assoc() ?: [];
$stmt->close();

// 3) Obtener alumnos y todas sus notas (sin fijar dinámicamente TP count)
$sql = "
    SELECT 
      m.alumno_legajo    AS legajo,
      a.apellido_alumno,
      a.nombre_alumno,
      n.cuatrimestre,
      n.tipo_evaluacion,
      n.nota,
      n.nota_final,
      n.condicion
    FROM matriculacion_materias m
    JOIN alumno a ON m.alumno_legajo = a.legajo
    LEFT JOIN notas n 
      ON a.legajo = n.alumno_legajo 
     AND n.materias_idMaterias = ?
    WHERE m.materias_idMaterias = ?
      " . ($anio ? "AND YEAR(m.año_matriculacion) = ?" : "") . "
    ORDER BY a.apellido_alumno
";
$stmt = $conexion->prepare($sql);
if ($anio) {
    $stmt->bind_param('iii', $idMateria, $idMateria, $anio);
} else {
    $stmt->bind_param('ii',   $idMateria, $idMateria);
}
$stmt->execute();
$res = $stmt->get_result();

$alumnos = [];
while ($row = $res->fetch_assoc()) {
    $leg = $row['legajo'];
    if (!isset($alumnos[$leg])) {
        $alumnos[$leg] = [
            'nombre'        => $row['apellido_alumno'] . ' ' . $row['nombre_alumno'],
            'cuatrimestres' => [
                1 => ['tps'=>[], 'parcial'=>'', 'recuperatorio'=>''],
                2 => ['tps'=>[], 'parcial'=>'', 'recuperatorio'=>'']
            ],
            'nota_final'    => $row['nota_final'],
            'condicion'     => $row['condicion'],
        ];
    }
    $c = (int)$row['cuatrimestre'];
    $t = (int)$row['tipo_evaluacion'];
    $n = $row['nota'];
    if ($t === 1) {
        $alumnos[$leg]['cuatrimestres'][$c]['tps'][] = $n;
    } elseif ($t === 2) {
        $alumnos[$leg]['cuatrimestres'][$c]['parcial'] = $n;
    } elseif ($t === 3) {
        $alumnos[$leg]['cuatrimestres'][$c]['recuperatorio'] = $n;
    }
}
$stmt->close();

// 4) Clase PDF con 3 TP fijos por cuatrimestre
class PDF extends FPDF {
    public $headerData;
    private $wName = 60,
            $wTP   = 8,
            $wPar  = 12,
            $wRec  = 12,
            $wDef  = 20,
            $wCond = 20;
    private $numTP = 3; // fijo

    function Header() {
        $h = $this->headerData;
        // Logos
        $this->Image('../../../imagenes/politecnico.png',15,10,25);
        $this->Image('../../../imagenes/CGE.png',170,10,25);
        // Títulos
        $this->SetFont('Arial','B',20);
        $this->Cell(0,10,conv('Planilla de Calificaciones ').date('Y'),0,1,'C');
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,conv($h['carreraNombre']),0,1,'C');
        $this->Cell(0,8,conv('Unidad Curricular: '.$h['materiaNombre']),0,1,'C');
        $this->Cell(0,8,conv('Curso: '.$h['curso'].'   Comisión: '.$h['comision']),0,1,'C');
        $this->Cell(0,8,conv('Profesor a cargo: '.$h['nombre_profe'].' '.$h['apellido_profe']),0,1,'C');
        $this->Ln(5);
    }

    function Footer() {
        global $isLastPage;
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,conv('Página ').$this->PageNo(),0,0,'C');
        if (!empty($isLastPage)) {
            $h = $this->headerData;
            // Sello
            $this->Image('../../../imagenes/sello_obal.jpg',83,$this->GetY()-60,40);
            // Firma derecha
            $this->SetY(-35); $this->SetX(-80);
            $this->Line($this->GetX(), $this->GetY(), $this->GetX()+60, $this->GetY());
            $this->SetY(-34); $this->SetX(-80);
            $this->SetFont('Arial','',10);
            $this->MultiCell(60,5,conv("Firma y Aclaración:\n".$h['nombre_profe'].' '.$h['apellido_profe']),0,'C');
            // Firma izquierda
            $this->SetY(-35); $this->SetX(15);
            $this->Line(15, $this->GetY(), 75, $this->GetY());
            $this->Image('../../../imagenes/Anibal_sello.jpg',23,$this->GetY()+2,40);
        }
    }

    function TableHeader() {
        // ancho total de 1 cuatrimestre
        $an = $this->numTP * $this->wTP + $this->wPar + $this->wRec;

        // Fila 1
        $this->SetFont('Arial','B',9);
        $this->Cell($this->wName,8,'',0,0,'C');
        $this->Cell($an,8,conv('1º Cuatrimestre'),1,0,'C');
        $this->Cell($an,8,conv('2º Cuatrimestre'),1,0,'C');
        $this->Cell($this->wDef,8,conv('Nota Final'),1,0,'C');
        $this->Cell($this->wCond,8,conv('Condición'),1,1,'C');

        // Fila 2
        $this->SetFont('Arial','B',8);
        $this->Cell($this->wName,8,conv('Nombre y Apellido'),1,0,'C');
        // 1º cuatri
        for ($i=1; $i<=$this->numTP; $i++) {
            $this->Cell($this->wTP,8,conv("TP{$i}"),1,0,'C');
        }
        $this->Cell($this->wPar,8,conv('Parcial'),1,0,'C');
        $this->Cell($this->wRec,8,conv('Recup.'),1,0,'C');
        // 2º cuatri
        for ($i=1; $i<=$this->numTP; $i++) {
            $this->Cell($this->wTP,8,conv("TP{$i}"),1,0,'C');
        }
        $this->Cell($this->wPar,8,conv('Parcial'),1,0,'C');
        $this->Cell($this->wRec,8,conv('Recup.'),1,0,'C');
        // celdas vacías de Nota Final y Condición
        $this->Cell($this->wDef,8,'',1,0,'C');
        $this->Cell($this->wCond,8,'',1,1,'C');
    }

    function TableRow($name, $cuat, $nf, $cd) {
        $this->SetFont('Arial','',8);
        // Nombre
        $this->Cell($this->wName,8,conv($name),1,0,'L');
        // 1º cuatri TP1..TP3
        for ($i=1; $i<=$this->numTP; $i++) {
            $val = $cuat[1]['tps'][$i-1] ?? '';
            $this->Cell($this->wTP,8,$val,1,0,'C');
        }
        // Parcial/Recup.
        $this->Cell($this->wPar,8,$cuat[1]['parcial'] ?? '',1,0,'C');
        $this->Cell($this->wRec,8,$cuat[1]['recuperatorio'] ?? '',1,0,'C');
        // 2º cuatri TP1..TP3
        for ($i=1; $i<=$this->numTP; $i++) {
            $val = $cuat[2]['tps'][$i-1] ?? '';
            $this->Cell($this->wTP,8,$val,1,0,'C');
        }
        // Parcial/Recup.
        $this->Cell($this->wPar,8,$cuat[2]['parcial'] ?? '',1,0,'C');
        $this->Cell($this->wRec,8,$cuat[2]['recuperatorio'] ?? '',1,0,'C');
        // Nota Final y Condición
        $this->Cell($this->wDef,8,$nf ?? '-',1,0,'C');
        $this->Cell($this->wCond,8,conv($cd ?? '-'),1,1,'C');
    }
}

// 5) Generar PDF
$pdf = new PDF();
$pdf->headerData = $header;
$isLastPage = false;
$pdf->AddPage();
$pdf->TableHeader();
foreach ($alumnos as $a) {
    $pdf->TableRow($a['nombre'], $a['cuatrimestres'], $a['nota_final'], $a['condicion']);
}
// Página de firma
$pdf->AddPage();
$isLastPage = true;

// Limpiar buffer y enviar PDF
ob_end_clean();
$pdf->Output('D', 'Planilla_Calificaciones_'.conv($header['materiaNombre']).'.pdf');
