<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';

$id = intval($_POST['id']);

$sql = "SELECT nombre_cargo, turno FROM cargos WHERE profesor_idProrfesor = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'cargo' => $fila['nombre_cargo'],
        'turno' => $fila['turno']
    ]);
} else {
    echo json_encode(['success' => false]);
}
$stmt->close();
