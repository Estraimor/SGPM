<?php
session_start();
include '../../conexion/conexion.php';

$profesor_id = $_SESSION['id'];

$sql = "SELECT c.idCarrera, c.nombre_carrera, m.idMaterias, m.Nombre AS materia_nombre, 
        lt.capacidades, lt.contenidos, lt.evaluacion, lt.fecha, lt.observacion_diaria 
        FROM libro_tema lt
        INNER JOIN carreras c ON lt.carreras_idCarrera = c.idCarrera
        INNER JOIN materias m ON lt.materias_idMaterias = m.idMaterias
        WHERE lt.profesor_idProrfesor = '$profesor_id'";

$result = mysqli_query($conexion, $sql);

$data = array();
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
