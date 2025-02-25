<?php
include '../../conexion/conexion.php';

$idCarrera = intval($_GET['idCarrera']);
$idCurso = intval($_GET['idCurso']);
$idComision = intval($_GET['idComision']);

$queryMaterias = "SELECT idMaterias, Nombre 
                  FROM materias 
                  WHERE carreras_idCarrera = $idCarrera 
                  AND cursos_idCursos = $idCurso 
                  AND comisiones_idComisiones = $idComision";

$resultMaterias = $conexion->query($queryMaterias);

if ($resultMaterias->num_rows > 0) {
    while ($row = $resultMaterias->fetch_assoc()) {
        echo '<div><input type="checkbox" name="materia" value="' . $row['idMaterias'] . '"> ' . htmlspecialchars($row['Nombre']) . '</div>';
    }
} else {
    echo '<p>No hay materias disponibles para esta combinación de carrera, curso y comisión</p>';
}
?>
