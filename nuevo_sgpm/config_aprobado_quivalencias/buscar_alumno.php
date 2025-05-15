<?php
include '../../conexion/conexion.php';

$query = $_GET['query'] ?? '';
if (strlen($query) < 3) exit;

$sql = "SELECT legajo, nombre_alumno, apellido_alumno, dni_alumno 
        FROM alumno 
        WHERE dni_alumno LIKE ? OR nombre_alumno LIKE ? OR apellido_alumno LIKE ?
        ORDER BY apellido_alumno LIMIT 50";

$stmt = $conexion->prepare($sql);
$like = "%$query%";
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div onclick='seleccionarAlumno({$row['legajo']})' style='cursor:pointer; padding:5px; border-bottom:1px solid #ccc'>
            <strong>{$row['apellido_alumno']}, {$row['nombre_alumno']}</strong> - DNI: {$row['dni_alumno']}
          </div>";
}
