<?php
 include'../../../../../conexion/conexion.php';

$carreraId = intval($_POST['carreraId']);
$cursoId = intval($_POST['cursoId']);

// Consulta para obtener solo las comisiones activas de la carrera seleccionada y el curso (primer año)
$queryComisiones = "
    SELECT com.idComisiones, com.comision 
    FROM comisiones com
    INNER JOIN materias mat ON mat.comisiones_idComisiones = com.idComisiones
    WHERE mat.carreras_idCarrera = $carreraId 
      AND mat.cursos_idCursos = $cursoId
      AND mat.estado = 1
      GROUP BY com.idComisiones
";

$resultComisiones = mysqli_query($conexion, $queryComisiones);

// Generar las opciones para el select de comisiones
$options = '<option hidden>Selecciona una Comisión</option>';
while ($comision = mysqli_fetch_assoc($resultComisiones)) {
    $options .= '<option value="' . $comision['idComisiones'] . '">' . $comision['comision'] . '</option>';
}

echo $options;
?>
