<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $idalumnos_fp = $_POST['idalumnos_fp'];
  $legajo_afp = $_POST['legajo_afp']; // Asegúrate de que este dato se esté recibiendo
  $fecha_finalizacion = $_POST['fecha_finalizacion'];
  $carreras_finalizadas = $_POST['carreras_finalizadas'];

  foreach ($carreras_finalizadas as $carrera_id) {
    $sql = "INSERT INTO finalizo_FP (alumnos_fp_legajo_afp, carreras_idCarrera, fecha_finalizo) 
            VALUES ('$legajo_afp', '$carrera_id', '$fecha_finalizacion')";
    if (!mysqli_query($conexion, $sql)) {
      echo "Error: " . mysqli_error($conexion);
      exit;
    }
  }

  echo "<script>
          alert('Datos guardados exitosamente.');
          window.history.back(); // Vuelve a la página anterior
        </script>";
}
?>
