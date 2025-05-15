<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';
session_start();

$busqueda = mysqli_real_escape_string($conexion, $_POST['busqueda']);
$carrera = intval($_POST['carrera']);

$sql = "
    SELECT a.idAlumno, a.nombre_alumno, a.apellido_alumno, a.dni_alumno, a.legajo,
           co.comision AS comision_letra,
           ia.Comisiones_idComisiones AS comision_id,
           ia.idinscripcion_asignatura
    FROM alumno a
    JOIN inscripcion_asignatura ia ON ia.alumno_legajo = a.legajo AND ia.carreras_idCarrera = $carrera
    JOIN comisiones co ON ia.Comisiones_idComisiones = co.idComisiones
    WHERE a.dni_alumno LIKE '%$busqueda%' 
       OR a.legajo LIKE '%$busqueda%' 
       OR a.nombre_alumno LIKE '%$busqueda%' 
       OR a.apellido_alumno LIKE '%$busqueda%'
   ";

$res = mysqli_query($conexion, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    echo "<p>No se encontraron estudiantes.</p>";
    exit;
}

while ($alumno = mysqli_fetch_assoc($res)) {
    $comision_actual = $alumno['comision_letra'];
    $idComisionActual = $alumno['comision_id'];
    $idInscripcion = $alumno['idinscripcion_asignatura'];
    $legajo = $alumno['legajo'];

    echo "<div style='border:1px solid #ccc; padding:10px; margin-top:10px;'>";
    echo "<strong>{$alumno['apellido_alumno']} {$alumno['nombre_alumno']}</strong><br>";
    echo "DNI: {$alumno['dni_alumno']} | Legajo: {$legajo}<br>";
    echo "Comisión actual: <strong>{$comision_actual}</strong><br>";

    $sqlComisiones = "
        SELECT DISTINCT p.comisiones_idComisiones, c.comision
        FROM preceptores p
        JOIN comisiones c ON p.comisiones_idComisiones = c.idComisiones
        WHERE p.carreras_idCarrera = $carrera";
    
    $comisiones = mysqli_query($conexion, $sqlComisiones);

    echo "<label>Nueva comisión: </label>";
    echo "<select class='comision-nueva' data-id='{$idInscripcion}' data-legajo='{$legajo}'>";
    echo "<option value=''>Seleccionar...</option>";
    while ($com = mysqli_fetch_assoc($comisiones)) {
        $idComision = $com['comisiones_idComisiones'];
        $letraComision = $com['comision'];
        $label = ($idComision == $idComisionActual) ? "(Actual) Comisión {$letraComision}" : "Comisión {$letraComision}";
        echo "<option value='{$idComision}'>{$label}</option>";
    }
    echo "</select> ";

    echo "<button class='btn-cambiar' data-id='{$idInscripcion}'>Cambiar</button>";
    echo "<div class='mensaje' id='msg{$idInscripcion}'></div>";
    echo "</div>";
}
?>




