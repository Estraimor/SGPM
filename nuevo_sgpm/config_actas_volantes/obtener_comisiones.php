<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';

$idCurso = intval($_GET['idCurso']);
$queryComisiones = "SELECT idComisiones, comision FROM comisiones WHERE idComisiones IN (
                        SELECT DISTINCT comisiones_idComisiones FROM materias WHERE cursos_idCursos = $idCurso
                    )";
$resultComisiones = $conexion->query($queryComisiones);

echo '<option value="">Seleccione una comisión</option>';
if ($resultComisiones->num_rows > 0) {
    while ($row = $resultComisiones->fetch_assoc()) {
        echo '<option value="' . $row['idComisiones'] . '">' . htmlspecialchars($row['comision']) . '</option>';
    }
} else {
    echo '<option value="">No hay comisiones disponibles</option>';
}
?>
