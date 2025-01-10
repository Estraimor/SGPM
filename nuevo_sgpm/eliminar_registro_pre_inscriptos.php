<?php
// Asume que ya tienes la conexión a la base de datos en $conexion
include '../conexion/conexion.php';
// Verifica si el parámetro 'DNI' está presente en la URL
if (isset($_GET['DNI'])) {
    $dni = $_GET['DNI'];

    // Prepara la consulta SQL para eliminar el registro basado en el DNI
    $sql = "DELETE FROM pre_inscripciones WHERE DNI = ?";

    // Prepara la declaración
    if ($stmt = $conexion->prepare($sql)) {
        // Vincula el parámetro DNI a la declaración preparada
        $stmt->bind_param("i", $dni);

        // Ejecuta la declaración
        if ($stmt->execute()) {
            // Mostrar un alert de éxito y redirigir
            echo "<script>
                    alert('El registro ha sido eliminado exitosamente.');
                    window.location.href = 'lista_pre_inscriptos.php';
                  </script>";
        } else {
            echo "Error al ejecutar la consulta: " . $stmt->error;
        }

        // Cierra la declaración
        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conexion->error;
    }
} else {
    echo "No se proporcionó un DNI válido.";
}

// Cierra la conexión
$conexion->close();
?>
