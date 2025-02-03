<?php
session_start();
include '../../conexion/conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id'])) {
    die("Acceso denegado. Debe iniciar sesión.");
}

// Obtener datos del estudiante desde la sesión
$alumno_legajo = $_SESSION['id'];
$idCarrera = $_SESSION["idCarrera"];
$idCurso = $_SESSION["idCurso"];
$idComision = $_SESSION["idComision"];

// Verificar si se recibió una materia válida
if (!isset($_GET['materia']) || !is_numeric($_GET['materia'])) {
    die("Materia inválida.");
}

$idMateria = intval($_GET['materia']);

// 1. Verificar si la materia pertenece a la carrera, curso y comisión del estudiante
$queryMateria = "
    SELECT idMaterias FROM materias 
    WHERE idMaterias = ? AND carreras_idCarrera = ? AND cursos_idCursos = ? AND comisiones_idComisiones = ?
";

$stmt = $conexion->prepare($queryMateria);
$stmt->bind_param("iiii", $idMateria, $idCarrera, $idCurso, $idComision);
$stmt->execute();
$resultMateria = $stmt->get_result();

if ($resultMateria->num_rows == 0) {
    die("Error: La materia no pertenece a tu carrera, curso o comisión.");
}

$stmt->close();

// 2. Verificar si el estudiante ya está matriculado en la materia
$queryMatriculado = "
    SELECT materias_idMaterias FROM matriculacion_materias 
    WHERE alumno_legajo = ? AND materias_idMaterias = ?
";

$stmt = $conexion->prepare($queryMatriculado);
$stmt->bind_param("ii", $alumno_legajo, $idMateria);
$stmt->execute();
$resultMatriculado = $stmt->get_result();

if ($resultMatriculado->num_rows > 0) {
    die("Ya estás matriculado en esta materia.");
}

$stmt->close();

// 3. Verificar si la materia tiene correlatividades y si el estudiante las cumple
$queryCorrelatividad = "
    SELECT materias_idMaterias1, tipo_correlatividad_idtipo_correlatividad 
    FROM correlatividades 
    WHERE materias_idMaterias = ?
";

$stmt = $conexion->prepare($queryCorrelatividad);
$stmt->bind_param("i", $idMateria);
$stmt->execute();
$resultCorrelatividad = $stmt->get_result();

while ($row = $resultCorrelatividad->fetch_assoc()) {
    $materiaRequerida = $row['materias_idMaterias1'];
    $tipoCorrelatividad = $row['tipo_correlatividad_idtipo_correlatividad'];

    if ($tipoCorrelatividad == 1) { // Regularización requerida
        $queryRegular = "
            SELECT condicion FROM notas 
            WHERE alumno_legajo = ? AND materias_idMaterias = ? 
            ORDER BY fecha DESC LIMIT 1
        ";

        $stmtCheck = $conexion->prepare($queryRegular);
        $stmtCheck->bind_param("ii", $alumno_legajo, $materiaRequerida);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $condicion = $resultCheck->fetch_assoc()['condicion'] ?? '';

        if ($condicion !== 'Regular') {
            die("No puedes inscribirte en esta materia porque no has regularizado la correlativa.");
        }

        $stmtCheck->close();
    }

    if ($tipoCorrelatividad == 2) { // Aprobación requerida
        $queryAprobado = "
            SELECT materias_idMaterias FROM (
                SELECT materias_idMaterias FROM notas_mesas_promocionados WHERE alumno_legajo = ?
                UNION
                SELECT materias_idMaterias FROM nota_examen_final WHERE alumno_legajo = ? AND nota >= 6
            ) AS materias_aprobadas
            WHERE materias_idMaterias = ?
        ";

        $stmtCheck = $conexion->prepare($queryAprobado);
        $stmtCheck->bind_param("iii", $alumno_legajo, $alumno_legajo, $materiaRequerida);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows == 0) {
            die("No puedes inscribirte en esta materia porque no has aprobado la correlativa.");
        }

        $stmtCheck->close();
    }
}

$stmt->close();

// 4. Inscribir al estudiante en la materia
$queryInscripcion = "
    INSERT INTO matriculacion_materias (alumno_legajo, materias_idMaterias, año_matriculacion) 
    VALUES (?, ?, CURDATE())
";

$stmt = $conexion->prepare($queryInscripcion);
$stmt->bind_param("ii", $alumno_legajo, $idMateria);

if ($stmt->execute()) {
    echo "Inscripción exitosa en la materia.";
} else {
    echo "Error al inscribirse en la materia.";
}

$stmt->close();
?>
