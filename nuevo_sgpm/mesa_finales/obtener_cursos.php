<?php
// Incluir la conexión a la base de datos
include '../../conexion/conexion.php';

// Verificar si se recibió el ID de la carrera
if (isset($_POST['idCarrera'])) {
    $idCarrera = $_POST['idCarrera'];

    // Consulta para obtener los cursos asociados a la carrera desde la tabla materias
    $query = "SELECT DISTINCT c.idCursos, c.nombre 
              FROM cursos c
              INNER JOIN materias m ON c.idCursos = m.cursos_idCursos
              WHERE m.carreras_idCarrera = '$idCarrera'";

    $result = mysqli_query($conexion, $query);

    // Verificar si hay resultados
    if (mysqli_num_rows($result) > 0) {
        echo '<option value="">Selecciona un curso</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['idCursos']}'>{$row['nombre']}</option>";
        }
    } else {
        echo "<option value=''>No hay cursos disponibles</option>";
    }
} else {
    echo "<option value=''>Selecciona una carrera primero</option>";
}
?>
