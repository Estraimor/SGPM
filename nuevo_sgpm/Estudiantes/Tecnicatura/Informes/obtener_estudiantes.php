<?php

$server='localhost';
$user='u756746073_root';
$pass='POLITECNICOmisiones2023.';
$bd='u756746073_politecnico';
$conexion=mysqli_connect($server,$user,$pass,$bd, '3306');

if ($conexion) { echo ""; } else { echo "conexion not connected"; }



// Verifica que la conexión se haya establecido
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

$idCarrera = mysqli_real_escape_string($conexion, $_GET['idCarrera']);

// Consulta para obtener los estudiantes de la carrera seleccionada
$sql = "SELECT nombre_alumno, apellido_alumno, dni_alumno 
        FROM alumno a
        INNER JOIN inscripcion_asignatura ia on ia.alumno_legajo = a.legajo
        WHERE ia.carreras_idCarrera = $idCarrera";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

$estudiantes = array();
while($fila = mysqli_fetch_assoc($resultado)) {
    $estudiantes[] = $fila;
}

// Devolver los datos en formato JSON
echo json_encode($estudiantes);
?>
