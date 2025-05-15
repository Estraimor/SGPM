<?php
include '../../../../conexion/conexion.php';
$rs = mysqli_query($conexion,
  "SELECT 
     idProrfesor   AS idProfesor,
     apellido_profe,
     nombre_profe
   FROM profesor
   ORDER BY apellido_profe, nombre_profe");

$data = mysqli_fetch_all($rs, MYSQLI_ASSOC);
echo json_encode(['data' => $data]);