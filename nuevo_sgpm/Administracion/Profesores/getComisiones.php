<?php
include '../../../conexion/conexion.php';

$sql = "SELECT * FROM comisiones";
$resultado = mysqli_query($conexion, $sql);

$comisiones = [];
while ($fila = mysqli_fetch_assoc($resultado)) {
    $comisiones[] = $fila;
}

echo json_encode($comisiones);
?>