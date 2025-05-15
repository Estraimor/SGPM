<?php
include '../../../conexion.php';
header('Content-Type: application/json');

$prof   = intval($_POST['profesor']);
$carrs  = $_POST['carreras'] ?? [];

mysqli_begin_transaction($conexion);
try {
  // 1) borro asignaciones previas
  mysqli_query($conexion,
    "DELETE FROM preceptores
     WHERE profesor_idProrfesor = {$prof}");

  // 2) inserto nuevas
  $stmt = mysqli_prepare($conexion,
    "INSERT INTO preceptores
      (carreras_idCarrera, profesor_idProrfesor, cursos_idCursos, comisiones_idComisiones)
     VALUES (?, ?, ?, ?)");
  foreach($carrs as $idCarrera){
    // aquí deberías decidir también qué curso y comisión asignarás por defecto
    // por simplicidad asumo curso=1 y comision=1:
    mysqli_stmt_bind_param($stmt, 'iiii',
      $idCarrera, $prof, $cursoPorDefecto, $comisionPorDefecto);
    mysqli_stmt_execute($stmt);
  }
  mysqli_commit($conexion);
  echo json_encode(['success'=>true, 'msg'=>'Asignaciones actualizadas']);
} catch(Exception $e){
  mysqli_rollback($conexion);
  echo json_encode(['success'=>false, 'msg'=>$e->getMessage()]);
}
