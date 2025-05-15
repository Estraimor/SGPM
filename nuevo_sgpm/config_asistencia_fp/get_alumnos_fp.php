<?php
include '../../conexion/conexion.php';

$carreraId = isset($_GET['carrera']) ? intval($_GET['carrera']) : 0;
$año_actual = date('Y');

if ($carreraId <= 0) exit;

$sql = "
  SELECT 
    afp.legajo_afp,
    afp.nombre_afp,
    afp.apellido_afp,
    afp.alumno_legajo,
    a.nombre_alumno,
    a.apellido_alumno
  FROM inscripcion_fp i
  INNER JOIN alumnos_fp afp ON afp.legajo_afp = i.alumnos_fp_legajo_afp
  LEFT JOIN alumno a ON afp.alumno_legajo = a.legajo
  WHERE i.carreras_idCarrera = $carreraId
    AND YEAR(i.fecha_inscripcion) = $año_actual
  ORDER BY 
    CASE 
      WHEN afp.apellido_afp IS NOT NULL AND afp.apellido_afp <> '' THEN afp.apellido_afp
      WHEN a.apellido_alumno IS NOT NULL AND a.apellido_alumno <> '' THEN a.apellido_alumno
      ELSE ''
    END ASC
";

$query = mysqli_query($conexion, $sql);
$alumnos = mysqli_fetch_all($query, MYSQLI_ASSOC);

$index = 1;
foreach ($alumnos as $a) {
  $legajo   = $a['legajo_afp'];

  // Preferencia por nombre/apellido de alumnos_fp, si no existe se usa el de alumno
  $apellido = !empty($a['apellido_afp']) ? $a['apellido_afp'] : $a['apellido_alumno'];
  $nombre   = !empty($a['nombre_afp'])   ? $a['nombre_afp']   : $a['nombre_alumno'];

  $apellido = htmlspecialchars($apellido ?? '');
  $nombre   = htmlspecialchars($nombre ?? '');

  echo "<tr id='fila-legajo-$legajo'>";
  echo "<td>$index</td>";
  echo "<td>$legajo</td>";
  echo "<td>$apellido</td>";
  echo "<td>$nombre</td>";
  echo "<td><input type='radio' name='asistencia[$legajo]' value='1'></td>";
  echo "<td><input type='radio' name='asistencia[$legajo]' value='2'></td>";
  echo "</tr>";
  $index++;
}
