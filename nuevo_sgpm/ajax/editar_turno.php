<?php
include '../../conexion/conexion.php';

$id = intval($_POST['id']);
$turno = trim($_POST['turno']);

$stmt = $conexion->prepare("UPDATE cargos SET turno = ? WHERE profesor_idProrfesor = ?");
$stmt->bind_param("si", $turno, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'msg' => 'Error al actualizar turno']);
}
$stmt->close();
