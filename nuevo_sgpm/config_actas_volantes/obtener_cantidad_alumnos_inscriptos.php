<?php
include '../../conexion/conexion.php';

// Validar los parámetros recibidos
$idCarrera = isset($_GET['idCarrera']) ? intval($_GET['idCarrera']) : 0;
$idMateria = isset($_GET['idMateria']) ? intval($_GET['idMateria']) : 0;
$llamado = isset($_GET['llamado']) ? intval($_GET['llamado']) : 0;
$tanda = isset($_GET['tanda']) ? intval($_GET['tanda']) : 0;
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y'); // Si no se selecciona un año, usar el actual

// Verificar si todos los parámetros necesarios están presentes
if ($idCarrera === 0 || $idMateria === 0 || $llamado === 0 || $tanda === 0) {
    echo "Error: Parámetros incompletos.";
    http_response_code(400);
    exit();
}

try {
    // Consulta para contar los alumnos inscritos con condición 'Regular'
$sqlCount = "
    SELECT COUNT(DISTINCT mf.alumno_legajo) as total 
    FROM mesas_finales mf
    JOIN notas n ON mf.alumno_legajo = n.alumno_legajo AND mf.materias_idMaterias = n.materias_idMaterias
    JOIN materias m ON mf.materias_idMaterias = m.idMaterias
    WHERE mf.materias_idMaterias = ? 
    AND m.carreras_idCarrera = ?
    AND n.condicion = 'Regular'
    AND mf.fechas_mesas_finales_idfechas_mesas_finales IN (
        SELECT idfechas_mesas_finales
        FROM fechas_mesas_finales
        WHERE tandas_idtandas IN (
            SELECT idtandas
            FROM tandas
            WHERE tanda = ? AND llamado = ? AND YEAR(fecha) = ?
        )
    )
";
$stmtCount = $conexion->prepare($sqlCount);
$stmtCount->bind_param('iiiii', $idMateria, $idCarrera, $tanda, $llamado, $anio);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalAlumnos = ($resultCount->fetch_assoc())['total'];

// Consulta para obtener los detalles de los alumnos inscritos con condición 'Regular'
$sqlDetails = "
    SELECT DISTINCT a.nombre_alumno, a.apellido_alumno, a.dni_alumno
    FROM alumno a
    JOIN mesas_finales mf ON a.legajo = mf.alumno_legajo
    JOIN notas n ON mf.alumno_legajo = n.alumno_legajo AND mf.materias_idMaterias = n.materias_idMaterias
    JOIN materias m ON mf.materias_idMaterias = m.idMaterias
    WHERE mf.materias_idMaterias = ?
    AND m.carreras_idCarrera = ?
    AND n.condicion = 'Regular'
    AND mf.fechas_mesas_finales_idfechas_mesas_finales IN (
        SELECT idfechas_mesas_finales
        FROM fechas_mesas_finales
        WHERE tandas_idtandas IN (
            SELECT idtandas
            FROM tandas
            WHERE tanda = ? AND llamado = ? AND YEAR(fecha) = ?
        )
    )
";
$stmtDetails = $conexion->prepare($sqlDetails);
$stmtDetails->bind_param('iiiii', $idMateria, $idCarrera, $tanda, $llamado, $anio);
$stmtDetails->execute();
$resultDetails = $stmtDetails->get_result();

    // Mostrar los resultados
    if ($totalAlumnos > 0) {
        echo "<p>Total de alumnos inscritos: $totalAlumnos</p>";
        $contador = 1;
        while ($row = $resultDetails->fetch_assoc()) {
            echo "<div>{$contador}. {$row['nombre_alumno']} {$row['apellido_alumno']} - DNI: {$row['dni_alumno']}</div>";
            $contador++;
        }
    } else {
        echo '<p>No hay alumnos inscritos en esta materia para la carrera seleccionada.</p>';
    }
} catch (Exception $e) {
    echo "Error en el servidor: " . $e->getMessage();
    http_response_code(500);
}
?>
