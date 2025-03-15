<?php
include '../../conexion/conexion.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if (isset($_POST['idMateria']) && isset($_POST['carrera']) && isset($_POST['comision'])) {
    $idMateria = $_POST['idMateria'];
    $idCarrera = $_POST['carrera'];
    $idComision = $_POST['comision']; // Nuevo parámetro recibido
    $anioCursada = 2023; // Año fijo de consulta

    $estudiantes = [];

    // Obtener estudiantes y sus notas si existen
    $stmt = $conexion->prepare("
        SELECT DISTINCT a.legajo, a.nombre_alumno, a.apellido_alumno, 
                        n.nota_final, n.condicion 
        FROM alumno a
        JOIN inscripcion_asignatura ia ON ia.alumno_legajo = a.legajo
        LEFT JOIN notas n ON n.alumno_legajo = a.legajo 
            AND n.materias_idMaterias = ? 
            AND n.carreras_idCarrera = ?
        WHERE ia.carreras_idCarrera = ?
        AND ia.año_inscripcion = ?
        AND ia.Comisiones_idComisiones = ? 
        AND a.estado = 1
        ORDER BY a.apellido_alumno, a.nombre_alumno
    ");

    $stmt->bind_param("iiiii", $idMateria, $idCarrera, $idCarrera, $anioCursada, $idComision);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $estudiantes[] = [
            'legajo' => $row['legajo'],
            'nombre' => $row['nombre_alumno'],
            'apellido' => $row['apellido_alumno'],
            'nota_final' => $row['nota_final'] ?? '',
            'condicion' => $row['condicion'] ?? ''
        ];
    }

    $stmt->close();
    echo json_encode($estudiantes);
} else {
    echo json_encode(['error' => 'Faltan parámetros en la petición.']);
}
?>
