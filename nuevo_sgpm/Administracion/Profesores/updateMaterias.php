<?php
include '../../../conexion/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['updates']) || !is_array($data['updates'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

$asignado = 0;

foreach ($data['updates'] as $update) {
    if (
        isset($update['materiaId'], $update['desmarcado']) &&
        is_numeric($update['materiaId']) &&
        $update['desmarcado'] === true
    ) {
        $materiaId = (int)$update['materiaId'];
        $idReservado = 12; // ID del profesor "sin asignar"

        $query = "UPDATE materias SET profesor_idProrfesor = ? WHERE idMaterias = ?";
        $stmt = $conexion->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ii", $idReservado, $materiaId);
            if ($stmt->execute()) {
                $asignado++;
            }
            $stmt->close();
        }
    }
}

if ($asignado > 0) {
    echo json_encode(['message' => "Se reasignaron $asignado materia(s) al profesor ID 12."]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'No se actualizaron materias.']);
}
?>
