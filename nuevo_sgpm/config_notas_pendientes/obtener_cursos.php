<?php
include '../../conexion/conexion.php';

$carrera_id = isset($_POST['carrera']) ? $_POST['carrera'] : '';

if (!$carrera_id) {
    echo "<option value=''>Seleccione un curso</option>";
    exit;
}

$stmt = $conexion->prepare("
    SELECT DISTINCT c.idCursos, c.curso 
    FROM cursos c
    INNER JOIN materias m ON m.cursos_idCursos = c.idCursos
    WHERE m.carreras_idCarrera = ?
");
$stmt->bind_param("i", $carrera_id);

$stmt->execute();
$result = $stmt->get_result();

$options = "";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='{$row['idCursos']}'>{$row['curso']}</option>";
}

echo $options;
$stmt->close();
?>
