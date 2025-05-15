<?php
include './S_estudiante/layout_estudiante.php';

?>

    <div class="contenido">
<?php
$legajo = $_SESSION["id"]; // Toma el legajo del alumno desde la sesión

try {
    // **Asistencias**
    $html_asistencias = '<br><h2 class="section-title">Asistencias</h2><br>';
    $html_asistencias .= '<table class="styled-table">
                            <tr>
                                <th>Materia</th>
                                <th>Porcentaje Presente</th>
                                <th>Porcentaje Ausente</th>
                                <th>Cantidad de Faltas Justificadas</th>
                            </tr>';

    $sql_asistencias = "SELECT 
                            m.Nombre,
                            SUM(CASE WHEN a.1_Horario = 'Presente' OR a.2_Horario = 'Presente' THEN 1 ELSE 0 END) AS asistencias,
                            SUM(CASE WHEN a.1_Horario = 'Ausente' OR a.2_Horario = 'Ausente' THEN 1 ELSE 0 END) AS ausencias,
                            COUNT(*) AS total_clases,
                            (SELECT COUNT(*) FROM alumnos_justificados aj 
                             WHERE (aj.materias_idMaterias = m.idMaterias OR aj.materias_idMaterias1 = m.idMaterias)
                             AND aj.inscripcion_asignatura_alumno_legajo = '$legajo') AS justificaciones
                        FROM 
                            asistencia a
                        INNER JOIN 
                            materias m ON a.materias_idMaterias = m.idMaterias
                        WHERE 
                            a.inscripcion_asignatura_alumno_legajo = '$legajo'
                        GROUP BY 
                            m.Nombre";

    $query_asistencias = mysqli_query($conexion, $sql_asistencias);

    if (!$query_asistencias) {
        throw new Exception("Error al obtener las asistencias: " . mysqli_error($conexion));
    }

    while ($row_asistencias = mysqli_fetch_assoc($query_asistencias)) {
        $total_clases = $row_asistencias['total_clases'];
        $justificaciones = $row_asistencias['justificaciones'];
        $ajuste_ausencias = floor($justificaciones / 2); 
        $ausencias_ajustadas = $row_asistencias['ausencias'] + $ajuste_ausencias;
        $asistencias_ajustadas = $row_asistencias['asistencias'] - $ajuste_ausencias;
        $porcentaje_asistencia = ($asistencias_ajustadas / $total_clases) * 100;
        $porcentaje_ausencia = ($ausencias_ajustadas / $total_clases) * 100;

        $html_asistencias .= "<tr>
                                <td>{$row_asistencias['Nombre']}</td>
                                <td>" . number_format($porcentaje_asistencia, 0) . "%</td>
                                <td>" . number_format($porcentaje_ausencia, 0) . "%</td>
                                <td>{$row_asistencias['justificaciones']}</td>
                            </tr>";
    }
    $html_asistencias .= '</table>';
    echo $html_asistencias;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
</div>




</body>
</html>

