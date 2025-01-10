<?php
session_start();
include '../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mesa_id = $_POST['mesa_id'];
    $tanda_id = $_POST['tanda_id'];
    $fecha = $_POST['fecha'];
    $legajo = $_SESSION['id']; // Legajo del estudiante logueado

    $fecha_hoy = date('Y-m-d');

    // Comprobación: No permitir dar de baja el mismo día de la mesa
    if (date('Y-m-d', strtotime($fecha)) === $fecha_hoy) {
    echo "<script>alert('No te puedes dar de baja el mismo día de la mesa.'); window.history.back();</script>";
    exit;
}

    // Iniciar transacción para asegurar integridad
    mysqli_begin_transaction($conexion);

    try {
        // Obtener el ID de la materia principal desde mesas_finales
        $sql_materia_principal = "SELECT materias_idMaterias 
                                  FROM mesas_finales 
                                  WHERE idMesas_finales = ? AND alumno_legajo = ?";
        $stmt_materia_principal = mysqli_prepare($conexion, $sql_materia_principal);
        mysqli_stmt_bind_param($stmt_materia_principal, 'ii', $mesa_id, $legajo);
        mysqli_stmt_execute($stmt_materia_principal);
        $result_materia_principal = mysqli_stmt_get_result($stmt_materia_principal);

        if (!$row_materia_principal = mysqli_fetch_assoc($result_materia_principal)) {
            throw new Exception("No se encontró la materia principal.");
        }
        $materia_principal_id = $row_materia_principal['materias_idMaterias'];

        // Verificar si la materia principal tiene pareja pedagógica
        $sql_pareja = "SELECT materias_idMaterias1 
                       FROM mesas_pedagogicas 
                       WHERE materias_idMaterias = ?";
        $stmt_pareja = mysqli_prepare($conexion, $sql_pareja);
        mysqli_stmt_bind_param($stmt_pareja, 'i', $materia_principal_id);
        mysqli_stmt_execute($stmt_pareja);
        $result_pareja = mysqli_stmt_get_result($stmt_pareja);

        $pareja_id = null;
        $tanda_pareja_id = null;

        if ($row_pareja = mysqli_fetch_assoc($result_pareja)) {
            $pareja_id = $row_pareja['materias_idMaterias1'];

            // Obtener el ID de tanda para la pareja pedagógica
            $sql_tanda_pareja = "SELECT tandas_idtandas 
                                 FROM fechas_mesas_finales 
                                 WHERE materias_idMaterias = ?";
            $stmt_tanda_pareja = mysqli_prepare($conexion, $sql_tanda_pareja);
            mysqli_stmt_bind_param($stmt_tanda_pareja, 'i', $pareja_id);
            mysqli_stmt_execute($stmt_tanda_pareja);
            $result_tanda_pareja = mysqli_stmt_get_result($stmt_tanda_pareja);

            if ($row_tanda_pareja = mysqli_fetch_assoc($result_tanda_pareja)) {
                $tanda_pareja_id = $row_tanda_pareja['tandas_idtandas'];
            }
        }

        // Borrar inscripción del alumno para la materia principal
        $sql_baja_principal = "DELETE FROM mesas_finales 
                               WHERE idMesas_finales = ? AND alumno_legajo = ?";
        $stmt_baja_principal = mysqli_prepare($conexion, $sql_baja_principal);
        mysqli_stmt_bind_param($stmt_baja_principal, 'ii', $mesa_id, $legajo);
        mysqli_stmt_execute($stmt_baja_principal);

        // Actualizar el cupo de la tanda para la materia principal
        $sql_cupo_principal = "UPDATE tandas SET cupo = cupo + 1 WHERE idtandas = ?";
        $stmt_cupo_principal = mysqli_prepare($conexion, $sql_cupo_principal);
        mysqli_stmt_bind_param($stmt_cupo_principal, 'i', $tanda_id);
        mysqli_stmt_execute($stmt_cupo_principal);

        // Si hay pareja pedagógica, borrarla y actualizar su cupo
        if ($pareja_id !== null && $tanda_pareja_id !== null) {
            // Borrar la inscripción del alumno para la materia pedagógica
            $sql_baja_pareja = "DELETE FROM mesas_finales 
                                WHERE materias_idMaterias = ? AND alumno_legajo = ?";
            $stmt_baja_pareja = mysqli_prepare($conexion, $sql_baja_pareja);
            mysqli_stmt_bind_param($stmt_baja_pareja, 'ii', $pareja_id, $legajo);
            mysqli_stmt_execute($stmt_baja_pareja);

            // Actualizar el cupo de la tanda para la materia pedagógica
            $sql_cupo_pareja = "UPDATE tandas SET cupo = cupo + 1 WHERE idtandas = ?";
            $stmt_cupo_pareja = mysqli_prepare($conexion, $sql_cupo_pareja);
            mysqli_stmt_bind_param($stmt_cupo_pareja, 'i', $tanda_pareja_id);
            mysqli_stmt_execute($stmt_cupo_pareja);
        }

        // Confirmar transacción
        mysqli_commit($conexion);

        echo "<script>alert('Se ha dado de baja correctamente.'); window.location.href='index_estudiante.php';</script>";
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($conexion);
        echo "<script>alert('Ocurrió un error al intentar dar de baja.'); window.history.back();</script>";
    }

    // Cerrar la conexión
    mysqli_close($conexion);
}
?>
