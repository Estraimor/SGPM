<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../login/login.php');
    exit();
}

include '../conexion/conexion.php';

$original_fecha = $_POST['original_fecha'];
$capacidades = $_POST['capacidades'];
$contenidos = $_POST['contenidos'];
$evaluacion = $_POST['evaluacion'];
$observacion_diaria = $_POST['observacion_diaria'];
$materia = $_POST['materia']; // Aquí obtienes el ID de la materia
$carrera = $_POST['carrera']; // Aquí obtienes el ID de la carrera

// Consulta preparada para evitar inyecciones SQL
$sql = "UPDATE libro_tema lt SET 
        lt.capacidades = ?, 
        lt.contenidos = ?, 
        lt.evaluacion = ?, 
        lt.observacion_diaria = ?
        WHERE lt.fecha = ? 
        AND lt.profesor_idProrfesor = ? 
        AND lt.carreras_idCarrera = ? 
        AND lt.materias_idMaterias = ?";

// Preparar la consulta
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssssss", $capacidades, $contenidos, $evaluacion, $observacion_diaria, $original_fecha, $_SESSION['id'], $carrera, $materia);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registro actualizado exitosamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar el registro: " . $stmt->error]);
}

// Cerrar la conexión y la consulta preparada
$stmt->close();
mysqli_close($conexion);
?>
