<?php

include '../../../../conexion/conexion.php';
header('Content-Type: application/json');

$prof     = intval($_POST['profesor']);
$idCarr   = intval($_POST['carrera']);
$idCurso  = intval($_POST['curso']);
$idCom    = intval($_POST['comision']);

// Evitamos duplicados
$chk = mysqli_query($conexion,
  "SELECT 1 FROM preceptores
   WHERE profesor_idProrfesor={$prof}
     AND carreras_idCarrera={$idCarr}
     AND cursos_idCursos={$idCurso}
     AND comisiones_idComisiones={$idCom}"
);
if (mysqli_fetch_row($chk)) {
  echo json_encode(['success'=>false,'msg'=>'Ya existe esa asignación']);
  exit;
}

$sql = "
  INSERT INTO preceptores
    (carreras_idCarrera, profesor_idProrfesor, cursos_idCursos, comisiones_idComisiones)
  VALUES
    ({$idCarr}, {$prof}, {$idCurso}, {$idCom})
";
if (mysqli_query($conexion,$sql)) {
  echo json_encode(['success'=>true,'msg'=>'Asignación creada']);
} else {
  echo json_encode(['success'=>false,'msg'=>mysqli_error($conexion)]);
}
