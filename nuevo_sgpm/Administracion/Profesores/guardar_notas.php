<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../../conexion/conexion.php'; // Incluir la conexión a la base de datos

// Establecer la zona horaria correcta
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Asegurarse de que se trata de una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtener ID del profesor desde la sesión
    $idProfesor = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;

    // Obtener el ID de la materia y carrera desde el formulario
    $idCarrera = isset($_POST['idCarrera']) ? (int)$_POST['idCarrera'] : 0;
    $idMateria = isset($_POST['idMateria']) ? (int)$_POST['idMateria'] : 0;

    // Verificar que se han pasado los IDs necesarios
    if ($idProfesor > 0 && $idMateria > 0 && $idCarrera > 0) {

        // Procesar los TPs del primer cuatrimestre
        foreach ($_POST as $key => $values) {
            if (preg_match('/tp_primer_(\d+)/', $key, $matches)) {
                $legajo = $matches[1]; // Obtener el legajo del estudiante

                foreach ($values as $index => $nota) {
                    $numero_evaluacion = $index + 1;
                    $tipo_evaluacion = 1; // 1 para TP

                    // Verificar si ya existe un registro en la base de datos para este TP
                    $sqlCheck = "SELECT idnotas FROM notas WHERE alumno_legajo = ? AND tipo_evaluacion = ? AND cuatrimestre = 1 AND materias_idMaterias = ? AND carreras_idCarrera = ? AND numero_evaluacion = ?";
                    $stmtCheck = $conexion->prepare($sqlCheck);
                    $stmtCheck->bind_param('iiiii', $legajo, $tipo_evaluacion, $idMateria, $idCarrera, $numero_evaluacion);
                    $stmtCheck->execute();
                    $stmtCheck->store_result();
                    
                    if ($stmtCheck->num_rows > 0) {
                        // Si ya existe, hacemos un UPDATE
                        $stmtCheck->bind_result($idnotas);
                        $stmtCheck->fetch();
                        $sql = "UPDATE notas SET nota = ?, fecha = NOW(), profesor_idProrfesor = ? WHERE idnotas = ?";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param('dii', $nota, $idProfesor, $idnotas);
                    } else {
                        // Si no existe, hacemos un INSERT
                        $sql = "INSERT INTO notas (alumno_legajo, tipo_evaluacion, numero_evaluacion, nota, cuatrimestre, materias_idMaterias, carreras_idCarrera, profesor_idProrfesor, fecha) 
                                VALUES (?, ?, ?, ?, 1, ?, ?, ?, NOW())";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param('iiidiii', $legajo, $tipo_evaluacion, $numero_evaluacion, $nota, $idMateria, $idCarrera, $idProfesor);
                    }
                    $stmt->execute();
                    $stmt->close();
                    $stmtCheck->close();
                }
            }
        }

        // Procesar los TPs del segundo cuatrimestre
        foreach ($_POST as $key => $values) {
            if (preg_match('/tp_segundo_(\d+)/', $key, $matches)) {
                $legajo = $matches[1]; // Obtener el legajo del estudiante

                foreach ($values as $index => $nota) {
                    $numero_evaluacion = $index + 1;
                    $tipo_evaluacion = 1; // 1 para TP

                    // Verificar si ya existe un registro en la base de datos para este TP
                    $sqlCheck = "SELECT idnotas FROM notas WHERE alumno_legajo = ? AND tipo_evaluacion = ? AND cuatrimestre = 2 AND materias_idMaterias = ? AND carreras_idCarrera = ? AND numero_evaluacion = ?";
                    $stmtCheck = $conexion->prepare($sqlCheck);
                    $stmtCheck->bind_param('iiiii', $legajo, $tipo_evaluacion, $idMateria, $idCarrera, $numero_evaluacion);
                    $stmtCheck->execute();
                    $stmtCheck->store_result();
                    
                    if ($stmtCheck->num_rows > 0) {
                        // Si ya existe, hacemos un UPDATE
                        $stmtCheck->bind_result($idnotas);
                        $stmtCheck->fetch();
                        $sql = "UPDATE notas SET nota = ?, fecha = NOW(), profesor_idProrfesor = ? WHERE idnotas = ?";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param('dii', $nota, $idProfesor, $idnotas);
                    } else {
                        // Si no existe, hacemos un INSERT
                        $sql = "INSERT INTO notas (alumno_legajo, tipo_evaluacion, numero_evaluacion, nota, cuatrimestre, materias_idMaterias, carreras_idCarrera, profesor_idProrfesor, fecha) 
                                VALUES (?, ?, ?, ?, 2, ?, ?, ?, NOW())";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param('iiidiii', $legajo, $tipo_evaluacion, $numero_evaluacion, $nota, $idMateria, $idCarrera, $idProfesor);
                    }
                    $stmt->execute();
                    $stmt->close();
                    $stmtCheck->close();
                }
            }
        }

        // Procesar los parciales y recuperatorios
        foreach (['primer', 'segundo'] as $cuatrimestre) {
            $cuatrimestreId = ($cuatrimestre == 'primer') ? 1 : 2;

            // Procesar parcial
            foreach ($_POST as $key => $nota) {
                if (preg_match("/^parcial_{$cuatrimestre}_(\d+)$/", $key, $matches)) {
                    $legajo = $matches[1];
                    $tipoEvaluacion = 2; // 2 para Parcial

                    $sqlCheck = "SELECT idnotas FROM notas WHERE alumno_legajo = ? AND tipo_evaluacion = ? AND cuatrimestre = ? AND materias_idMaterias = ? AND carreras_idCarrera = ?";
                    $stmtCheck = $conexion->prepare($sqlCheck);
                    $stmtCheck->bind_param('iiiii', $legajo, $tipoEvaluacion, $cuatrimestreId, $idMateria, $idCarrera);
                    $stmtCheck->execute();
                    $stmtCheck->store_result();
                    
                    if ($stmtCheck->num_rows > 0) {
                        $stmtCheck->bind_result($idnotas);
                        $stmtCheck->fetch();
                        $sql = "UPDATE notas SET nota = ?, fecha = NOW(), profesor_idProrfesor = ? WHERE idnotas = ?";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param('dii', $nota, $idProfesor, $idnotas);
                    } else {
                        $sql = "INSERT INTO notas (alumno_legajo, tipo_evaluacion, numero_evaluacion, nota, cuatrimestre, materias_idMaterias, carreras_idCarrera, profesor_idProrfesor, fecha) 
                                VALUES (?, ?, 1, ?, ?, ?, ?, ?, NOW())";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param('iidiiii', $legajo, $tipoEvaluacion, $nota, $cuatrimestreId, $idMateria, $idCarrera, $idProfesor);
                    }
                    $stmt->execute();
                    $stmt->close();
                    $stmtCheck->close();
                }
            }

            // Procesar recuperatorio
            foreach ($_POST as $key => $nota) {
                if (preg_match("/^recuperatorio_{$cuatrimestre}_(\d+)$/", $key, $matches)) {
                    $legajo = $matches[1];
                    $tipo_evaluacion = 3; // 3 para Recuperatorio

                    $sqlCheck = "SELECT idnotas FROM notas WHERE alumno_legajo = ? AND tipo_evaluacion = ? AND cuatrimestre = ? AND materias_idMaterias = ? AND carreras_idCarrera = ?";
                    $stmtCheck = $conexion->prepare($sqlCheck);
                    $stmtCheck->bind_param('iiiii', $legajo, $tipo_evaluacion, $cuatrimestreId, $idMateria, $idCarrera);
                    $stmtCheck->execute();
                    $stmtCheck->store_result();
                    
                    if ($stmtCheck->num_rows > 0) {
                        $stmtCheck->bind_result($idnotas);
                        $stmtCheck->fetch();
                        $sql = "UPDATE notas SET nota = ?, fecha = NOW(), profesor_idProrfesor = ? WHERE idnotas = ?";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param('dii', $nota, $idProfesor, $idnotas);
                    } else {
                        $sql = "INSERT INTO notas (alumno_legajo, tipo_evaluacion, numero_evaluacion, nota, cuatrimestre, materias_idMaterias, carreras_idCarrera, profesor_idProrfesor, fecha) 
                                VALUES (?, ?, 1, ?, ?, ?, ?, ?, NOW())";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param('iidiiii', $legajo, $tipo_evaluacion, $nota, $cuatrimestreId, $idMateria, $idCarrera, $idProfesor);
                    }
                    $stmt->execute();
                    $stmt->close();
                    $stmtCheck->close();
                }
            }
        }

        // Procesar la nota final y condición
       foreach ($_POST as $key => $nota_final) {
    if (preg_match('/nota_final_(\d+)/', $key, $matches)) {
        $legajo = $matches[1];
        $condicion = $_POST["condicion_{$legajo}"];

        $sqlCheck = "SELECT idnotas FROM notas WHERE alumno_legajo = ? AND materias_idMaterias = ? AND carreras_idCarrera = ?";
        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->bind_param('iii', $legajo, $idMateria, $idCarrera);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->bind_result($idnotas);
            $stmtCheck->fetch();
            $sql = "UPDATE notas SET nota_final = ?, condicion = ?, fecha = NOW(), profesor_idProrfesor = ? WHERE idnotas = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('dsii', $nota_final, $condicion, $idProfesor, $idnotas);
        } else {
            $sql = "INSERT INTO notas (alumno_legajo, nota_final, condicion, materias_idMaterias, carreras_idCarrera, profesor_idProrfesor, fecha) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('idsiii', $legajo, $nota_final, $condicion, $idMateria, $idCarrera, $idProfesor);
        }

        // Ejecución de la consulta
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }

        // Cierre de la consulta
        $stmt->close();
        $stmtCheck->close();
    }
}


        echo json_encode(['status' => 'success', 'message' => 'Notas guardadas correctamente']);
         header('Location: pre_parciales.php');

        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos o incompletos']);
    }

    $conexion->close(); // Cerrar la conexión a la base de datos
}
?>