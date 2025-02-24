<?php
include '../../conexion/conexion.php';
session_start();

error_log("Datos recibidos: " . print_r($_POST, true));

// Verificar que se recibieron todos los parámetros necesarios
if (isset($_POST['carrera']) && isset($_POST['curso']) && isset($_POST['comision']) ) {
    $idCarrera = $_POST['carrera'];
    $idCurso = $_POST['curso'];
    $idComision = $_POST['comision'];
    

    error_log("Carrera: $idCarrera, Curso: $idCurso, Comisión: $idComision, Año: $anio");

    // Preparar la consulta para obtener las materias asociadas
    $stmt = $conexion->prepare("
        SELECT idMaterias, Nombre
        FROM materias 
        WHERE carreras_idCarrera = ? 
        AND cursos_idCursos = ? 
        AND comisiones_idComisiones = ?
    ");

    // Verificar si la consulta se preparó correctamente
    if (!$stmt) {
        echo "<option value=''>Error en la consulta SQL</option>";
        exit();
    }

    // Asignar los parámetros a la consulta preparada
    $stmt->bind_param("iii", $idCarrera, $idCurso, $idComision);
    $stmt->execute();
    $result = $stmt->get_result();


    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='{$row['idMaterias']}'>{$row['Nombre']}</option>";
        }
    } else {
        $options .= "<option value=''>No hay materias disponibles</option>";
    }

    echo $options;

    $stmt->close();
} else {
    echo "<option value=''>Error: Datos incompletos</option>";
    error_log("POST Data: " . print_r($_POST, true));
}
?>
