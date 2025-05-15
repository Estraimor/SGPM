<?php
include '../../conexion/conexion.php';

$id = intval($_POST['id']);
$cargo = trim($_POST['cargo']);
$turno = trim($_POST['turno']);

// Verifica si ya tiene un cargo
$verif = $conexion->prepare("SELECT id FROM cargos WHERE profesor_idProrfesor = ?");
$verif->bind_param("i", $id);
$verif->execute();
$res = $verif->get_result();

if ($res->num_rows > 0) {
    echo json_encode(['success' => false, 'msg' => 'El profesor ya tiene un cargo']);
    exit;
}

$stmt = $conexion->prepare("INSERT INTO cargos (profesor_idProrfesor, nombre_cargo, turno) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $id, $cargo, $turno);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'msg' => 'Error al asignar cargo']);
}
$stmt->close();
