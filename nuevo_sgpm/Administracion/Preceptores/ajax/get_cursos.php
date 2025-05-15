<?php
// ajax/get_cursos.php
header('Content-Type: application/json; charset=utf-8');
include '../../../../conexion/conexion.php';

// Traigo todos los cursos (años)
$rs = mysqli_query($conexion,
  "SELECT idCursos, curso
   FROM cursos
   ORDER BY curso"
);
if (!$rs) {
  echo json_encode(['data'=>[], 'error'=> mysqli_error($conexion)]);
  exit;
}

$data = mysqli_fetch_all($rs, MYSQLI_ASSOC);
echo json_encode(['data' => $data]);