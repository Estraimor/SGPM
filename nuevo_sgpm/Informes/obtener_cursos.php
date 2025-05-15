<?php
header('Content-Type: application/json; charset=utf-8');
include '../../conexion/conexion.php';
session_start();
$idPreceptor = $_SESSION['id'];
$rolUsuario = $_SESSION["roles"];

if (!isset($_GET['idCarrera'])) {
    echo json_encode(['error' => 'Parámetro carrera requerido']);
    exit;
}

$idCarrera = intval($_GET['idCarrera']);

try {
    if ($rolUsuario == 1) {
        $sql = "SELECT DISTINCT c.idCursos, c.curso
                FROM materias m
                INNER JOIN cursos c ON m.cursos_idCursos = c.idCursos
                WHERE m.carreras_idCarrera = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $idCarrera);
    } else {
        $sql = "SELECT DISTINCT c.idCursos, c.curso
                FROM materias m
                INNER JOIN cursos c ON m.cursos_idCursos = c.idCursos
                INNER JOIN preceptores p
                  ON p.cursos_idCursos = c.idCursos
                 AND p.profesor_idProrfesor = ?
                WHERE m.carreras_idCarrera = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ii', $idPreceptor, $idCarrera);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $cursos = [];
    while ($row = $result->fetch_assoc()) {
        $cursos[] = $row;
    }
    echo json_encode($cursos);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
