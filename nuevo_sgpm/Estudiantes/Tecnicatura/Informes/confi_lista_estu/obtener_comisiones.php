<?php
header('Content-Type: application/json'); // Forzar respuesta en JSON
include '../../../../../conexion/conexion.php'; // Ajusta la ruta según tu estructura

session_start();
$idPreceptor = $_SESSION['id'];
$rolUsuario = $_SESSION["roles"];

if (isset($_GET['idCarrera']) && isset($_GET['idCurso'])) {
    $idCarrera = intval($_GET['idCarrera']);
    $idCurso = intval($_GET['idCurso']);

    if ($rolUsuario == 1) {
        $sql = "SELECT DISTINCT co.idComisiones, co.comision 
                FROM materias m
                INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones
                WHERE m.carreras_idCarrera = ? AND m.cursos_idCursos = ?";
    } else {
        $sql = "SELECT DISTINCT co.idComisiones, co.comision 
                FROM materias m
                INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones
                INNER JOIN preceptores p ON m.comisiones_idComisiones = p.comisiones_idComisiones 
                                         AND m.cursos_idCursos = p.cursos_idCursos
                WHERE m.carreras_idCarrera = ? AND m.cursos_idCursos = ? 
                AND p.profesor_idProfesor = ?";
    }

    $stmt = mysqli_prepare($conexion, $sql);
    
    if ($rolUsuario == 1) {
        mysqli_stmt_bind_param($stmt, "ii", $idCarrera, $idCurso);
    } else {
        mysqli_stmt_bind_param($stmt, "iii", $idCarrera, $idCurso, $idPreceptor);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $comisiones = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comisiones[] = $row;
    }

    echo json_encode($comisiones);
} else {
    echo json_encode(["error" => "Parámetros incorrectos"]);
}
?>
