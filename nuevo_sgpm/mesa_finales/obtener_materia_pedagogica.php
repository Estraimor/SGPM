<?php
// Incluir la conexión a la base de datos
include '../../conexion/conexion.php';

// Verificar si se recibieron los IDs de carrera, curso y comisión
if (isset($_POST['idCarrera']) && isset($_POST['idCurso']) && isset($_POST['idComision'])) {
    $idCarrera = $_POST['idCarrera'];
    $idCurso = $_POST['idCurso'];
    $idComision = $_POST['idComision'];

    // Consulta para obtener las materias pedagógicas filtradas por carrera, curso y comisión
    $query = "
        SELECT idMaterias, Nombre 
        FROM materias 
        WHERE carreras_idCarrera = '$idCarrera' 
        AND cursos_idCursos = '$idCurso' 
        AND comisiones_idComisiones = '$idComision'
    ";
    
    $result = mysqli_query($conexion, $query);

    // Verificar si hay resultados
    if (mysqli_num_rows($result) > 0) {
        // Recorrer los resultados y generar las opciones para el select de materias pedagógicas
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['idMaterias']}'>{$row['Nombre']}</option>";
        }
    } else {
        // No hay materias pedagógicas disponibles
        echo "<option value=''>No hay materias pedagógicas disponibles</option>";
    }
} else {
    // No se recibieron todos los parámetros necesarios
    echo "<option value=''>Selecciona carrera, curso y comisión primero</option>";
}
?>
