<?php
session_start();
include '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que vengan los 3 campos
    if (isset($_POST['carrera_id'], $_POST['curso_id'], $_POST['comision_id'])) {
        $carreraId  = $_POST['carrera_id'];
        $cursoId    = $_POST['curso_id'];
        $comisionId = $_POST['comision_id'];

        // Ajusta nombres de columnas según tu BD:
        // Por ejemplo, si en la tabla "materias" existen:
        //  - carreras_idCarrera
        //  - cursos_idCursos
        //  - comisiones_idComisiones
        //  - idMaterias (PK)
        //  - Nombre (nombre de la materia)
        $sql = "SELECT m.idMaterias, m.Nombre 
                FROM materias m
                WHERE m.carreras_idCarrera     = '$carreraId'
                  AND m.cursos_idCursos        = '$cursoId'
                  AND m.comisiones_idComisiones = '$comisionId'";

        $result = mysqli_query($conexion, $sql);

        $materias = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $materias[] = $row;
            }
        }

        // Devuelve el array JSON
        header('Content-Type: application/json');
        echo json_encode($materias);
    } else {
        echo json_encode(["error" => "Faltan parámetros (carrera_id, curso_id, comision_id)"]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
