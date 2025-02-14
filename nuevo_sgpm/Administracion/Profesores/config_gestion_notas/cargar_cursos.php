<?php
include "conexion.php";
$carreraId = $_GET['carreraId'];
$query = "SELECT idCurso, nombreCurso FROM cursos WHERE idCarrera = '$carreraId'";
$result = mysqli_query($conexion, $query);

$cursos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cursos[] = $row;
}
echo json_encode($cursos);
?>
