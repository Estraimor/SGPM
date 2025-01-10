<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';

function log_error($message) {
    file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

if (isset($_POST['id_mesa'])) {
    $idMesa = $_POST['id_mesa'];
    $fecha = $_POST['fecha'];
    $llamado = $_POST['llamado'];
    $tanda = $_POST['tanda'];
    $cupo = $_POST['cupo'];

    // 1. Consulta de depuración para confirmar que el registro existe en fechas_mesas_finales y obtener tandas_idtandas
    $query_debug = "SELECT tandas_idtandas FROM fechas_mesas_finales WHERE tandas_idtandas = ?";
    $stmt_debug = $conexion->prepare($query_debug);
    $stmt_debug->bind_param("i", $idMesa);
    $stmt_debug->execute();
    $result_debug = $stmt_debug->get_result();

    if ($result_debug->num_rows > 0) {
        $row = $result_debug->fetch_assoc();
        $tandas_id = $row['tandas_idtandas']; // Obtenemos el idtandas asociado
        log_error("Registro encontrado en fechas_mesas_finales con tandas_idtandas: $tandas_id");
    } else {
        echo "<script>alert('No se encontró ningún registro en fechas_mesas_finales para la mesa seleccionada con ID: $idMesa');</script>";
        log_error("No se encontró ningún registro en fechas_mesas_finales para idfechas_mesas_finales: $idMesa");
        exit; // Termina la ejecución aquí para depurar
    }

    // 2. Actualizamos los datos en la tabla tandas usando el idtandas obtenido
    $query_update = "UPDATE tandas 
                     SET fecha = ?, llamado = ?, tanda = ?, cupo = ? 
                     WHERE idtandas = ?";
    $stmt_update = $conexion->prepare($query_update);
    $stmt_update->bind_param("siiii", $fecha, $llamado, $tanda, $cupo, $tandas_id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Mesa actualizada correctamente');</script>";
        log_error("Mesa actualizada correctamente para idtandas: $tandas_id");
    } else {
        $error = "Error al actualizar la mesa: " . $conexion->error;
        echo "<script>alert('$error');</script>";
        log_error($error);
    }
} else {
    echo "<script>alert('ID de mesa no especificado.');</script>";
    log_error("ID de mesa no especificado en la solicitud.");
}
?>
