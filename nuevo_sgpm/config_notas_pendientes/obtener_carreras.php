<?php
include '../../conexion/conexion.php';

// Obtener todas las carreras sin importar el rol
$query = "
    SELECT idCarrera, nombre_carrera
    FROM carreras
";

$stmt = $conexion->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$options = "";
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
