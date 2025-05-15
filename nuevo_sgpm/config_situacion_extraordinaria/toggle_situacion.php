<?php
include '../../conexion/conexion.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['legajo']) || !isset($input['materia'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos."]);
    exit;
}

$legajo = intval($input['legajo']);
$materia = intval($input['materia']);

// Verificar si ya existe
$sql_check = "SELECT 1 FROM situacion_extraordinaria WHERE alumno_legajo = ? AND materias_idMaterias = ?";
$stmt = $conexion->prepare($sql_check);
$stmt->bind_param("ii", $legajo, $materia);
$stmt->execute();
$existe = $stmt->get_result()->num_rows > 0;

if ($existe) {
    // Si existe, eliminar
    $sql_delete = "DELETE FROM situacion_extraordinaria WHERE alumno_legajo = ? AND materias_idMaterias = ?";
    $stmt = $conexion->prepare($sql_delete);
    $stmt->bind_param("ii", $legajo, $materia);
    $success = $stmt->execute();
    echo json_encode(["success" => $success, "accion" => "removido"]);
} else {
    // Si no existe, insertar
    $sql_insert = "INSERT INTO situacion_extraordinaria (alumno_legajo, materias_idMaterias, fecha) VALUES (?, ?, NOW())";
    $stmt = $conexion->prepare($sql_insert);
    $stmt->bind_param("ii", $legajo, $materia);
    $success = $stmt->execute();
    echo json_encode(["success" => $success, "accion" => "agregado"]);
}
?>
