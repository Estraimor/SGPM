<?php
include '../../conexion/conexion.php';
session_start();

$preceptor_id = $_SESSION['id']; // ID del preceptor de la sesión
$rol = $_SESSION['roles']; // Rol de la sesión
$carrera_id = isset($_POST['carrera']) ? $_POST['carrera'] : '';

if (!$carrera_id) {
    echo "<option value=''>Seleccione un curso</option>";
    exit;
}

// Si el rol es 1, mostrar todos los cursos
if ($rol == 1) {
    $stmt = $conexion->prepare("
        SELECT DISTINCT idCursos, curso 
        FROM cursos 
        WHERE cursos.idCursos IN (
            SELECT cursos_idCursos 
            FROM preceptores 
            WHERE carreras_idCarrera = ?
        )
    ");
    $stmt->bind_param("i", $carrera_id);
} else {
    // Si no, solo los asignados al preceptor
    $stmt = $conexion->prepare("
        SELECT DISTINCT cu.idCursos, cu.curso
        FROM cursos cu
        JOIN preceptores p ON cu.idCursos = p.cursos_idCursos
        WHERE p.profesor_idProrfesor = ? 
        AND p.carreras_idCarrera = ?
    ");
    $stmt->bind_param("ii", $preceptor_id, $carrera_id);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $options .= "<option value='{$row['idCursos']}'>{$row['curso']}</option>";
}

echo $options;
$stmt->close();
?>
