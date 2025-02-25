<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';

$idCarrera = intval($_GET['idCarrera']);
$queryCursos = "SELECT idCursos, curso FROM cursos WHERE idCursos IN (
                    SELECT DISTINCT cursos_idCursos FROM materias WHERE carreras_idCarrera = $idCarrera
                )";
$resultCursos = $conexion->query($queryCursos);

echo '<option value="">Seleccione un curso</option>';
if ($resultCursos->num_rows > 0) {
    while ($row = $resultCursos->fetch_assoc()) {
        echo '<option value="' . $row['idCursos'] . '">' . htmlspecialchars($row['curso']) . '</option>';
    }
} else {
    echo '<option value="">No hay cursos disponibles</option>';
}
?>
