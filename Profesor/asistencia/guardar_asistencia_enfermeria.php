<?php
include '../../conexion/conexion.php';

// Configuramos el huso horario a Argentina (Misiones)
date_default_timezone_set('America/Argentina/Buenos_Aires');

if (isset($_POST['Enviar'])) {
    if (!empty($_POST['presentePrimera']) || !empty($_POST['ausentePrimera']) || !empty($_POST['idcarrera']) || !empty($_POST['materiaPrimera']) || !empty($_POST['materiaSegunda'])) {
        $idasignatura = $_POST['idcarrera'];
        $materia1 = $_POST['materiaPrimera'];
        $materia2 = $_POST['materiaSegunda'];
        $materia3 = $_POST['materiaTercera'];
        $materia4 = $_POST['materiaCuarta'];

        // Obtenemos la hora actual en formato de MySQL
        $horaArgentina = date('Y-m-d H:i:s');

        // Primer Horario: Primera Materia y Tercera Materia
        if (!empty($_POST['presentePrimera'])) {
            $presentes = $_POST['presentePrimera'];
            foreach ($presentes as $legajo) {
                $sql = "INSERT INTO asistencia (fecha, inscripcion_asignatura_alumno_legajo, inscripcion_asignatura_carreras_idCarrera, materias_idMaterias, 1_Horario) 
                        VALUES ('$horaArgentina', '$legajo', '$idasignatura', '$materia1', 'Presente')";
                $query = mysqli_query($conexion, $sql);
            }
        }

        if (!empty($_POST['ausentePrimera'])) {
            $ausentes = $_POST['ausentePrimera'];
            foreach ($ausentes as $legajo) {
                $sql = "INSERT INTO asistencia (fecha, inscripcion_asignatura_alumno_legajo, inscripcion_asignatura_carreras_idCarrera, materias_idMaterias, 1_Horario) 
                        VALUES ('$horaArgentina', '$legajo', '$idasignatura', '$materia1', 'Ausente')";
                $query = mysqli_query($conexion, $sql);
            }
        }

        if (!empty($_POST['presenteTercera'])) {
            $presentes = $_POST['presenteTercera'];
            foreach ($presentes as $legajo) {
                $sql = "INSERT INTO asistencia (fecha, inscripcion_asignatura_alumno_legajo, inscripcion_asignatura_carreras_idCarrera, materias_idMaterias, 1_Horario) 
                        VALUES ('$horaArgentina', '$legajo', '$idasignatura', '$materia3', 'Presente')";
                $query = mysqli_query($conexion, $sql);
            }
        }

        if (!empty($_POST['ausenteTercera'])) {
            $ausentes = $_POST['ausenteTercera'];
            foreach ($ausentes as $legajo) {
                $sql = "INSERT INTO asistencia (fecha, inscripcion_asignatura_alumno_legajo, inscripcion_asignatura_carreras_idCarrera, materias_idMaterias, 1_Horario) 
                        VALUES ('$horaArgentina', '$legajo', '$idasignatura', '$materia3', 'Ausente')";
                $query = mysqli_query($conexion, $sql);
            }
        }

        // Segundo Horario: Segunda Materia y Cuarta Materia
        if (!empty($_POST['presenteSegunda'])) {
            $presentes = $_POST['presenteSegunda'];
            foreach ($presentes as $legajo) {
                $sql = "INSERT INTO asistencia (fecha, inscripcion_asignatura_alumno_legajo, inscripcion_asignatura_carreras_idCarrera, materias_idMaterias, 2_Horario) 
                        VALUES ('$horaArgentina', '$legajo', '$idasignatura', '$materia2', 'Presente')";
                $query = mysqli_query($conexion, $sql);
            }
        }

        if (!empty($_POST['ausenteSegunda'])) {
            $ausentes = $_POST['ausenteSegunda'];
            foreach ($ausentes as $legajo) {
                $sql = "INSERT INTO asistencia (fecha, inscripcion_asignatura_alumno_legajo, inscripcion_asignatura_carreras_idCarrera, materias_idMaterias, 2_Horario) 
                        VALUES ('$horaArgentina', '$legajo', '$idasignatura', '$materia2', 'Ausente')";
                $query = mysqli_query($conexion, $sql);
            }
        }

        if (!empty($_POST['presenteCuarta'])) {
            $presentes = $_POST['presenteCuarta'];
            foreach ($presentes as $legajo) {
                $sql = "INSERT INTO asistencia (fecha, inscripcion_asignatura_alumno_legajo, inscripcion_asignatura_carreras_idCarrera, materias_idMaterias, 2_Horario) 
                        VALUES ('$horaArgentina', '$legajo', '$idasignatura', '$materia4', 'Presente')";
                $query = mysqli_query($conexion, $sql);
            }
        }

        if (!empty($_POST['ausenteCuarta'])) {
            $ausentes = $_POST['ausenteCuarta'];
            foreach ($ausentes as $legajo) {
                $sql = "INSERT INTO asistencia (fecha, inscripcion_asignatura_alumno_legajo, inscripcion_asignatura_carreras_idCarrera, materias_idMaterias, 2_Horario) 
                        VALUES ('$horaArgentina', '$legajo', '$idasignatura', '$materia4', 'Ausente')";
                $query = mysqli_query($conexion, $sql);
            }
        }
    }

    header('Location: ../../nuevo_sgpm/index.php');
}
?>
