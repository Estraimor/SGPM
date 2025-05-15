<?php
include '../../conexion/conexion.php';

$legajo = intval($_GET['legajo'] ?? 0);

$sql = "SELECT * FROM alumno WHERE legajo = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $legajo);
$stmt->execute();
$alumno = $stmt->get_result()->fetch_assoc();

echo "<h4>{$alumno['apellido_alumno']}, {$alumno['nombre_alumno']}</h4>";
echo "<p><strong>DNI:</strong> {$alumno['dni_alumno']}</p>";
echo "<p><strong>Legajo:</strong> {$alumno['legajo']}</p>";

// Ahora buscamos en qué carrera está
$sqlCarrera = "SELECT DISTINCT carreras_idCarrera, Cursos_idCursos, Comisiones_idComisiones 
               FROM inscripcion_asignatura 
               WHERE alumno_legajo = ? ORDER BY idinscripcion_asignatura DESC LIMIT 1";

$stmt2 = $conexion->prepare($sqlCarrera);
$stmt2->bind_param("i", $legajo);
$stmt2->execute();
$insc = $stmt2->get_result()->fetch_assoc();

echo "<p><strong>Carrera ID:</strong> {$insc['carreras_idCarrera']}</p>";
echo "<p><strong>Curso:</strong> {$insc['Cursos_idCursos']}</p>";
echo "<p><strong>Comisión:</strong> {$insc['Comisiones_idComisiones']}</p>";
echo "<input type='hidden' id='idCarrera' value='{$insc['carreras_idCarrera']}'>";

