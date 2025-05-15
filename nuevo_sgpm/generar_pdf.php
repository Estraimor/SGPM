<?php


if (!file_exists('../indexs/pdf/vendor/setasign/fpdf/fpdf.php')) {
    die("No se encontró la librería FPDF.");
}
include '../conexion/conexion.php';


$fecha = $_POST['fecha'] ?? null;
if (!$fecha) {
    die("Fecha no proporcionada.");
}

$fechaFormateada = date('d / m / Y', strtotime($fecha));

// 📌 Ruta del logo - podés ajustarla según ubicación
$logoRuta = './Escudo_provincia.png';

// 🧾 Consulta de docentes con cargos
$sql = "SELECT p.nombre_profe, p.apellido_profe, p.dni_profe, c.nombre_cargo, c.turno
        FROM profesor p
        INNER JOIN cargos c ON p.idProrfesor = c.profesor_idProrfesor
        ORDER BY p.apellido_profe ASC";

$resultado = $conexion->query($sql);

// 🧱 Inicia PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// 🖼 Encabezado
if (file_exists($logoRuta)) {
    $pdf->Image($logoRuta, 10, 10, 25); // imagen de 25mm de ancho
}

$pdf->SetXY(40, 10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->MultiCell(0, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "CONSEJO GENERAL DE EDUCACIÓN\nProvincia de Misiones"), 0, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(40, 20);
$pdf->MultiCell(0, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "CUISE: 1672      LOCALIDAD: POSADAS\nESTABLECIMIENTO: Instituto Superior Politécnico Misiones Nº1"), 0, 'L');
$pdf->SetXY(40, 35);
$pdf->MultiCell(0, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "FECHA       $fechaFormateada"), 0, 'L');

// 🧾 Tabla de datos
$pdf->Ln(25);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(50, 7, 'APELLIDO Y NOMBRE', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'D.N.I', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Area', 1, 0, 'C', true);
$pdf->Cell(20, 7, 'TURNO', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'HORAS', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Firma/Observaciones', 1, 1, 'C', true);

// 📄 Contenido
$pdf->SetFont('Arial', '', 9);
while ($row = $resultado->fetch_assoc()) {
    $nombre = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "{$row['apellido_profe']} {$row['nombre_profe']}");
    $dni = $row['dni_profe'];
    $cargo = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $row['nombre_cargo']);
    $turno = strtoupper($row['turno']);
    $horas = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', '19 A 23:30 HS');

    $pdf->Cell(50, 7, $nombre, 1);
    $pdf->Cell(25, 7, $dni, 1);
    $pdf->Cell(35, 7, $cargo, 1);
    $pdf->Cell(20, 7, $turno, 1);
    $pdf->Cell(30, 7, $horas, 1);
    $pdf->Cell(30, 7, '', 1);
    $pdf->Ln();
}

// ✍️ Firmas
$pdf->Ln(10);
$pdf->Cell(90, 10, 'Firma Secretario', 0, 0, 'C');
$pdf->Cell(90, 10, 'Firma Director', 0, 1, 'C');

// 📤 Salida del PDF
$pdf->Output('I', 'planilla_docentes_con_cargo.pdf');
