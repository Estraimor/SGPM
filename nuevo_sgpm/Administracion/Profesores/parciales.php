<?php include'../../../conexion/conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Calificaciones</title>
    <style>
      

       

        .titulo-boton {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0;
        }

       

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #ff6f61;
            color: white;
        }

        .cuatrimestre-header {
            text-align: center;
            background-color: #ff6f61;
            font-weight: bold;
            color: white;
        }

        .botones-tp {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="contenido">
    <div class="titulo-boton">
        <h1>Listado de Calificaciones</h1>
        <div>
            <button id="btnAgregarTP1">Agregar TP Primer Cuatrimestre</button>
            <button id="btnAgregarTP2">Agregar TP Segundo Cuatrimestre</button>
        </div>
    </div>

    <div class="botones-tp">
        <button id="btnEliminarTP1">Eliminar Último TP Primer Cuatrimestre</button>
        <button id="btnEliminarTP2">Eliminar Último TP Segundo Cuatrimestre</button>
    </div>

    <?php
    session_start();
    $idProfesor = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0; // ID del profesor desde la sesión
    $idCarrera = isset($_GET['comision']) ? (int)$_GET['comision'] : 0;
    $idMateria = isset($_GET['materia']) ? (int)$_GET['materia'] : 0;

    // Consulta para obtener los alumnos y sus notas
    $sql1 = "
        SELECT a.legajo, a.apellido_alumno, a.nombre_alumno, n.idnotas, n.numero_evaluacion, n.nota, n.cuatrimestre, n.tipo_evaluacion,n.nota_final,n.condicion
        FROM inscripcion_asignatura ia
        INNER JOIN alumno a ON ia.alumno_legajo = a.legajo
        LEFT JOIN notas n ON a.legajo = n.alumno_legajo AND n.materias_idMaterias = $idMateria
        WHERE ia.carreras_idCarrera = $idCarrera
    ";

    $result = mysqli_query($conexion, $sql1);
    $alumnos = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $legajo = $row['legajo'];
        $cuatrimestre = $row['cuatrimestre'];
        $tipoEvaluacion = $row['tipo_evaluacion'];
        $nota = $row['nota'];
        $idnotas = $row['idnotas'];  // Añadir el idnotas
        $nota_final = $row['nota_final'];
        $condicion = $row['condicion'];

        if (!isset($alumnos[$legajo])) {
            $alumnos[$legajo] = [
                'apellido' => $row['apellido_alumno'],
                'nombre' => $row['nombre_alumno'],
                'primer_cuatri' => ['tps' => [], 'parcial' => '', 'recuperatorio' => '', 'promedio' => 0],
                'segundo_cuatri' => ['tps' => [], 'parcial' => '', 'recuperatorio' => '', 'promedio' => 0],
                 'nota_final' => $row['nota_final'],
                'condicion' => $row['condicion']
            ];
        }

        if ($cuatrimestre == 1) {
            if ($tipoEvaluacion == 2) {
                $alumnos[$legajo]['primer_cuatri']['parcial'] = $nota;
            } elseif ($tipoEvaluacion == 3) {
                $alumnos[$legajo]['primer_cuatri']['recuperatorio'] = $nota;
            } else {
                $alumnos[$legajo]['primer_cuatri']['tps'][] = ['nota' => $nota, 'idnotas' => $idnotas];
            }
        } elseif ($cuatrimestre == 2) {
            if ($tipoEvaluacion == 2) {
                $alumnos[$legajo]['segundo_cuatri']['parcial'] = $nota;
            } elseif ($tipoEvaluacion == 3) {
                $alumnos[$legajo]['segundo_cuatri']['recuperatorio'] = $nota;
            } else {
                $alumnos[$legajo]['segundo_cuatri']['tps'][] = ['nota' => $nota, 'idnotas' => $idnotas];
            }
        }
    }
    
    // Consulta para obtener el porcentaje de asistencia y ausencias ajustadas de cada alumno
$sql_asistencias = "
    SELECT 
        a.inscripcion_asignatura_alumno_legajo AS legajo,
        SUM(CASE WHEN a.1_Horario = 'Presente' OR a.2_Horario = 'Presente' THEN 1 ELSE 0 END) AS asistencias,
        SUM(CASE WHEN a.1_Horario = 'Ausente' OR a.2_Horario = 'Ausente' THEN 1 ELSE 0 END) AS ausencias,
        COUNT(*) AS total_clases,
        (SELECT COUNT(*) 
         FROM alumnos_justificados aj 
         WHERE aj.inscripcion_asignatura_alumno_legajo = a.inscripcion_asignatura_alumno_legajo
           AND (aj.materias_idMaterias = $idMateria OR aj.materias_idMaterias1 = $idMateria)
        ) AS justificaciones
    FROM 
        asistencia a
    WHERE 
        a.inscripcion_asignatura_alumno_legajo IN (SELECT alumno_legajo FROM inscripcion_asignatura WHERE carreras_idCarrera = $idCarrera)
        AND a.materias_idMaterias = $idMateria
    GROUP BY 
        a.inscripcion_asignatura_alumno_legajo";

