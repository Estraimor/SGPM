<?php
include '../../../../conexion/conexion.php';

$prof = intval($_GET['profesor']);
$sql = "
  SELECT 
    p.carreras_idCarrera AS idCarrera,
    p.cursos_idCursos    AS idCurso,
    p.comisiones_idComisiones AS idComision,
    c.nombre_carrera,
    cu.curso,
    co.comision
  FROM preceptores p
  JOIN carreras c   ON p.carreras_idCarrera     = c.idCarrera
  JOIN cursos cu     ON p.cursos_idCursos        = cu.idCursos
  JOIN comisiones co ON p.comisiones_idComisiones = co.idComisiones
  WHERE p.profesor_idProrfesor = {$prof}
";
$rs   = mysqli_query($conexion, $sql);
$data = mysqli_fetch_all($rs, MYSQLI_ASSOC);
echo json_encode($data);
