<?php
include '../../../conexion/conexion.php';

$carrera_id = $_POST['carrera_id'];

$query = "SELECT idMaterias, Nombre FROM materias WHERE carreras_idCarrera = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $carrera_id);
$stmt->execute();
$result = $stmt->get_result();

$materias = [];
while ($row = $result->fetch_assoc()) {
    $materias[$row['idMaterias']] = $row['Nombre'];
}

echo json_encode($materias);
?>
