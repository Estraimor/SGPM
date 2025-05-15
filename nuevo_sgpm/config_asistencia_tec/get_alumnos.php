<?php
include '../../conexion/conexion.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');
$fechaHoy = date('Y-m-d');

$materiaId = isset($_GET['materia']) ? intval($_GET['materia']) : 0;

if ($materiaId <= 0) {
    echo '<tr><td colspan="7">Materia inválida.</td></tr>';
    exit;
}

// Obtener alumnos justificados en otras materias ese día
$sqlJustificados = "
    SELECT inscripcion_asignatura_alumno_legajo AS legajo, Motivo 
    FROM alumnos_justificados 
    WHERE fecha = '$fechaHoy' 
      AND materias_idMaterias != $materiaId
";
$justificadosOtros = mysqli_query($conexion, $sqlJustificados);
$justificadosPrevios = [];

while ($row = mysqli_fetch_assoc($justificadosOtros)) {
    $justificadosPrevios[$row['legajo']] = $row['Motivo'];
}

// Traer alumnos de esta materia
$sql = "SELECT a.legajo, a.nombre_alumno, a.apellido_alumno,
               (SELECT asistencia FROM asistencia 
                WHERE alumno_legajo = a.legajo 
                  AND fecha = '$fechaHoy'
                  AND materias_idMaterias = mm.materias_idMaterias
                LIMIT 1) AS estado_asistencia,
               (SELECT Motivo FROM alumnos_justificados 
                WHERE inscripcion_asignatura_alumno_legajo = a.legajo 
                  AND fecha = '$fechaHoy'
                  AND materias_idMaterias = mm.materias_idMaterias
                LIMIT 1) AS motivo_justificado
        FROM matriculacion_materias mm
        INNER JOIN alumno a ON mm.alumno_legajo = a.legajo
        WHERE mm.materias_idMaterias = $materiaId
          AND YEAR(mm.año_matriculacion) = YEAR(CURDATE()) 
        GROUP BY a.legajo
        ORDER BY a.apellido_alumno ASC";

$query = mysqli_query($conexion, $sql);

if (!$query || mysqli_num_rows($query) === 0) {
    echo '<tr><td colspan="7">No se encontraron alumnos.</td></tr>';
    exit;
}

$contador = 1;
while ($alumno = mysqli_fetch_assoc($query)) {
    $legajo = $alumno['legajo'];

    if (isset($justificadosPrevios[$legajo])) {
        $estado = 3;
        $motivo = $justificadosPrevios[$legajo];
    } else {
        $estado = $alumno['estado_asistencia'];
        $motivo = $alumno['motivo_justificado'];
    }

    $checked1 = ($estado == 1) ? "checked" : "";
    $checked2 = ($estado == 2) ? "checked" : "";
    $checked3 = ($estado == 3) ? "checked" : "";

    $clase = ($estado == 3) ? 'style="background-color:#fff9c4;"' : '';

    echo '<tr id="fila-legajo-' . $legajo . '" ' . $clase . '>';
    echo '<td>' . $contador++ . '</td>';
    echo '<td>' . htmlspecialchars($alumno['apellido_alumno']) . '</td>';
    echo '<td>' . htmlspecialchars($alumno['nombre_alumno']) . '</td>';
    echo '<td>';
    // CAMPO OCULTO para indicar de qué materia viene este alumno
    echo '<input type="hidden" name="materia_origen[' . $legajo . ']" value="' . $materiaId . '">';
    echo '</td>';

    echo '<td><label><input type="radio" name="asistencia[' . $legajo . ']" value="1" ' . $checked1 . '> Presente</label></td>';
    echo '<td><label><input type="radio" name="asistencia[' . $legajo . ']" value="2" ' . $checked2 . '> Ausente</label></td>';

    echo '<td style="position: relative;">';
    echo '<label>';
    echo '<input type="radio" name="asistencia[' . $legajo . ']" value="3" ' . $checked3;

    if (empty($motivo)) {
        echo ' onclick="mostrarModalJustificacion(this, ' . $legajo . ')"';
    }

    echo '> Ausente Justificado</label>';

    if (!empty($motivo)) {
        echo '<input type="hidden" name="motivo[' . $legajo . ']" value="' . htmlspecialchars($motivo) . '">';
    } else {
        echo '<div class="modal-justificacion" id="modal-' . $legajo . '" style="display:none;">
            <p style="margin-bottom: 5px;">¿Justificar?</p>
            <button type="button" onclick="abrirOpciones(' . $legajo . ')">Sí</button>
            <button type="button" onclick="cerrarModal(' . $legajo . ')">No</button>
            <div class="opciones-motivo" id="opciones-' . $legajo . '" style="display:none; margin-top: 5px;">
                <label><input type="radio" name="motivo[' . $legajo . ']" value="Salud" onchange="guardarMotivoYCerrarModal(' . $legajo . ')"> Salud</label><br>
                <label><input type="radio" name="motivo[' . $legajo . ']" value="Razones particulares" onchange="guardarMotivoYCerrarModal(' . $legajo . ')"> Razones particulares</label><br>
                <label><input type="radio" name="motivo[' . $legajo . ']" value="Trabajo" onchange="guardarMotivoYCerrarModal(' . $legajo . ')"> Trabajo</label><br>
                <label><input type="radio" name="motivo[' . $legajo . ']" value="Otro" onchange="guardarMotivoYCerrarModal(' . $legajo . ')"> Otro</label>
            </div>
        </div>';
    }

    echo '</td>';
    echo '</tr>';
}
?>
