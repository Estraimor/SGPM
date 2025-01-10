<?php
// Incluir la conexión a la base de datos
include '../../conexion/conexion.php';

// Verificar si se recibió el ID de la carrera
if (isset($_POST['idCarrera'])) {
    $idCarrera = $_POST['idCarrera'];

    // Consulta para obtener las materias de la carrera seleccionada
    $query = "SELECT idMaterias, Nombre FROM materias WHERE carreras_idCarrera  = '$idCarrera'";
    $result = mysqli_query($conexion, $query);

    // Verificar si hay resultados
    if (mysqli_num_rows($result) > 0) {
        // Recorrer los resultados y generar las opciones para el select de materias
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['idMaterias']}'>{$row['Nombre']}</option>";
        }
    } else {
        // No hay materias disponibles para la carrera seleccionada
        echo "<option value=''>No hay materias disponibles</option>";
    }
} else {
    // No se recibió el ID de la carrera
    echo "<option value=''>Selecciona una carrera primero</option>";
}
?>
