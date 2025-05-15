<?php
session_start();
include '../conexion/conexion.php';

$profesor_id = $_SESSION['id'] ?? 0;
$rol         = $_SESSION['roles'] ?? 0;

$carrera_id = isset($_GET['carrera']) ? intval($_GET['carrera']) : 0;
$anio       = isset($_GET['anio']) ? intval($_GET['anio']) : 0;

$materia_ids_raw = isset($_GET['materias']) ? $_GET['materias'] : '';
$materia_ids     = array_filter(array_map('intval', explode(',', $materia_ids_raw)));

// Validación
if (!$carrera_id || empty($materia_ids) || !$anio) {
    echo json_encode([]);
    exit();
}

// Placeholders para IN
$placeholders = implode(',', array_fill(0, count($materia_ids), '?'));

// SQL base
$sql = "SELECT  
            c.idCarrera, 
            c.nombre_carrera, 
            m.idMaterias, 
            m.Nombre AS materia_nombre, 
            lt.capacidades, 
            lt.contenidos, 
            lt.evaluacion, 
            lt.fecha,  
            lt.observacion_diaria 
        FROM libro_tema lt
        INNER JOIN carreras c ON lt.carreras_idCarrera = c.idCarrera
        INNER JOIN materias m ON lt.materias_idMaterias = m.idMaterias
        WHERE lt.carreras_idCarrera = ?
          AND YEAR(lt.fecha) = ?
          AND lt.materias_idMaterias IN ($placeholders)";

// Si no es rol 1, filtrar por profesor
if ($rol != 1) {
    $sql .= " AND lt.profesor_idProrfesor = ?";
}

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => $conexion->error]);
    exit();
}

// Armar tipos y parámetros
$baseParams = [$carrera_id, $anio];
$types = str_repeat('i', count($baseParams) + count($materia_ids) + ($rol != 1 ? 1 : 0));
$params = array_merge($baseParams, $materia_ids);
if ($rol != 1) {
    $params[] = $profesor_id;
}

// Ejecutar consulta
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
exit;
?>
