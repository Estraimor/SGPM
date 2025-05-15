<?php
session_start();
include '../../conexion/conexion.php';
$carrera = intval($_GET['carrera']);
$curso = intval($_GET['curso']);
$idProfesor = $_SESSION['id'];
$roles = $_SESSION['roles'];

$sql = ($roles == 1) ?
    "SELECT DISTINCT co.idComisiones, co.comision FROM comisiones co" :
    "SELECT DISTINCT co.idComisiones, co.comision
     FROM preceptores p
     INNER JOIN comisiones co ON p.comisiones_idComisiones = co.idComisiones
     WHERE p.carreras_idCarrera = $carrera AND p.cursos_idCursos = $curso AND p.profesor_idProrfesor = $idProfesor";

$res = mysqli_query($conexion, $sql);
while ($row = mysqli_fetch_assoc($res)) {
    echo "<option value='{$row['idComisiones']}'>{$row['comision']}</option>";
}
