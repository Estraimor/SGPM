<?php
include '../../conexion/conexion.php';

if (isset($_POST['legajo'])) {
    $legajo = $_POST['legajo'];

        $stmt = $conexion->prepare("SELECT i.carreras_idCarrera, c.nombre_carrera,i.Comisiones_idComisiones, i.Cursos_idCursos,cm.comision
                                FROM inscripcion_asignatura i
                                JOIN carreras c ON i.carreras_idCarrera = c.idCarrera
                                JOIN comisiones cm on cm.idComisiones = i.Comisiones_idComisiones
                                WHERE i.alumno_legajo = ?
                                LIMIT 1");
    $stmt->bind_param("i", $legajo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $response = [
            "carrera" => $row['nombre_carrera'],
            "comisiones" => "<option value='{$row['Comisiones_idComisiones']}'>Comisión {$row['comision']}</option>",
            "cursos" => "<option value='{$row['Cursos_idCursos']}'>Curso {$row['Cursos_idCursos']}</option>"
        ];
        echo json_encode($response);
    } else {
        echo json_encode(["carrera" => "", "comisiones" => "", "cursos" => ""]);
    }

    $stmt->close();
}
?>
