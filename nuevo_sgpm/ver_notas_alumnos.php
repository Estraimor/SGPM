<?php
include'./S_estudiante/layout_estudiante.php';
?>

   <div class="contenido">
    <?php

    $legajo = $_SESSION["id"]; // Toma el legajo del alumno desde la sesión

    try {
        // Consulta para obtener todas las notas con valores y condición de la mesa final y el nombre de la materia
        $sql_notas = "SELECT 
                        n.nota_final,
                        n.condicion,
                        COALESCE(nf.nota, 'N/A') AS nota_examen_final,
                        COALESCE(m.Nombre, 'N/A') AS nombre_materia
                      FROM 
                        notas n
                      LEFT JOIN 
                        nota_examen_final nf ON n.alumno_legajo = nf.alumno_legajo AND n.materias_idMaterias = nf.materias_idMaterias
                      LEFT JOIN 
                        materias m ON n.materias_idMaterias = m.idMaterias
                      WHERE 
                        n.alumno_legajo = '$legajo'
                        AND n.nota_final IS NOT NULL
                        AND n.condicion IS NOT NULL
                      ORDER BY 
                        n.fecha DESC";

        $query_notas = mysqli_query($conexion, $sql_notas);

        if (!$query_notas) {
            throw new Exception("Error al obtener las notas: " . mysqli_error($conexion));
        }

        $html_notas = '<br><h2 class="section-title">Última Nota Registrada</h2><br>';
        $html_notas .= '<table class="styled-table">
                            <tr>
                                <th>Materia</th>
                                <th>Nota Final</th>
                                <th>Condición</th>
                                <th>Nota Examen Final</th>
                            </tr>';

        // Procesar todos los resultados
        $hay_registros = false;
        while ($row_notas = mysqli_fetch_assoc($query_notas)) {
            $hay_registros = true;
            $nombre_materia = $row_notas['nombre_materia'];
            $nota_final = isset($row_notas['nota_final']) ? number_format($row_notas['nota_final'], 2) : 'N/A';
            $condicion = $row_notas['condicion'] ?? 'N/A';
            $nota_examen_final = is_numeric($row_notas['nota_examen_final']) ? number_format($row_notas['nota_examen_final'], 2) : $row_notas['nota_examen_final'];

            $html_notas .= "<tr>
                                <td>$nombre_materia</td>
                                <td>$nota_final</td>
                                <td>$condicion</td>
                                <td>$nota_examen_final</td>
                            </tr>";
        }

        if (!$hay_registros) {
            $html_notas .= '<tr><td colspan="4">No hay registros de notas disponibles.</td></tr>';
        }

        $html_notas .= '</table>';
        echo $html_notas;

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
</div>


<


</body>
</html>

