<?php
include '../../conexion/conexion.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$legajo = intval($data['legajo'] ?? 0);
$materia = intval($data['materia'] ?? 0);
$año = intval($data['año'] ?? 0);

if ($legajo > 0 && $materia > 0 && $año > 0) {
    $sql = "INSERT INTO matriculacion_materias (alumno_legajo, materias_idMaterias, año_matriculacion) 
            VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iis", $legajo, $materia, $fechaMat);

    $fechaMat = $año . "-02-13"; // como el resto

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}
