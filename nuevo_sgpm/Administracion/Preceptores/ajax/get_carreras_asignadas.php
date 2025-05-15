<?php
include '../../../../conexion/conexion.php';
$prof = intval($_GET['profesor']);
$sql = "
  SELECT carreras_idCarrera AS idCarrera
  FROM preceptores
  WHERE profesor_idProrfesor = {$prof}
";
$rs = mysqli_query($conexion,$sql);
echo json_encode(mysqli_fetch_all($rs, MYSQLI_ASSOC));
