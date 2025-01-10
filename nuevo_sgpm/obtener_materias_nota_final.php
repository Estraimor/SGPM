<?php
include '../conexion/conexion.php';

if (isset($_POST['carrera_id'])) {
    $carrera_id = $_POST['carrera_id'];
    
    $sqlMaterias = "SELECT * FROM materias WHERE carreras_idCarrera = $carrera_id";
    $resultMaterias = mysqli_query($conexion, $sqlMaterias);

    $materias = [];
    while ($row = mysqli_fetch_assoc($resultMaterias)) {
        $materias[] = $row;
    }

    echo json_encode($materias);
}
?>
