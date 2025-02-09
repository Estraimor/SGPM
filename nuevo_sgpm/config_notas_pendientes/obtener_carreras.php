<?php
include '../../conexion/conexion.php';
session_start();

$preceptor_id = $_SESSION['id']; // Obtener ID del preceptor de la sesión

$stmt = $conexion->prepare("
    SELECT DISTINCT c.idCarrera, c.nombre_carrera
    FROM carreras c
    JOIN preceptores p ON c.idCarrera = p.carreras_idCarrera
    WHERE p.profesor_idProrfesor = ?
");
$stmt->bind_param("i", $preceptor_id);
$stmt->execute();
$result = $stmt->get_result();

// Asegurar que solo agregamos la opción una vez
$options = "<option value=''>Seleccione una carrera</option>";

$carreras = []; // Evitar duplicados

while ($row = $result->fetch_assoc()) {
    if (!in_array($row['idCarrera'], $carreras)) {
        $options .= "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
        $carreras[] = $row['idCarrera'];
    }
}

echo $options;
$stmt->close();
?>
