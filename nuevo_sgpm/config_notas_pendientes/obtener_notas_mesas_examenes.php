<?php
include '../../conexion/conexion.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if (isset($_POST['idMateria']) && isset($_POST['carrera']) && isset($_POST['comision']) && isset($_POST['turno'])) {
    $idMateria = $_POST['idMateria'];
    $idCarrera = $_POST['carrera'];
    $idComision = $_POST['comision'];
    $turno = $_POST['turno'];
    $anioCursada = 2023;

    $estudiantes = [];

    $stmt = $conexion->prepare("
        SELECT DISTINCT 
            a.legajo, a.nombre_alumno, a.apellido_alumno,
            ef1.nota AS nota1, ef1.tomo AS tomo1, ef1.folio AS folio1,
            ef2.nota AS nota2, ef2.tomo AS tomo2, ef2.folio AS folio2
        FROM alumno a
        JOIN inscripcion_asignatura ia ON ia.alumno_legajo = a.legajo
        LEFT JOIN nota_examen_final ef1 ON ef1.alumno_legajo = a.legajo 
            AND ef1.materias_idMaterias = ? 
            AND ef1.turno = ? 
            AND ef1.llamado = 1
        LEFT JOIN nota_examen_final ef2 ON ef2.alumno_legajo = a.legajo 
            AND ef2.materias_idMaterias = ? 
            AND ef2.turno = ? 
            AND ef2.llamado = 2
        WHERE ia.carreras_idCarrera = ?
        AND ia.año_inscripcion = ?
        AND ia.Comisiones_idComisiones = ?
        AND a.estado = 1
        ORDER BY a.apellido_alumno, a.nombre_alumno
    ");

    $stmt->bind_param("iiiiiii", $idMateria, $turno, $idMateria, $turno, $idCarrera, $anioCursada, $idComision);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $nota1 = $row['nota1'];
        $nota2 = $row['nota2'];

        $estudiantes[] = [
            'legajo' => $row['legajo'],
            'nombre' => $row['nombre_alumno'],
            'apellido' => $row['apellido_alumno'],
            'nota1' => ($nota1 === '0.00' || $nota1 === 0 || $nota1 === '0') ? 'A' : $nota1,
            'tomo1' => ($nota1 === '0.00' || $nota1 === 0 || $nota1 === '0') ? '' : $row['tomo1'],
            'folio1' => ($nota1 === '0.00' || $nota1 === 0 || $nota1 === '0') ? '' : $row['folio1'],
            'bloquear1' => ($nota1 === '0.00' || $nota1 === 0 || $nota1 === '0'),

            'nota2' => ($nota2 === '0.00' || $nota2 === 0 || $nota2 === '0') ? 'A' : $nota2,
            'tomo2' => ($nota2 === '0.00' || $nota2 === 0 || $nota2 === '0') ? '' : $row['tomo2'],
            'folio2' => ($nota2 === '0.00' || $nota2 === 0 || $nota2 === '0') ? '' : $row['folio2'],
            'bloquear2' => ($nota2 === '0.00' || $nota2 === 0 || $nota2 === '0'),
        ];
    }

    $stmt->close();
    echo json_encode($estudiantes);
} else {
    echo json_encode(['error' => 'Faltan parámetros.']);
}
?>
