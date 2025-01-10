<?php
include '../../../conexion/conexion.php';

$carreraId = $_POST['carreraId'];

$query = "SELECT idMaterias, Nombre FROM materias WHERE carreras_idCarrera = ?";
$stmt = $conexion->prepare($query);

if ($stmt) {
    $stmt->bind_param("i", $carreraId);
    $stmt->execute();
    $result = $stmt->get_result();
    $materias = [];
    while ($row = $result->fetch_assoc()) {
        $materias[] = ['idMaterias' => $row['idMaterias'], 'nombre' => $row['Nombre']];
    }
    echo json_encode($materias);
} else {
    echo json_encode(['error' => 'Error preparing statement']);  // Captura errores en la preparación del statement
}
?>
