<?php
include '../../conexion/conexion.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Mostrar errores MySQLi en desarrollo
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$data = json_decode(file_get_contents('php://input'), true);

$legajo = intval($data['legajo']);
$aprobadas = $data['aprobadas'] ?? [];
$inscripciones = $data['inscripciones'] ?? [];
$fecha = date('Y-m-d');

$guardadoAprobadas = 0;
$guardadoInscripciones = 0;

try {
    // Guardar en aprobado_por_equivalencias si viene algo
    if (!empty($aprobadas)) {
        foreach ($aprobadas as $item) {
            $idMateria = intval($item['materia_id']);
            $observacion = $conexion->real_escape_string($item['porcentaje'] ?? '');

            $sql = "INSERT INTO aprobado_por_equivalencias (alumno_legajo, materias_idMaterias, observacion, fecha)
                    VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("iiss", $legajo, $idMateria, $observacion, $fecha);
            $stmt->execute();
            $stmt->close();
            $guardadoAprobadas++;
        }
    }

    // Guardar en matriculacion_materias si viene algo
    if (!empty($inscripciones)) {
        foreach ($inscripciones as $item) {
            $idMateria = intval($item['materia_id']);
            $anio_matriculacion = $fecha; // formato Y-m-d

            $sql = "INSERT INTO matriculacion_materias (alumno_legajo, materias_idMaterias, año_matriculacion)
                    VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("iis", $legajo, $idMateria, $anio_matriculacion);
            $stmt->execute();
            $stmt->close();
            $guardadoInscripciones++;
        }
    }

    // Mensaje según lo que se cargó
    if ($guardadoAprobadas > 0 && $guardadoInscripciones > 0) {
        echo "Se guardaron $guardadoAprobadas materias aprobadas por equivalencia y $guardadoInscripciones inscripciones.";
    } elseif ($guardadoAprobadas > 0) {
        echo "Se guardaron $guardadoAprobadas materias aprobadas por equivalencia.";
    } elseif ($guardadoInscripciones > 0) {
        echo "Se guardaron $guardadoInscripciones materias a cursar.";
    } else {
        echo "No se recibió información para guardar.";
    }

} catch (Exception $e) {
    echo "Error al guardar: " . $e->getMessage();
}
?>
