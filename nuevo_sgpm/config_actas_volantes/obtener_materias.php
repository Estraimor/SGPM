<?php
include '../../conexion/conexion.php';

$idCarrera = intval($_GET['idCarrera']);
$queryMaterias = "SELECT idMaterias, Nombre FROM materias WHERE carreras_idCarrera = $idCarrera";
$resultMaterias = $conexion->query($queryMaterias);

if ($resultMaterias->num_rows > 0) {
    while ($row = $resultMaterias->fetch_assoc()) {
        echo '<div><input type="checkbox" name="materia" value="' . $row['idMaterias'] . '"> ' . htmlspecialchars($row['Nombre']) . '</div>';
    }
} else {
    echo '<p>No hay materias disponibles para esta carrera</p>';
}
?>
