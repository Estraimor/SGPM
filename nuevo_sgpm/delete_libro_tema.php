<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../login/login.php');
    exit();
}

include '../conexion/conexion.php';

// Recibir los parámetros enviados por POST
$fecha   = $_POST['fecha'];   
$materia = intval($_POST['materia']); 
$carrera = intval($_POST['carrera']);

// Usar prepared statement para mayor seguridad
$sql = "DELETE FROM libro_tema 
        WHERE fecha = ? 
          AND profesor_idProrfesor = ? 
          AND carreras_idCarrera = ? 
          AND materias_idMaterias = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo "Error en la preparación: " . $conexion->error;
    exit();
}

$stmt->bind_param('siii', $fecha, $_SESSION['id'], $carrera, $materia);

$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Registro borrado exitosamente.";
} else {
    echo "No se encontró ningún registro para eliminar.";
}

$stmt->close();
?>
