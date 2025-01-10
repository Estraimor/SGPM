<?php
include '../../../conexion/conexion.php';  // Asegúrate de que esta ruta es correcta
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener los datos enviados desde el formulario
$legajo_afp = $_POST['legajo_afp'];
$carreras = explode(",", $_POST['carreras']);  // Convertir la cadena en un array

// Preparar la consulta para insertar cada carrera en la tabla no_aprobados
foreach ($carreras as $idCarrera) {
    $idCarrera = intval($idCarrera);  // Asegúrate de que es un número entero
    echo "Intentando insertar idCarrera: $idCarrera con legajo_afp: $legajo_afp<br>";

    // Inserción en la tabla no_aprobados
    $query = "INSERT INTO no_aprobados (alumnos_fp_legajo_afp, carreras_idCarrera) VALUES (?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ii", $legajo_afp, $idCarrera);

    if (!$stmt->execute()) {
        echo "Error al ejecutar la consulta: " . $stmt->error;
    }
}


// Cerrar la conexión
$stmt->close();
$conexion->close();

// Devolver una respuesta
echo "Éxito";
?>
