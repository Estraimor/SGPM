<?php
include '../../conexion/conexion.php';

if (isset($_POST['idCarrera']) && isset($_POST['idCurso'])) {
    $idCarrera = $_POST['idCarrera'];
    $idCurso = $_POST['idCurso'];

    // Depurar valores recibidos
    error_log("Carrera: $idCarrera, Curso: $idCurso");

    // Mostrar todas las comisiones disponibles para la carrera y el curso
    $stmt = $conexion->prepare("
        SELECT DISTINCT c.idComisiones, c.comision
        FROM comisiones c
        INNER JOIN materias m ON m.comisiones_idComisiones = c.idComisiones
        WHERE m.carreras_idCarrera = ? AND m.cursos_idCursos = ?
    ");
    $stmt->bind_param("ii", $idCarrera, $idCurso);

    if (!$stmt) {
        echo "<option value=''>Error en la consulta SQL</option>";
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $options = "";
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='{$row['idComisiones']}'>Comisión {$row['comision']}</option>";
    }

    echo $options;
    $stmt->close();
} else {
    echo "<option value=''>Error al obtener comisiones</option>";
    error_log("Datos POST: " . print_r($_POST, true));
}
