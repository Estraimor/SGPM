<?php
include '../../../conexion/conexion.php';

if (isset($_POST['materiaId'], $_POST['diaId'], $_POST['entrada'], $_POST['salida'])) {
    $materiaId = $_POST['materiaId'];
    $diaId = $_POST['diaId'];
    $entrada = $_POST['entrada'];
    $salida = $_POST['salida'];

    $query = "REPLACE INTO dias_semana_has_materias (dias_semana_idDias_semana, materias_idMaterias, horario_entrada, horario_salida) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($query);
    if ($stmt) {
        $stmt->bind_param("iiss", $diaId, $materiaId, $entrada, $salida);
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Horarios actualizados correctamente.']);
        } else {
            echo json_encode(['error' => 'Error al guardar el horario: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error al preparar la consulta']);
    }
} else {
    echo json_encode(['error' => 'Faltan datos necesarios para la operación']);
}

?>
