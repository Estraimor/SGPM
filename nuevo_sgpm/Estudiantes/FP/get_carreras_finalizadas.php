<?php
include '../../../conexion/conexion.php';
$legajo_afp = $_POST['legajo_afp'];

$carreras_finalizadas = [];

$sql = "SELECT c.idCarrera, c.nombre_carrera 
        FROM finalizo_FP f
        JOIN carreras c ON f.carreras_idCarrera = c.idCarrera
        WHERE f.alumnos_fp_legajo_afp = '$legajo_afp'";

$query = mysqli_query($conexion, $sql);

while ($row = mysqli_fetch_assoc($query)) {
    $carreras_finalizadas[] = array(
        'id' => $row['idCarrera'],
        'nombre' => $row['nombre_carrera']
    );
}

echo json_encode($carreras_finalizadas);
?>