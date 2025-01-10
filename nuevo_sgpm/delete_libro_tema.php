<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../login/login.php');
    exit();
}

include '../conexion/conexion.php';

$fecha = $_POST['fecha'];
$materia = $_POST['materia'];
$carrera = $_POST['carrera'];

// Consulta DELETE
$sql = "DELETE FROM libro_tema 
        WHERE fecha = '$fecha' 
        AND profesor_idProrfesor = '{$_SESSION['id']}' 
        AND carreras_idCarrera = '$carrera' 
        AND materias_idMaterias = '$materia'";


if (mysqli_query($conexion, $sql)) {
    if (mysqli_affected_rows($conexion) > 0) {
        echo "Registro borrado exitosamente.";
    } else {
        echo "No se encontró ningún registro para eliminar.";
    }
} else {
    echo "Error al borrar el registro: " . mysqli_error($conexion);
}

?>
