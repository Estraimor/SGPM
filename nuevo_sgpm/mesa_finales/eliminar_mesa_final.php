<?php
include '../../conexion/conexion.php';

if (isset($_POST['idMesa'])) {
    $idMesa = $_POST['idMesa'];

    $query = "DELETE FROM fechas_mesas_finales WHERE idfechas_mesas_finales = $idMesa";

    if (mysqli_query($conexion, $query)) {
        echo "Mesa eliminada correctamente";
    } else {
        echo "Error al eliminar la mesa: " . mysqli_error($conexion);
    }
}
?>
