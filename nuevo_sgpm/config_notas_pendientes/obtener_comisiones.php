<?php
include '../../conexion/conexion.php';
session_start();

if (isset($_POST['idCarrera']) && isset($_POST['idCurso']) && isset($_SESSION['id'])) {
    $idCarrera = $_POST['idCarrera'];
    $idCurso = $_POST['idCurso'];
    $preceptor_id = $_SESSION['id'];

    // Depurar valores recibidos
    error_log("Carrera: $idCarrera, Curso: $idCurso, Preceptor: $preceptor_id");

    $stmt = $conexion->prepare("
        SELECT DISTINCT co.idComisiones, co.comision
        FROM comisiones co
        JOIN preceptores p ON co.idComisiones = p.comisiones_idComisiones
        WHERE p.carreras_idCarrera = ? 
        AND p.cursos_idCursos = ? 
        AND p.profesor_idProrfesor = ? 
    ");

    if (!$stmt) {
        echo "<option value=''>Error en la consulta SQL</option>";
        exit();
    }

    $stmt->bind_param("iii", $idCarrera, $idCurso, $preceptor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='{$row['idComisiones']}'>Comisión {$row['comision']}</option>";
    }

    echo $options;
    $stmt->close();
} else {
    echo "<option value=''>Error al obtener comisiones</option>";
    error_log("Datos POST: " . print_r($_POST, true));
    error_log("Datos SESSION: " . print_r($_SESSION, true));
}
?>
