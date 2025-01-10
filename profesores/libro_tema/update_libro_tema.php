<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../../login/login.php');
    exit();
}

include '../../conexion/conexion.php';

$original_fecha = $_POST['original_fecha'];
$capacidades = $_POST['capacidades'];
$contenidos = $_POST['contenidos'];
$evaluacion = $_POST['evaluacion'];
$observacion_diaria = $_POST['observacion_diaria'];
$materia = $_POST['materia']; // Aquí obtienes el ID de la materia
$carrera = $_POST['carrera']; // Aquí obtienes el ID de la carrera

// Agregar mensajes de depuración
echo "Datos recibidos: <br>";
echo "Fecha original: $original_fecha<br>";
echo "Capacidades: $capacidades<br>";
echo "Contenidos: $contenidos<br>";
echo "Evaluación: $evaluacion<br>";
echo "Observación diaria: $observacion_diaria<br>";
echo "Materia: $materia<br>";
echo "Carrera: $carrera<br>";

$sql = "UPDATE libro_tema lt SET 
        lt.capacidades = '$capacidades', 
        lt.contenidos = '$contenidos', 
        lt.evaluacion = '$evaluacion', 
        lt.observacion_diaria = '$observacion_diaria'
        WHERE lt.fecha = '$original_fecha' AND lt.profesor_idProrfesor = '{$_SESSION['id']}' 
        AND lt.carreras_idCarrera = '$carrera' 
        AND lt.materias_idMaterias = '$materia'";

if (mysqli_query($conexion, $sql)) {
    echo "Registro actualizado exitosamente";
} else {
    echo "Error al actualizar el registro: " . mysqli_error($conexion);
}

mysqli_close($conexion);