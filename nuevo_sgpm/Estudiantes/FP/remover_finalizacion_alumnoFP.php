<?php
include '../../../conexion/conexion.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idalumnos_fp = $_POST['idalumnos_fp_remove'];
    $legajo_afp = $_POST['legajo_afp_remove'];
    $carreras_finalizadas = isset($_POST['carreras_finalizadas']) ? $_POST['carreras_finalizadas'] : [];

    // Obtener todas las carreras finalizadas del alumno en la tabla finalizo_FP
    $sql = "SELECT * FROM finalizo_FP WHERE alumnos_fp_legajo_afp = '$legajo_afp'";
    $query = mysqli_query($conexion, $sql);
    $carreras_actuales = [];
    
    while ($row = mysqli_fetch_assoc($query)) {
        $carreras_actuales[] = $row['carreras_idCarrera'];
    }

    // Encontrar las carreras desmarcadas (que necesitan ser eliminadas)
    $carreras_a_eliminar = array_diff($carreras_actuales, $carreras_finalizadas);

    // Eliminar las carreras desmarcadas de la tabla finalizo_FP
    foreach ($carreras_a_eliminar as $carrera_id) {
        $sql_delete = "DELETE FROM finalizo_FP WHERE alumnos_fp_legajo_afp = '$legajo_afp' AND carreras_idCarrera = '$carrera_id'";
        mysqli_query($conexion, $sql_delete);
    }

   echo "<script>
            alert('La finalización ha sido eliminada correctamente.');
            window.history.back();
          </script>";
    exit();
}
?>
