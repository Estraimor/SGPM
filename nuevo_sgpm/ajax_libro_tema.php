<?php
session_start();
include '../conexion/conexion.php';

$profesor_id = $_SESSION['id'];
$carrera_id = $_GET['carrera'];  // Obtener el id de la carrera desde los parámetros GET
$materia_ids = explode(',', $_GET['materias']);  // Obtener los ids de las materias desde los parámetros GET

// Verificar que los parámetros no estén vacíos
if (!$carrera_id || empty($materia_ids)) {
    echo json_encode([]);  // Devolver un JSON vacío si no se especifica carrera o materia
    exit();
}

// Construir la consulta para filtrar por carrera y materias
$materia_placeholders = implode(',', array_fill(0, count($materia_ids), '?'));  // Placeholder para la consulta IN
$sql = "SELECT c.idCarrera, c.nombre_carrera, m.idMaterias, m.Nombre AS materia_nombre, 
        lt.capacidades, lt.contenidos, lt.evaluacion, lt.fecha, lt.observacion_diaria 
        FROM libro_tema lt
        INNER JOIN carreras c ON lt.carreras_idCarrera = c.idCarrera
        INNER JOIN materias m ON lt.materias_idMaterias = m.idMaterias
        WHERE lt.profesor_idProrfesor = ? 
        AND lt.carreras_idCarrera = ?
        AND lt.materias_idMaterias IN ($materia_placeholders)";

// Preparar la consulta
$stmt = $conexion->prepare($sql);

// Vincular parámetros (id de profesor, id de carrera y lista de ids de materias)
$params = array_merge([$profesor_id, $carrera_id], $materia_ids);
$stmt->bind_param(str_repeat('i', count($params)), ...$params);

// Ejecutar la consulta
$stmt->execute();
$result = $stmt->get_result();

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Devolver los datos como JSON
echo json_encode($data);
?>
