<?php
//obtener_estudiantes.php
$server = 'localhost';
$user = 'u756746073_root';
$pass = 'POLITECNICOmisiones2023.';
$bd = 'u756746073_politecnico';
$conexion = mysqli_connect($server, $user, $pass, $bd, '3306');

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

header('Content-Type: application/json');
$estudiantes = [];

// 1) Filtro por materia si viene
if (isset($_GET['idMateria'])) {
    $idMateria = intval($_GET['idMateria']);

    $sql = "SELECT DISTINCT a.nombre_alumno, a.apellido_alumno, a.dni_alumno
            FROM matriculacion_materias mm
            INNER JOIN alumno a ON mm.alumno_legajo = a.legajo
            WHERE mm.materias_idMaterias = ?
            ORDER BY a.apellido_alumno";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idMateria);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    while ($fila = mysqli_fetch_assoc($resultado)) {
        $estudiantes[] = $fila;
    }

    echo json_encode($estudiantes);
    exit;
}

// 2) Filtro por carrera + curso + comisión si no hay idMateria
$idCarrera  = isset($_GET['idCarrera'])  ? intval($_GET['idCarrera'])  : 0;
$idCurso    = isset($_GET['idCurso'])    ? intval($_GET['idCurso'])    : 0;
$idComision = isset($_GET['idComision']) ? intval($_GET['idComision']) : 0;

if ($idCarrera > 0 && $idCurso > 0 && $idComision > 0) {
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

    while ($fila = mysqli_fetch_assoc($resultado)) {
        $estudiantes[] = $fila;
    }

    echo json_encode($estudiantes);
    exit;
}

// 3) Si no vino nada válido
echo json_encode(["error" => "Parámetros inválidos"]);
exit;
?>
