<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';

$idInscripcion = intval($_POST['idInscripcion']);
$nuevaComision = intval($_POST['nuevaComision']);
$legajo = mysqli_real_escape_string($conexion, $_POST['legajo']);

// 1. Traer datos actuales
$consulta = "
    SELECT a.nombre_alumno, a.apellido_alumno, c.comision AS comision_anterior,
           ia.Comisiones_idComisiones AS id_comision_actual
    FROM inscripcion_asignatura ia
    JOIN alumno a ON a.legajo = ia.alumno_legajo
    JOIN comisiones c ON ia.Comisiones_idComisiones = c.idComisiones
    WHERE ia.idinscripcion_asignatura = $idInscripcion 
      AND ia.alumno_legajo = '$legajo'
    LIMIT 1
";

$datos = mysqli_query($conexion, $consulta);

if (!$datos || mysqli_num_rows($datos) == 0) {
    echo "❌ No se encontró el estudiante o la inscripción es inválida.";
    exit;
}

$info = mysqli_fetch_assoc($datos);
$nombreCompleto = $info['apellido_alumno'] . ", " . $info['nombre_alumno'];
$comisionAnterior = $info['comision_anterior'];
$idComisionActual = $info['id_comision_actual'];

// 2. Validar si ya está en esa comisión
if ($idComisionActual == $nuevaComision) {
    echo "⚠️ No se realizó el cambio: el alumno ya está en la comisión $comisionAnterior.";
    exit;
}

// 3. Obtener letra de la nueva comisión
$comNueva = mysqli_query($conexion, "SELECT comision FROM comisiones WHERE idComisiones = $nuevaComision LIMIT 1");
$letraNueva = ($row = mysqli_fetch_assoc($comNueva)) ? $row['comision'] : 'Desconocida';

// 4. Realizar el cambio
$query = "
    UPDATE inscripcion_asignatura 
    SET Comisiones_idComisiones = $nuevaComision 
    WHERE idinscripcion_asignatura = $idInscripcion 
      AND alumno_legajo = '$legajo'
";

if (mysqli_query($conexion, $query)) {
    if (mysqli_affected_rows($conexion) > 0) {
        echo "✔️ Comisión cambiada correctamente.\n";
        echo "👤 Alumno: $nombreCompleto\n";
        echo "🔁 De comisión $comisionAnterior a $letraNueva.";
    } else {
        echo "⚠️ La consulta se ejecutó pero no se modificaron registros.";
    }
} else {
    echo "❌ Error al ejecutar el cambio: " . mysqli_error($conexion);
}
