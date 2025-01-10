<?php
include '../../../conexion/conexion.php';  // Asegúrate de que esta ruta es correcta
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener los datos enviados desde el formulario
$legajo_afp = $_POST['legajo_afp'];
$carrerasDesmarcadas = isset($_POST['carrerasDesmarcadas']) ? explode(",", $_POST['carrerasDesmarcadas']) : [];

// Eliminar las carreras desmarcadas de la tabla no_aprobados
foreach ($carrerasDesmarcadas as $idCarrera) {
    $idCarrera = intval($idCarrera);  // Asegúrate de que es un número entero
    echo "Intentando eliminar idCarrera: $idCarrera con legajo_afp: $legajo_afp<br>";

    $query_delete = "DELETE FROM no_aprobados WHERE alumnos_fp_legajo_afp = ? AND carreras_idCarrera = ?";
    $stmt_delete = $conexion->prepare($query_delete);
    $stmt_delete->bind_param("ii", $legajo_afp, $idCarrera);
    $stmt_delete->execute();
    $stmt_delete->close();
}

// Cerrar la conexión
$conexion->close();

// Devolver una respuesta
echo "Éxito";
?>
