<?php
include '../../../conexion/conexion.php';
$query = "SELECT c.idCarrera,c.nombre_carrera FROM carreras c";
$result = $conexion->query($query);
$carreras = [];
while ($row = $result->fetch_assoc()) {
    $carreras[] = $row;
}
echo json_encode($carreras);
?>
