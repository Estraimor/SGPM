<?php
include '../../../conexion/conexion.php';

// Comprobación del botón Enviar
if (isset($_POST['Enviar'])) {
    $idProfesor = $_POST['idprofe']; // Obtención del ID del profesor mediante GET
    $apellido = $_POST['apellido'];
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $celular = $_POST['celular'];
    $titulo = $_POST['titulo'];

    // Preparar la consulta para evitar inyecciones SQL
    $sql = "UPDATE profesor SET apellido_profe = ?, nombre_profe = ?, dni_profe = ?, celular = ?, Titulo = ? WHERE idProrfesor = ?";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conexion->error);
    }

    // Vinculación de los parámetros para la sentencia preparada
    $stmt->bind_param("sssisi", $apellido, $nombre, $dni, $celular, $titulo, $idProfesor);

    // Ejecución de la sentencia preparada
    if ($stmt->execute()) {
        // Redirigir a la página de lista de profesores si la actualización es exitosa
        header('Location: lista_profesores.php?success=1');
    } else {
        // Si hay un error en la ejecución, muestra un mensaje de error
        die('Execute error: ' . $stmt->error);
    }

    // Cierre del statement
    $stmt->close();
} else {
    // Redirigir si no se presionó el botón Enviar
    header('Location: index.php');
    exit();
}
?>
