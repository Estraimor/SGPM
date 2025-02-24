<?php
include '../../conexion/conexion.php';
session_start();

if (isset($_POST['idCarrera']) && isset($_POST['idCurso']) && isset($_SESSION['id'])) {
    $idCarrera = $_POST['idCarrera'];
    $idCurso = $_POST['idCurso'];
    $preceptor_id = $_SESSION['id'];
    $rol = $_SESSION['roles']; // Obtener el rol de la sesión

    // Depurar valores recibidos
    error_log("Carrera: $idCarrera, Curso: $idCurso, Preceptor: $preceptor_id, Rol: $rol");

    if ($rol == 1) {
        // Mostrar todas las comisiones si el rol es 1
        $stmt = $conexion->prepare("
            SELECT DISTINCT idComisiones, comision
            FROM comisiones
            WHERE idComisiones IN (
                SELECT comisiones_idComisiones 
                FROM preceptores 
                WHERE carreras_idCarrera = ? 
                AND cursos_idCursos = ?
            )
        ");
        $stmt->bind_param("ii", $idCarrera, $idCurso);
    } else {
        // Solo las comisiones asignadas al preceptor
        $stmt = $conexion->prepare("
            SELECT DISTINCT co.idComisiones, co.comision
            FROM comisiones co
            JOIN preceptores p ON co.idComisiones = p.comisiones_idComisiones
            WHERE p.carreras_idCarrera = ? 
            AND p.cursos_idCursos = ? 
            AND p.profesor_idProrfesor = ? 
        ");
        $stmt->bind_param("iii", $idCarrera, $idCurso, $preceptor_id);
    }

    if (!$stmt) {
        echo "<option value=''>Error en la consulta SQL</option>";
        exit();
    }

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
