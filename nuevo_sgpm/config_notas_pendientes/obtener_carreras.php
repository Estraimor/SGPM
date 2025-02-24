<?php
include '../../conexion/conexion.php';
session_start();

$preceptor_id = $_SESSION['id']; // Obtener ID del preceptor de la sesión
$rol = $_SESSION['roles']; // Obtener el rol de la sesión

if ($rol == 1) {
    // Si el rol es 1, mostrar todas las carreras
    $query = "
        SELECT DISTINCT idCarrera, nombre_carrera
        FROM carreras
    ";
    $stmt = $conexion->prepare($query);
} else {
    // Si no, mostrar solo las carreras asignadas al preceptor
    $query = "
        SELECT DISTINCT c.idCarrera, c.nombre_carrera
        FROM carreras c
        JOIN preceptores p ON c.idCarrera = p.carreras_idCarrera
        WHERE p.profesor_idProrfesor = ?
    ";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $preceptor_id);
}

$stmt->execute();
$result = $stmt->get_result();

$carreras = []; // Para evitar duplicados

while ($row = $result->fetch_assoc()) {
    if (!in_array($row['idCarrera'], $carreras)) {
        $options .= "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
        $carreras[] = $row['idCarrera'];
    }
}

echo $options;
$stmt->close();
?>
