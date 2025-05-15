<?php
include '../../conexion/conexion.php';

$q = $_GET['q'] ?? '';
$q = $conexion->real_escape_string($q);

$sql = "SELECT 
    nombre_alumno, apellido_alumno, dni_alumno,legajo, Trabaja_Horario, edad, celular, 
    cuil, discapacidad, fecha_nacimiento, Titulo_secundario, escuela_secundaria, 
    materias_adeuda, fecha_estimacion, ocupacion, domicilio_laboral, 
    horario_laboral_desde, horario_laboral_hasta, calle_domicilio, barrio_domicilio, 
    numeracion_domicilio, telefono_urgencias, correo 
FROM alumno 
WHERE dni_alumno LIKE '%$q%' 
   OR nombre_alumno LIKE '%$q%' 
   OR apellido_alumno LIKE '%$q%' 
LIMIT 10";

$resultado = $conexion->query($sql);
$alumnos = [];

while ($fila = $resultado->fetch_assoc()) {
    $alumnos[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($alumnos);
