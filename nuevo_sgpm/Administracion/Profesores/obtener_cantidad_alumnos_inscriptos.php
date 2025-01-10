<?php
include'../../../conexion/conexion.php';

$idMateria = intval($_GET['idMateria']);
$idCarrera = intval($_GET['idCarrera']);

// Consulta para obtener la cantidad de alumnos inscritos
$sqlCount = "SELECT COUNT(*) as total FROM mesas_finales WHERE materias_idMaterias = ?";
$stmtCount = $conexion->prepare($sqlCount);
$stmtCount->bind_param('i', $idMateria);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalAlumnos = ($resultCount->fetch_assoc())['total'];

// Consulta para obtener detalles de los alumnos inscritos
$sqlDetails = "SELECT a.nombre_alumno, a.apellido_alumno, a.dni_alumno 
               FROM alumno a 
               JOIN mesas_finales mf ON a.legajo = mf.alumno_legajo
               JOIN materias m ON mf.materias_idMaterias = m.idMaterias
               WHERE m.carreras_idCarrera = ? AND m.idMaterias = ?";
$stmtDetails = $conexion->prepare($sqlDetails);
$stmtDetails->bind_param('ii', $idCarrera, $idMateria);
$stmtDetails->execute();
$resultDetails = $stmtDetails->get_result();

if ($totalAlumnos > 0) {
    echo "<p>Total de alumnos inscritos: $totalAlumnos</p>";
    $contador = 1;
    while ($row = $resultDetails->fetch_assoc()) {
        echo "<div>{$contador}. {$row['nombre_alumno']} {$row['apellido_alumno']} - DNI: {$row['dni_alumno']}</div>";
        $contador++;
    }
} else {
    echo '<p>No hay alumnos inscritos en esta materia para la carrera seleccionada.</p>';
}
?>
