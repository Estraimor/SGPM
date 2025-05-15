<?php
include '../../../conexion/conexion.php';

$carreraId = $_POST['carreraId'];
$cursoId   = $_POST['cursoId'];
$comisionId = $_POST['comisionId'];

$sql = "SELECT m.idMaterias, m.Nombre AS nombre 
        FROM materias m
        WHERE m.carreras_idCarrera = '$carreraId'
          AND m.cursos_idCursos = '$cursoId'
          AND m.comisiones_idComisiones = '$comisionId'";

$resultado = mysqli_query($conexion, $sql);

$materias = [];
while ($fila = mysqli_fetch_assoc($resultado)) {
    $materias[] = $fila;
}

echo json_encode($materias);
?>
