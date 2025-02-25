<?php
include'../../../conexion/conexion.php';
if (isset($_POST['legajo'])) {
    $legajo = $_POST['legajo'];
    $nueva_password = '0123456789';

    $sql_update = "UPDATE alumno SET pass_alumno = '$nueva_password' WHERE legajo = '$legajo'";
    $resultado = mysqli_query($conexion, $sql_update);

    if ($resultado) {
        echo "<script>
            alert('Contraseña restablecida a 0123456789');
            window.location.href = 'info_alumnoT.php?legajo=$legajo'; 
        </script>";
    } else {
        echo "<script>
            alert('Error al restablecer la contraseña: " . mysqli_error($conexion) . "');
            window.location.href = 'info_alumnoT.php?legajo=$legajo';
        </script>";
    }
} else {
    echo "<script>
        alert('No se proporcionó el legajo del estudiante.');
        window.location.href = 'info_alumnoT.php';
    </script>";
}
?>
