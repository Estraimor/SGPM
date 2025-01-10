<?php
include '../../../conexion/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['updates'])) {
    foreach ($data['updates'] as $update) {
        if (!empty($update['desmarcado'])) {
            $materiaId = $update['materiaId'];

            $query = "UPDATE materias SET profesor_idProrfesor = 12 WHERE idMaterias = ?";
            $stmt = $conexion->prepare($query);
            if ($stmt) {
                $stmt->bind_param("i", $materiaId);
                $stmt->execute();
                $stmt->close();
            } else {
                echo json_encode(['error' => 'Error al preparar la consulta']);
                exit;
            }
        }
    }
    echo json_encode(['message' => 'Materias actualizadas exitosamente']);
} else {
    echo json_encode(['error' => 'Datos insuficientes para procesar la solicitud']);
}
?>
