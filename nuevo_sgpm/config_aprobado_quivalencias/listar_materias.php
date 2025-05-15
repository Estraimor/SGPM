<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../conexion/conexion.php';

$carrera = intval($_GET['carrera'] ?? 0);
$curso = intval($_GET['curso'] ?? 0);
$comision = intval($_GET['comision'] ?? 0);

if ($carrera <= 0 || $curso <= 0 || $comision <= 0) {
    echo "<option value=''>Faltan datos válidos</option>";
    exit;
}

$sql = "SELECT m.idMaterias, m.Nombre 
        FROM materias m
        WHERE m.carreras_idCarrera = ? 
          AND m.cursos_idCursos = ? 
          AND m.comisiones_idComisiones = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("iii", $carrera, $curso, $comision);
$stmt->execute();
$res = $stmt->get_result();

echo "<option value=''>Seleccionar materia...</option>";
while ($row = $res->fetch_assoc()) {
    echo "<option value='" . $row['idMaterias'] . "'>" . htmlspecialchars($row['Nombre']) . "</option>";
}
?>
