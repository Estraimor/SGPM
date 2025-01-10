<?php
session_start();
include '../conexion/conexion.php';

if (isset($_POST['cambiar'])) {
    if (!empty($_POST['nueva']) && !empty($_POST['confirmar'])) {
        $nueva = $conexion->real_escape_string($_POST['nueva']);
        $confirmar = $conexion->real_escape_string($_POST['confirmar']);

        if ($nueva === $confirmar) {
            $idAlumno = $_SESSION['id'];

            // Actualizar la contraseña en la base de datos
            $stmt = $conexion->prepare("UPDATE profesor SET pass = ? WHERE idProrfesor  = ?");
            $stmt->bind_param("si", $nueva, $idAlumno);

            if ($stmt->execute()) {
                $_SESSION['contraseña'] = $nueva; // Actualizar la contraseña en la sesión
                echo "<script>alert('Se ha guardado la nueva contraseña: $nueva');</script>";
                echo "<script>window.location.href='index.php';</script>";
            } else {
                echo 'Error al cambiar la contraseña. Inténtalo de nuevo.';
            }
        } else {
            echo 'La nueva contraseña y su confirmación no coinciden.';
        }
    } else {
        echo 'Por favor, complete todos los campos.';
    }
}
?>
