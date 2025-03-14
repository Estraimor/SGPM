<?php
include '../../conexion/conexion.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if (isset($_POST['idMateria']) && isset($_POST['carrera'])) {
    $idMateria = $_POST['idMateria'];
    $idCarrera = $_POST['carrera'];
    $anioCursada = 2023; // Año fijo de consulta

    $estudiantes = [];

    // Obtener estudiantes que en 2023 estaban en segundo año (independiente de la comisión y curso)
    $stmt = $conexion->prepare("
        SELECT DISTINCT a.legajo, a.nombre_alumno, a.apellido_alumno
        FROM alumno a
        JOIN inscripcion_asignatura ia ON ia.alumno_legajo = a.legajo
        WHERE ia.carreras_idCarrera = ?
        AND ia.año_inscripcion = ?
        AND ia.Cursos_idCursos = 2 -- Filtrar solo alumnos que en 2023 estaban en segundo año
        AND a.estado = 1
        ORDER BY a.apellido_alumno, a.nombre_alumno
    ");

    $stmt->bind_param("ii", $idCarrera, $anioCursada);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $estudiantes[] = [
            'legajo' => $row['legajo'],
            'nombre' => $row['nombre_alumno'],
            'apellido' => $row['apellido_alumno'],
            'nota_final' => '',
            'condicion' => ''
        ];
    }

    $stmt->close();
    echo json_encode($estudiantes);
} else {
    echo json_encode(['error' => 'Faltan parámetros en la petición.']);
}
?>
