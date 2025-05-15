<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo'])) {
    $tipo = $_POST['tipo'];
    $profesorId = $_SESSION['id'];
    $rolUsuario = $_SESSION['roles'];

    $sql = "";

    // ================== 1. CURSOS ==================
    if ($tipo == 'curso' && isset($_POST['carrera_id'])) {
        $carreraId = $_POST['carrera_id'];

        if ($rolUsuario == 1 || $rolUsuario == 5) {
            // Ver todos los cursos de esa carrera
            $sql = "SELECT DISTINCT cu.idCursos, cu.curso
                    FROM materias m
                    INNER JOIN cursos cu ON m.cursos_idCursos = cu.idCursos
                    WHERE m.carreras_idCarrera = '$carreraId'";
        } else {
            // Solo ver cursos del profe
            $sql = "SELECT DISTINCT cu.idCursos, cu.curso
                    FROM materias m
                    INNER JOIN cursos cu ON m.cursos_idCursos = cu.idCursos
                    WHERE m.carreras_idCarrera = '$carreraId'
                      AND m.profesor_idProrfesor = '$profesorId'";
        }
    }

    // ================== 2. COMISIONES ==================
    else if ($tipo == 'comision' && isset($_POST['carrera_id']) && isset($_POST['curso_id'])) {
        $carreraId = $_POST['carrera_id'];
        $cursoId   = $_POST['curso_id'];

        if ($rolUsuario == 1 || $rolUsuario == 5) {
            $sql = "SELECT DISTINCT co.idComisiones, co.comision
                    FROM materias m
                    INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones
                    WHERE m.carreras_idCarrera = '$carreraId'
                      AND m.cursos_idCursos = '$cursoId'";
        } else {
            $sql = "SELECT DISTINCT co.idComisiones, co.comision
                    FROM materias m
                    INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones
                    WHERE m.carreras_idCarrera = '$carreraId'
                      AND m.cursos_idCursos = '$cursoId'
                      AND m.profesor_idProrfesor = '$profesorId'";
        }
    }

    // ================== 3. MATERIAS ==================
    else if (
        $tipo == 'materia' && 
        isset($_POST['carrera_id']) && 
        isset($_POST['curso_id']) && 
        isset($_POST['comision_id']) &&
        isset($_POST['anio'])
    ) {
        $carreraId  = $_POST['carrera_id'];
        $cursoId    = $_POST['curso_id'];
        $comisionId = $_POST['comision_id'];
        $anio       = $_POST['anio'];

        if ($rolUsuario == 1 || $rolUsuario == 5) {
            // Ver todas las materias
            $sql = "SELECT DISTINCT m.idMaterias, m.Nombre
                    FROM materias m
                    WHERE m.carreras_idCarrera = '$carreraId'
                      AND m.cursos_idCursos = '$cursoId'
                      AND m.comisiones_idComisiones = '$comisionId'";
        } else {
            // Solo ver materias asignadas al profesor
            $sql = "SELECT DISTINCT m.idMaterias, m.Nombre
                    FROM materias m
                    WHERE m.carreras_idCarrera = '$carreraId'
                      AND m.cursos_idCursos = '$cursoId'
                      AND m.comisiones_idComisiones = '$comisionId'
                      AND m.profesor_idProrfesor = '$profesorId'";
        }
    }

    // ================== Si no se cumple ninguna ==================
    else {
        echo json_encode(['error' => 'Parámetros faltantes o tipo inválido']);
        exit;
    }

    // ============ Ejecutar consulta =============
    $result = mysqli_query($conexion, $sql);
    if (!$result) {
        echo json_encode(['error' => mysqli_error($conexion)]);
        exit;
    }

    $datos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $datos[] = $row;
    }
    echo json_encode($datos);
    exit;
} else {
    echo json_encode(['error' => 'Método de petición inválido o parámetro "tipo" no recibido']);
    exit;
}
