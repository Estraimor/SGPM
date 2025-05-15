<?php
// ajax/get_comisiones.php
header('Content-Type: application/json; charset=utf-8');
include '../../../../conexion/conexion.php';

// Consulta de todas las comisiones
$rs = mysqli_query($conexion,
  "SELECT idComisiones, comision
   FROM comisiones
   ORDER BY comision"
);
if (!$rs) {
  echo json_encode(['data'=>[], 'error'=>mysqli_error($conexion)]);
  exit;
}

$data = mysqli_fetch_all($rs, MYSQLI_ASSOC);
echo json_encode(['data' => $data]);
