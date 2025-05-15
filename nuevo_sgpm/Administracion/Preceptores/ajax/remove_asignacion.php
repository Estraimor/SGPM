<?php
// ajax/remove_asignacion.php
include '../../../../conexion/conexion.php';
header('Content-Type: application/json');

$prof    = intval($_POST['profesor']);
$idCarr  = intval($_POST['carrera']);
$idCurso = intval($_POST['curso']);
$idCom   = intval($_POST['comision']);

$sql = "
  DELETE FROM preceptores
   WHERE profesor_idProrfesor={$prof}
     AND carreras_idCarrera={$idCarr}
     AND cursos_idCursos={$idCurso}
     AND comisiones_idComisiones={$idCom}
";
if (mysqli_query($conexion,$sql)) {
  echo json_encode(['success'=>true]);
} else {
  echo json_encode(['success'=>false,'msg'=>mysqli_error($conexion)]);
}
