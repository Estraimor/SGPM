<?php
include '../../../conexion/conexion.php';
session_start();

// Recibir los datos del formulario
$idCarrera = isset($_POST['idcarrera']) ? intval($_POST['idcarrera']) : 0;
$presentePrimera = isset($_POST['presentePrimera']) ? $_POST['presentePrimera'] : [];
$ausentePrimera = isset($_POST['ausentePrimera']) ? $_POST['ausentePrimera'] : [];
$materiaId = isset($_POST['materiaPrimera']) ? intval($_POST['materiaPrimera']) : 0;
$fecha = date('Y-m-d');

// Validar el ID de carrera y materia
if ($idCarrera <= 0 || $materiaId <= 0) {
    die('Error: ID de carrera o materia inválido.');
}

// Iniciar la transacción
mysqli_begin_transaction($conexion);

try {
    // Guardar los presentes
    foreach ($presentePrimera as $legajo) {
        $legajo = intval($legajo);
        $queryPresente = "INSERT INTO asistencia_FP (alumnos_fp_legajo_afp, carreras_idCarrera, materias_idMaterias, 1_horario, fecha_FP) 
                          VALUES ('$legajo', '$idCarrera', '$materiaId', 'Presente', '$fecha')";
        mysqli_query($conexion, $queryPresente);
    }

    // Guardar los ausentes
    foreach ($ausentePrimera as $legajo) {
        $legajo = intval($legajo);
        $queryAusente = "INSERT INTO asistencia_FP (alumnos_fp_legajo_afp, carreras_idCarrera, materias_idMaterias, 1_horario, fecha_FP) 
                         VALUES ('$legajo', '$idCarrera', '$materiaId', 'Ausente', '$fecha')";
        mysqli_query($conexion, $queryAusente);
    }

    // Si no hay errores, confirmar la transacción
    mysqli_commit($conexion);
    echo "<script>alert('Asistencia guardada correctamente.'); window.location.href='ver_FPS.php';</script>";
} catch (Exception $e) {
    // En caso de error, revertir la transacción
    mysqli_rollback($conexion);
    echo "<script>alert('Error al guardar la asistencia. Intente nuevamente.'); window.location.href='ver_FPS.php';</script>";
}
?>
