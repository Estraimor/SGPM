<?php
include '../../../conexion/conexion.php';
$legajo_afp = $_POST['legajo_afp'];

// Consultar las carreras en las que está inscrito el alumno
$query = "SELECT c.idCarrera, c.nombre_carrera 
FROM carreras c
INNER JOIN alumnos_fp af 
    ON c.idCarrera = af.carreras_idCarrera 
    OR c.idCarrera = af.carreras_idCarrera1 
    OR c.idCarrera = af.carreras_idCarrera2 
    OR c.idCarrera = af.carreras_idCarrera3
WHERE af.legajo_afp = ?
AND c.idCarrera != 65;";
$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $legajo_afp);
$stmt->execute();
$result = $stmt->get_result();

$carreras = [];
while ($row = $result->fetch_assoc()) {
    $carreras[] = [
        'idCarrera' => $row['idCarrera'],
        'nombre_carrera' => $row['nombre_carrera']
    ];
}

// Consultar las carreras no aprobadas
$query_no_aprobadas = "SELECT carreras_idCarrera FROM no_aprobados WHERE alumnos_fp_legajo_afp = ?";
$stmt_no_aprobadas = $conexion->prepare($query_no_aprobadas);
$stmt_no_aprobadas->bind_param("s", $legajo_afp);
$stmt_no_aprobadas->execute();
$result_no_aprobadas = $stmt_no_aprobadas->get_result();

$carreras_no_aprobadas = [];
while ($row = $result_no_aprobadas->fetch_assoc()) {
    $carreras_no_aprobadas[] = $row['carreras_idCarrera'];
}

// Devolver las carreras y las carreras no aprobadas como un JSON
echo json_encode([
    'carreras' => $carreras,
    'carreras_no_aprobadas' => $carreras_no_aprobadas
]);

$stmt->close();
$stmt_no_aprobadas->close();
$conexion->close();
?>