$query_asistencias = mysqli_query($conexion, $sql_asistencias);

// Arreglo para almacenar el porcentaje de asistencia por alumno
$asistencias_alumnos = [];

while ($row_asistencias = mysqli_fetch_assoc($query_asistencias)) {
    $legajo = $row_asistencias['legajo'];
    $total_clases = $row_asistencias['total_clases'];
    $justificaciones = $row_asistencias['justificaciones'];
    
    // Ajustar ausencias y asistencias en función de las justificaciones
    $ajuste_ausencias = floor($justificaciones / 2); // Cada 2 justificaciones suman 1 ausencia
    $ausencias_ajustadas = $row_asistencias['ausencias'] + $ajuste_ausencias; // Se suman las justificaciones como ausencias
    $asistencias_ajustadas = $row_asistencias['asistencias'] - $ajuste_ausencias; // Se restan del presente

    // Calcular porcentajes ajustados
    $porcentaje_asistencia = ($asistencias_ajustadas / $total_clases) * 100;
    $porcentaje_ausencia = ($ausencias_ajustadas / $total_clases) * 100;

    // Almacenar los datos en el arreglo
    $asistencias_alumnos[$legajo] = [
        'porcentaje_asistencia' => $porcentaje_asistencia,
        'porcentaje_ausencia' => $porcentaje_ausencia,
        'justificaciones' => $justificaciones
    ];
}

    ?>

    <form action="guardar_notas.php" method="POST">
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Apellido</th>
                    <th rowspan="2">Nombre</th>
                    <th colspan="4" class="cuatrimestre-header">Primer Cuatrimestre</th>
                    <th colspan="4" class="cuatrimestre-header">Segundo Cuatrimestre</th>
                    <th colspan="2">Asistencia (%)</th>
                    <th rowspan="2">Nota Final</th> <!-- Nueva columna para nota final -->
                    <th rowspan="2">Condición</th> <!-- Nueva columna para condición -->
                </tr>
                <tr>
                    <th>TPs</th>
                    <th>Parcial</th>
                    <th>Recuperatorio</th>
                    <th>Promedio</th>
                    <th>TPs</th>
                    <th>Parcial</th>
                    <th>Recuperatorio</th>
                    <th>Promedio</th>
                    <th>Presente</th> <!-- Columna Presente -->
                    <th>Ausente</th> <!-- Columna Ausente -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos as $legajo => $alumno): ?>
                <tr>
                    <td><?php echo htmlspecialchars($alumno['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>

                    <!-- TPs Primer Cuatrimestre -->
                    <td id="tp-primer-<?php echo $legajo; ?>">
                        <?php foreach ($alumno['primer_cuatri']['tps'] as $index => $tp): ?>
                            TP<?php echo $index + 1; ?>: 
                            <input type="hidden" name="idnotas_primer_<?php echo $legajo; ?>[]" value="<?php echo htmlspecialchars($tp['idnotas']); ?>">
                            <input type="number" name="tp_primer_<?php echo $legajo; ?>[]" value="<?php echo htmlspecialchars($tp['nota']); ?>" min="0" max="10" class="tp-primer-cuatrimestre" data-legajo="<?php echo $legajo; ?>">
                            <input type="hidden" name="tipo_evaluacion_primer_<?php echo $legajo; ?>[]" value="1"> <!-- 1 para TP -->
                        <?php endforeach; ?>
                    </td>

                    <!-- Parcial Primer Cuatrimestre -->
                    <td>
                        <input type="number" name="parcial_primer_<?php echo $legajo; ?>" value="<?php echo isset($alumno['primer_cuatri']['parcial']) ? htmlspecialchars($alumno['primer_cuatri']['parcial']) : 0; ?>" min="0" max="10">
                        <input type="hidden" name="tipo_evaluacion_parcial_primer_<?php echo $legajo; ?>" value="2"> <!-- 2 para Parcial -->
                    </td>

                    <!-- Recuperatorio Primer Cuatrimestre -->
                    <td>
                        <input type="number" name="recuperatorio_primer_<?php echo $legajo; ?>" value="<?php echo isset($alumno['primer_cuatri']['recuperatorio']) ? htmlspecialchars($alumno['primer_cuatri']['recuperatorio']) : ''; ?>" min="0" max="10">
                        <input type="hidden" name="tipo_evaluacion_recuperatorio_primer_<?php echo $legajo; ?>" value="3"> <!-- 3 para Recuperatorio -->
                    </td>

                    <!-- Promedio Primer Cuatrimestre -->
                    <td>
                        <input type="number" name="promedio_primer_<?php echo $legajo; ?>" value="0" min="0" max="10" readonly class="promedio-primer-cuatrimestre" id="promedio-primer-<?php echo $legajo; ?>">
                    </td>

                    <!-- TPs Segundo Cuatrimestre -->
                    <td id="tp-segundo-<?php echo $legajo; ?>">
                        <?php foreach ($alumno['segundo_cuatri']['tps'] as $index => $tp): ?>
                            TP<?php echo $index + 1; ?>: 
                            <input type="hidden" name="idnotas_segundo_<?php echo $legajo; ?>[]" value="<?php echo htmlspecialchars($tp['idnotas']); ?>">
                            <input type="number" name="tp_segundo_<?php echo $legajo; ?>[]" value="<?php echo htmlspecialchars($tp['nota']); ?>" min="0" max="10" class="tp-segundo-cuatrimestre" data-legajo="<?php echo $legajo; ?>">
                            <input type="hidden" name="tipo_evaluacion_segundo_<?php echo $legajo; ?>[]" value="1"> <!-- 1 para TP -->
                        <?php endforeach; ?>
                    </td>

                    <!-- Parcial Segundo Cuatrimestre -->
                    <td>
                        <input type="number" name="parcial_segundo_<?php echo $legajo; ?>" value="<?php echo htmlspecialchars($alumno['segundo_cuatri']['parcial']); ?>" min="0" max="10">
                        <input type="hidden" name="tipo_evaluacion_parcial_segundo_<?php echo $legajo; ?>" value="2"> <!-- 2 para Parcial -->
                    </td>

                    <!-- Recuperatorio Segundo Cuatrimestre -->
                    <td>
                        <input type="number" name="recuperatorio_segundo_<?php echo $legajo; ?>" value="<?php echo htmlspecialchars($alumno['segundo_cuatri']['recuperatorio']); ?>" min="0" max="10">
                        <input type="hidden" name="tipo_evaluacion_recuperatorio_segundo_<?php echo $legajo; ?>" value="3"> <!-- 3 para Recuperatorio -->
                    </td>

                    <!-- Promedio Segundo Cuatrimestre -->
                    <td>
                        <input type="number" name="promedio_segundo_<?php echo $legajo; ?>" value="0" min="0" max="10" readonly class="promedio-segundo-cuatrimestre" id="promedio-segundo-<?php echo $legajo; ?>">
                    </td>

                    <!-- Columna Asistencia Presente -->
                    <td>
                        <?php 
                        if (isset($asistencias_alumnos[$legajo])) {
                            echo number_format($asistencias_alumnos[$legajo]['porcentaje_asistencia'], 2) . '%';
                        } else {
                            echo 'No disponible';
                        }
                        ?>
                    </td>

                    <!-- Columna Asistencia Ausente -->
                    <td>
                        <?php 
                        if (isset($asistencias_alumnos[$legajo])) {
                            echo number_format($asistencias_alumnos[$legajo]['porcentaje_ausencia'], 2) . '%';
                        } else {
                            echo 'No disponible';
                        }
                        ?>
                    </td>

                    <!-- Nota Final -->
                    <td>
                        <input type="number" name="nota_final_<?php echo $legajo; ?>" 
                               value="<?php echo isset($alumno['nota_final']) ? htmlspecialchars($alumno['nota_final']) : 0; ?>" 
                               min="0" max="10">
                    </td>

                    <!-- Condición -->
                    <td>
                        <select name="condicion_<?php echo $legajo; ?>">
                            <option value="" hidden>Seleccionar Opción</option>
                            <option value="Libre" <?php echo (isset($alumno['condicion']) && $alumno['condicion'] == 'Libre') ? 'selected' : ''; ?>>Libre</option>
                            <option value="Regular" <?php echo (isset($alumno['condicion']) && $alumno['condicion'] == 'Regular') ? 'selected' : ''; ?>>Regular</option>
                            <option value="Promocion" <?php echo (isset($alumno['condicion']) && $alumno['condicion'] == 'Promocion') ? 'selected' : ''; ?>>Promoción</option>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Enviar los IDs de carrera, materia y profesor para procesarlos en PHP -->
        <input type="hidden" name="idCarrera" value="<?php echo $idCarrera; ?>">
        <input type="hidden" name="idMateria" value="<?php echo $idMateria; ?>">
        <input type="hidden" name="idProfesor" value="<?php echo $idProfesor; ?>">
        <a href="pagina_destino.php?idCarrera=<?php echo $idCarrera; ?>&idMateria=<?php echo $idMateria; ?>">Descargar Primer Cuatri</a>
        <a href="pagina_destino.php?idCarrera=<?php echo $idCarrera; ?>&idMateria=<?php echo $idMateria; ?>">Descargar Segundo Cuatri</a>
        <a href="PDF_general_notas.php?idCarrera=<?php echo $idCarrera; ?>&idMateria=<?php echo $idMateria; ?>">Descarga Completa</a>
        <button type="submit">Guardar Calificaciones</button>
    </form>
</div>





</div>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    // Función para agregar TPs al cuatrimestre correcto
    function agregarTP(cuatrimestre, legajo) {
        const tpColumn = document.getElementById(`tp-${cuatrimestre}-${legajo}`);
        const newTP = document.createElement('input');
        newTP.type = 'number';
        newTP.min = 0;
        newTP.max = 10;
        newTP.name = `tp_${cuatrimestre}_${legajo}[]`; // Nombre único por legajo
        newTP.classList.add(`tp-${cuatrimestre}-cuatrimestre`);
        newTP.setAttribute('data-legajo', legajo);
        newTP.addEventListener('input', function () {
            recalcularPromedio(cuatrimestre, legajo);
        });
        tpColumn.appendChild(newTP);
    }

    // Eliminar el último TP agregado
    function eliminarTP(cuatrimestre, legajo) {
        const tpColumn = document.getElementById(`tp-${cuatrimestre}-${legajo}`);
        const tpInputs = tpColumn.querySelectorAll(`input.tp-${cuatrimestre}-cuatrimestre`);
        if (tpInputs.length > 0) {
            tpColumn.removeChild(tpInputs[tpInputs.length - 1]);
            recalcularPromedio(cuatrimestre, legajo);
        }
    }

    // Recalcular el promedio en tiempo real
    function recalcularPromedio(cuatrimestre, legajo) {
        let sumaNotas = 0;
        let totalNotas = 0;

        const tpInputs = document.querySelectorAll(`.tp-${cuatrimestre}-cuatrimestre[data-legajo='${legajo}']`);
        tpInputs.forEach(function (input) {
            const valor = parseFloat(input.value);
            if (!isNaN(valor)) {
                sumaNotas += valor;
                totalNotas++;
            }
        });

        const promedio = totalNotas > 0 ? (sumaNotas / totalNotas).toFixed(2) : 0;
        document.getElementById(`promedio-${cuatrimestre}-${legajo}`).value = promedio;
    }

    // Recalcular los promedios desde las notas de la base de datos al cargar la página
    function inicializarPromedios() {
        document.querySelectorAll('.tp-primer-cuatrimestre, .tp-segundo-cuatrimestre').forEach(function (input) {
            const cuatrimestre = input.classList.contains('tp-primer-cuatrimestre') ? 'primer' : 'segundo';
            const legajo = input.getAttribute('data-legajo');
            recalcularPromedio(cuatrimestre, legajo);
        });
    }

    // Asignar eventos para agregar TP al primer y segundo cuatrimestre
    document.getElementById('btnAgregarTP1').addEventListener('click', function () {
        document.querySelectorAll('[id^=tp-primer-]').forEach(function (col) {
            const legajo = col.getAttribute('id').replace('tp-primer-', '');
            agregarTP('primer', legajo);
        });
    });

    document.getElementById('btnAgregarTP2').addEventListener('click', function () {
        document.querySelectorAll('[id^=tp-segundo-]').forEach(function (col) {
            const legajo = col.getAttribute('id').replace('tp-segundo-', '');
            agregarTP('segundo', legajo);
        });
    });

    // Asignar eventos para eliminar TP del primer y segundo cuatrimestre
    document.getElementById('btnEliminarTP1').addEventListener('click', function () {
        document.querySelectorAll('[id^=tp-primer-]').forEach(function (col) {
            const legajo = col.getAttribute('id').replace('tp-primer-', '');
            eliminarTP('primer', legajo);
        });
    });

    document.getElementById('btnEliminarTP2').addEventListener('click', function () {
        document.querySelectorAll('[id^=tp-segundo-]').forEach(function (col) {
            const legajo = col.getAttribute('id').replace('tp-segundo-', '');
            eliminarTP('segundo', legajo);
        });
    });

    // Inicializar promedios para todas las notas al cargar la página
    inicializarPromedios();

    // Recalcular el promedio en tiempo real para todos los inputs de TP existentes
    document.querySelectorAll('.tp-primer-cuatrimestre, .tp-segundo-cuatrimestre').forEach(function (input) {
        input.addEventListener('input', function () {
            const cuatrimestre = input.classList.contains('tp-primer-cuatrimestre') ? 'primer' : 'segundo';
            const legajo = input.getAttribute('data-legajo');
            recalcularPromedio(cuatrimestre, legajo);
        });
    });
});

</script>





</body>
</html>
