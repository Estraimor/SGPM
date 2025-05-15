<?php
include '../../conexion/conexion.php';
session_start();

$idCarrera = intval($_GET['carrera']);
$idProfesor = $_SESSION['id'];
$roles = $_SESSION['roles'];

if ($idCarrera <= 0) exit;

if ($roles == 1) {
    $query = "SELECT DISTINCT cur.idCursos, cur.curso 
              FROM materias m 
              INNER JOIN cursos cur ON m.cursos_idCursos = cur.idCursos 
              WHERE m.carreras_idCarrera = $idCarrera 
              ORDER BY cur.curso";
} else {
    $query = "SELECT DISTINCT cur.idCursos, cur.curso 
              FROM materias m 
              INNER JOIN cursos cur ON m.cursos_idCursos = cur.idCursos 
              INNER JOIN preceptores p ON 
                   p.carreras_idCarrera = m.carreras_idCarrera 
               AND p.cursos_idCursos = m.cursos_idCursos 
              WHERE m.carreras_idCarrera = $idCarrera 
              AND p.profesor_idProrfesor = $idProfesor 
              ORDER BY cur.curso";
}

$res = mysqli_query($conexion, $query);
while ($row = mysqli_fetch_assoc($res)) {
    echo "<option value='{$row['idCursos']}'>{$row['curso']}</option>";
}
?>
