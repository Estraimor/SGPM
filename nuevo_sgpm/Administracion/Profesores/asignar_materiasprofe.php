<?php
include '../../../conexion/conexion.php';

if (isset($_POST['profesorId'], $_POST['materiaId'])) {
    $profesorId = $_POST['profesorId'];
    $materiaId = $_POST['materiaId'];

    // Consultar quién es el profesor actualmente asignado a la materia
    $checkQuery = "SELECT profesor_idProrfesor FROM materias WHERE idMaterias = ?";
    $checkStmt = $conexion->prepare($checkQuery);
    $checkStmt->bind_param("i", $materiaId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $currentProfesor = $checkResult->fetch_assoc();
    $checkStmt->close();

    // Si la materia ya está asignada a un profesor distinto al que se quiere asignar
    if ($currentProfesor['profesor_idProrfesor'] != $profesorId) {
        // Si no se ha enviado el flag de forzar reasignación, se retorna el warning
        if (!isset($_POST['force']) || $_POST['force'] != true) {
            // Consultar los datos del profesor actual para mostrarlos
            $queryProf = "SELECT apellido_profe, nombre_profe FROM profesor WHERE idProrfesor = ?";
            $stmtProf = $conexion->prepare($queryProf);
            $stmtProf->bind_param("i", $currentProfesor['profesor_idProrfesor']);
            $stmtProf->execute();
            $resultProf = $stmtProf->get_result();
            $currentProfessorDetails = $resultProf->fetch_assoc();
            $stmtProf->close();

            echo json_encode([
                'warning' => 'Esta materia ya está asignada al profesor: ' 
                    . $currentProfessorDetails['apellido_profe'] . ' ' 
                    . $currentProfessorDetails['nombre_profe'] . "\n\n¿Desea confirmar la reasignación?",
                'currentProfessor' => $currentProfessorDetails
            ]);
            exit;
        }
    }

    // Si se envía el flag force o la materia estaba asignada al mismo profesor, se actualiza la asignación
    $updateQuery = "UPDATE materias SET profesor_idProrfesor = ? WHERE idMaterias = ?";
    $updateStmt = $conexion->prepare($updateQuery);
    $updateStmt->bind_param("ii", $profesorId, $materiaId);
    if ($updateStmt->execute()) {
        echo json_encode(['message' => 'Materia reasignada correctamente al nuevo profesor.']);
    } else {
        echo json_encode(['error' => 'Error al reasignar la materia: ' . $updateStmt->error]);
    }
    $updateStmt->close();
} else {
    echo json_encode(['error' => 'Datos insuficientes para procesar la solicitud']);
}
?>
