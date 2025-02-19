<?php
// Incluir la conexión a la base de datos
include '../../conexion/conexion.php';

// Verificar si se recibió el ID del curso
if (isset($_POST['idCurso'])) {
    $idCurso = $_POST['idCurso'];

    // Consulta para obtener las comisiones asociadas al curso desde la tabla materias
    $query = "SELECT DISTINCT co.idComisiones, co.comision
              FROM comisiones co
              INNER JOIN materias m ON co.idComisiones = m.comisiones_idComisiones
              WHERE m.cursos_idCursos = '$idCurso'";

    $result = mysqli_query($conexion, $query);

    // Verificar si hay resultados
    if (mysqli_num_rows($result) > 0) {
        echo '<option value="">Selecciona una comisión</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['idComisiones']}'>{$row['comision']}</option>";
        }
    } else {
        echo "<option value=''>No hay comisiones disponibles</option>";
    }
} else {
    echo "<option value=''>Selecciona un curso primero</option>";
}
?>
zx  |