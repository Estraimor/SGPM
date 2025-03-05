<?php

$server = 'localhost';
$user = 'u756746073_root';
$pass = 'POLITECNICOmisiones2023.';
$bd = 'u756746073_politecnico';
$conexion = mysqli_connect($server, $user, $pass, $bd, '3306');

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener los parámetros de la solicitud GET y asegurarse de que sean enteros
$idCarrera = isset($_GET['idCarrera']) ? intval($_GET['idCarrera']) : 0;
$idCurso = isset($_GET['idCurso']) ? intval($_GET['idCurso']) : 0;
$idComision = isset($_GET['idComision']) ? intval($_GET['idComision']) : 0;

// Validar que los parámetros sean mayores a 0
if ($idCarrera > 0 && $idCurso > 0 && $idComision > 0) {
    // Consulta para obtener los estudiantes matriculados en la carrera, curso y comisión seleccionados
    $sql = "SELECT DISTINCT a.nombre_alumno, a.apellido_alumno, a.dni_alumno
            FROM matriculacion_materias mm
            INNER JOIN materias m ON mm.materias_idMaterias = m.idMaterias
            INNER JOIN alumno a ON mm.alumno_legajo = a.legajo
            WHERE m.carreras_idCarrera = ? 
            AND m.cursos_idCursos = ? 
            AND m.comisiones_idComisiones = ?
            ORDER BY a.apellido_alumno";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $idCarrera, $idCurso, $idComision);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $estudiantes = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $estudiantes[] = $fila;
    }

    echo json_encode($estudiantes);
} else {
    echo json_encode(["error" => "Parámetros inválidos"]);
}

?>
