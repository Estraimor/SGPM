<?php
include '../../../../conexion/conexion.php';

// Obtener la fecha actual
$fecha_actual = date('Y-m-d');

// Consulta para obtener los alumnos cuya falta justificada termina hoy en su última fecha
$sql = "SELECT aj.inscripcion_asignatura_alumno_legajo 
        FROM alumnos_justificados aj
        INNER JOIN (
            SELECT inscripcion_asignatura_alumno_legajo, MAX(fecha) AS ultima_fecha
            FROM alumnos_justificados
            GROUP BY inscripcion_asignatura_alumno_legajo
        ) max_fecha ON aj.inscripcion_asignatura_alumno_legajo = max_fecha.inscripcion_asignatura_alumno_legajo
        AND aj.fecha = max_fecha.ultima_fecha
        WHERE DATE(aj.fecha) = '$fecha_actual'";

$result = mysqli_query($conexion, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $legajo = $row['inscripcion_asignatura_alumno_legajo'];
        echo "Procesando alumno con legajo: $legajo <br>"; // Mensaje de depuración

        // Actualizar el estado del alumno a 1 (activo) solo en la última fecha de su falta justificada
        $sql_update = "UPDATE alumno SET estado = 1 WHERE legajo = '$legajo'";
        if (!mysqli_query($conexion, $sql_update)) {
            echo "Error al actualizar el estado del alumno con legajo $legajo: " . mysqli_error($conexion);
        } else {
            echo "Estado del alumno con legajo $legajo actualizado a activo.<br>";
        }
    }
} else {
    echo "Error al obtener los alumnos con faltas justificadas: " . mysqli_error($conexion);
}

mysqli_close($conexion);
?>
