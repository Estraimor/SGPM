<?php
include '../../../conexion/conexion.php';

if (isset($_POST['profesorId'], $_POST['materiaId'])) {
    $profesorId = $_POST['profesorId'];
    $materiaId = $_POST['materiaId'];

    // Verificar si la materia está asignada al profesor con ID 12
    $checkQuery = "SELECT profesor_idProrfesor FROM materias WHERE idMaterias = ?";
    $checkStmt = $conexion->prepare($checkQuery);
    $checkStmt->bind_param("i", $materiaId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $currentProfesor = $checkResult->fetch_assoc();

    if ($currentProfesor['profesor_idProrfesor'] != 12) {
        echo json_encode(['error' => 'No se puede asignar la materia de otro profesor asignado.']);
    } else {
        $updateQuery = "UPDATE materias SET profesor_idProrfesor = ? WHERE idMaterias = ?";
        $updateStmt = $conexion->prepare($updateQuery);
        $updateStmt->bind_param("ii", $profesorId, $materiaId);
        if ($updateStmt->execute()) {
            echo json_encode(['message' => 'Materia reasignada correctamente al nuevo profesor.']);
        } else {
            echo json_encode(['error' => 'Error al reasignar la materia: ' . $updateStmt->error]);
        }
        $updateStmt->close();
    }
    $checkStmt->close();
} else {
    echo json_encode(['error' => 'Datos insuficientes para procesar la solicitud']);
}
?>
