<?php
include '../../../../conexion/conexion.php';
$rs = mysqli_query($conexion,
  "SELECT idCarrera, nombre_carrera
   FROM carreras
   ORDER BY nombre_carrera");
$data = mysqli_fetch_all($rs, MYSQLI_ASSOC);
echo json_encode(['data'=>$data]);
