<?php
include '../../../conexion/conexion.php';

$sql = "SELECT * FROM cursos";
$resultado = mysqli_query($conexion, $sql);

$cursos = [];
while ($fila = mysqli_fetch_assoc($resultado)) {
    $cursos[] = $fila;
}

echo json_encode($cursos);
?>