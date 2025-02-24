<?php
include '../../conexion/conexion.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if (isset($_POST['idMateria']) && isset($_POST['anio']) && isset($_POST['comision']) && isset($_POST['curso']) && isset($_POST['carrera'])) {
    $idMateria = $_POST['idMateria'];
    $anio = $_POST['anio'];
    $idComision = $_POST['comision'];
    $idCurso = $_POST['curso'];
    $idCarrera = $_POST['carrera'];

    $estudiantes = [];

    // Verificar si hay notas registradas para el año seleccionado
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as total
        FROM notas n
        WHERE n.materias_idMaterias = ?
            AND n.carreras_idCarrera = ?
            AND YEAR(n.fecha) = ?
    ");
    
    $stmt->bind_param("iii", $idMateria, $idCarrera, $anio);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalNotas = $row['total'] ?? 0;
    $stmt->close();

    if ($totalNotas > 0) {
        // Si hay notas, obtener los estudiantes con sus notas y condición
        $stmt = $conexion->prepare("
            SELECT 
                a.legajo, 
                a.nombre_alumno, 
                a.apellido_alumno,
                COALESCE(n.nota_final, '') AS nota_final,
                COALESCE(n.condicion, '') AS condicion
            FROM alumno a
            LEFT JOIN notas n ON n.alumno_legajo = a.legajo 
                AND n.materias_idMaterias = ?
                AND n.carreras_idCarrera = ?
                AND YEAR(n.fecha) = ?
            JOIN materias m ON m.idMaterias = ?
                AND m.cursos_idCursos = ?
                AND m.comisiones_idComisiones = ?
            WHERE a.estado = 1
            GROUP BY n.alumno_legajo 
            ORDER BY a.apellido_alumno, a.nombre_alumno
        ");

        $stmt->bind_param("iiiiii", $idMateria, $idCarrera, $anio, $idMateria, $idCurso, $idComision);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $estudiantes[] = [
                'legajo' => $row['legajo'],
                'nombre' => $row['nombre_alumno'],
                'apellido' => $row['apellido_alumno'],
                'nota_final' => $row['nota_final'],
                'condicion' => $row['condicion']
            ];
        }
        $stmt->close();

    } else {
        // Si no hay notas, obtener los estudiantes desde la tabla "inscripcion_asignatura"
        $stmt = $conexion->prepare("
            SELECT 
                a.legajo, 
                a.nombre_alumno, 
                a.apellido_alumno
            FROM alumno a
            JOIN inscripcion_asignatura ia ON ia.alumno_legajo = a.legajo
                AND ia.carreras_idCarrera = ?
                AND ia.Cursos_idCursos = ?
                AND ia.Comisiones_idComisiones = ?
                AND ia.año_inscripcion = ?
            WHERE a.estado = 1
            ORDER BY a.apellido_alumno, a.nombre_alumno
        ");

        $stmt->bind_param("iiii", $idCarrera, $idCurso, $idComision, $anio);
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
    }

    echo json_encode($estudiantes);
} else {
    echo json_encode(['error' => 'Faltan parámetros en la petición.']);
}
?>
