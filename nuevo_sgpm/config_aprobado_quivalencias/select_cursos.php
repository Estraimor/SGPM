<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include '../../conexion/conexion.php';
$legajo = intval($_GET['legajo']);

// Obtener cursos
$sqlCurso = "SELECT * FROM cursos ";
$stmt = $conexion->prepare($sqlCurso);
$stmt->execute();
$resultCurso = $stmt->get_result();

// Armado del select de cursos
echo "<label>Curso:</label>";
echo "<select id='selectCurso' onchange='actualizarComisiones()'>";
echo "<option value=''>Seleccionar...</option>";
while ($row = $resultCurso->fetch_assoc()) {
    echo "<option value='{$row['idCursos']}'>{$row['curso']}</option>";
}
echo "</select>";

 // Select de comisión vacío por ahora
 echo "<label>Comisión:</label>";
 echo "<select id='selectComision' onchange='cargarMaterias(this.value)'>";
 echo "<option value=''>Seleccionar...</option>";
 echo "</select>";
