<?php
include '../../conexion/conexion.php';
session_start();

$idProfesor = $_SESSION['id'];
$roles = $_SESSION['roles'];

$carrera = intval($_GET['carrera']);
$curso = intval($_GET['curso']);
$comision = intval($_GET['comision']);
$fecha = mysqli_real_escape_string($conexion, $_GET['fecha']);

if (!$carrera || !$curso || !$comision || !$fecha) {
    echo '<tr><td colspan="8" style="color:red;">Faltan parámetros</td></tr>';
    exit;
}

$where = "
    asi.asistencia = 3
    AND m.carreras_idCarrera = $carrera
    AND m.cursos_idCursos = $curso
    AND m.comisiones_idComisiones = $comision
    AND asi.fecha = '$fecha'
";

if ($roles != 1) {
    $where .= " AND p.profesor_idProrfesor = $idProfesor";
}

$sql = "SELECT DISTINCT
            a.legajo, a.apellido_alumno, a.nombre_alumno,
            m.Nombre AS materia, asi.fecha, j.Motivo
        FROM asistencia asi
        INNER JOIN alumno a ON a.legajo = asi.alumno_legajo
        INNER JOIN materias m ON m.idMaterias = asi.materias_idMaterias
        INNER JOIN carreras c ON c.idCarrera = m.carreras_idCarrera
        INNER JOIN cursos cur ON cur.idCursos = m.cursos_idCursos
        INNER JOIN comisiones co ON co.idComisiones = m.comisiones_idComisiones
        INNER JOIN preceptores p ON p.carreras_idCarrera = c.idCarrera 
                                 AND p.cursos_idCursos = cur.idCursos 
                                 AND p.comisiones_idComisiones = co.idComisiones
        LEFT JOIN alumnos_justificados j 
               ON j.inscripcion_asignatura_alumno_legajo = a.legajo 
              AND j.fecha = asi.fecha 
              AND j.materias_idMaterias = asi.materias_idMaterias
        WHERE $where
        ORDER BY asi.fecha DESC";

$result = mysqli_query($conexion, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo '<tr><td colspan="8" style="text-align:center; color:#b71c1c;">No hay faltas justificadas.</td></tr>';
    exit;
}

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['legajo'] . '|' . $row['fecha'] . '|' . $row['materia'];
    $inputId = md5($id);
    $motivo = $row['Motivo'];

    // Detectar si es un motivo personalizado
    $esOtro = !in_array($motivo, ['Salud', 'Razones particulares', 'Trabajo']) && $motivo;

    echo "<tr>
            <td>{$row['legajo']}</td>
            <td>{$row['apellido_alumno']}</td>
            <td>{$row['nombre_alumno']}</td>
            <td>{$row['materia']}</td>
            <td>{$row['fecha']}</td>
            <td>
                <select name='motivo[$id]' onchange=\"toggleOtroInput(this, 'otro-$inputId')\">
                    <option value=''>Seleccionar</option>
                    <option value='Salud' " . ($motivo == 'Salud' ? 'selected' : '') . ">Salud</option>
                    <option value='Razones particulares' " . ($motivo == 'Razones particulares' ? 'selected' : '') . ">Razones particulares</option>
                    <option value='Trabajo' " . ($motivo == 'Trabajo' ? 'selected' : '') . ">Trabajo</option>
                    <option value='Otro' " . ($esOtro ? 'selected' : '') . ">Otro</option>
                </select>
            </td>
            <td>
                <input type='text' name='otro[$id]' id='otro-$inputId' placeholder='Especificar...' 
                    value='" . ($esOtro ? htmlspecialchars($motivo) : '') . "' 
                    style='" . ($esOtro ? '' : 'display:none;') . "'>
            </td>
        </tr>";
}

?>
