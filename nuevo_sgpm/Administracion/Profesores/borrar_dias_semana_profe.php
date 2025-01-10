<?php
include '../../../conexion/conexion.php';

if (isset($_POST['materiaId'], $_POST['diaId'])) {
    $materiaId = $_POST['materiaId'];
    $diaId = $_POST['diaId'];

    $query = "DELETE FROM dias_semana_has_materias WHERE materias_idMaterias = ? AND dias_semana_idDias_semana = ?";
    $stmt = $conexion->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ii", $materiaId, $diaId);
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Registro eliminado correctamente.']);
        } else {
            echo json_encode(['error' => 'Error al eliminar el registro: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error al preparar la consulta']);
    }
} else {
    echo json_encode(['error' => 'Faltan datos necesarios para la eliminación']);
}
?>
