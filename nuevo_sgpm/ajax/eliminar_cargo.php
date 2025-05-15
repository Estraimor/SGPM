<?php
include '../../conexion/conexion.php';

$id = intval($_POST['id']);

$stmt = $conexion->prepare("DELETE FROM cargos WHERE profesor_idProrfesor = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'msg' => 'No se pudo eliminar el cargo']);
}
$stmt->close();
