<?php
include '../../conexion/conexion.php';
session_start();

// Verificar que lleguen los datos necesarios
if (!isset($_POST['motivo']) || !is_array($_POST['motivo'])) {
    echo "<script>alert('No se enviaron motivos válidos.'); history.back();</script>";
    exit;
}

$motivos = $_POST['motivo'];
$otros = $_POST['otro'] ?? [];

$errores = 0;

foreach ($motivos as $id => $motivo) {
    // Separar el ID en legajo, fecha y materia
    list($legajo, $fecha, $materiaNombre) = explode('|', $id);

    // Buscar el idMateria por nombre (recomendado tenerlo desde el front, pero si no está, lo buscamos)
    $materiaNombre = mysqli_real_escape_string($conexion, $materiaNombre);
    $consulta = mysqli_query($conexion, "SELECT idMaterias FROM materias WHERE Nombre = '$materiaNombre' LIMIT 1");

    if (!$consulta || mysqli_num_rows($consulta) == 0) {
        $errores++;
        continue;
    }

    $idMateria = mysqli_fetch_assoc($consulta)['idMaterias'];

    // Usar el valor de "Otro" si corresponde
    if ($motivo === 'Otro' && isset($otros[$id]) && !empty(trim($otros[$id]))) {
        $motivo = mysqli_real_escape_string($conexion, trim($otros[$id]));
    }

    // Preparar y ejecutar el update
    $fecha = mysqli_real_escape_string($conexion, $fecha);
    $query = "
        UPDATE alumnos_justificados 
        SET Motivo = '$motivo' 
        WHERE inscripcion_asignatura_alumno_legajo = $legajo 
        AND materias_idMaterias = $idMateria 
        AND fecha = '$fecha'
        LIMIT 1
    ";

    if (!mysqli_query($conexion, $query)) {
        $errores++;
    }
}

if ($errores === 0) {
    echo "<script>alert('Justificaciones guardadas correctamente.'); window.location.href = '../falta_justificada.php';</script>";
} else {
    echo "<script>alert('Hubo $errores errores al guardar.'); window.location.href = '../falta_justificada.php';</script>";
}
?>
