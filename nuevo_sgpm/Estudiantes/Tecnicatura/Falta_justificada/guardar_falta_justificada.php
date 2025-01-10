<?php
include'../../../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $legajo = $_POST['hiddenAlumno'];
    $carrera_id = $_POST['carrera'];  // El ID de la carrera viene del input hidden
    $fechaDesde = $_POST['fechaDesde'];
    $fechaHasta = $_POST['fechaHasta'];
    $motivo = $_POST['motivo'];

    // Verifica que todos los datos están presentes
    if (!$legajo || !$carrera_id || !$fechaDesde || !$fechaHasta || !$motivo) {
        die("Datos incompletos: legajo, carrera_id, fechaDesde, fechaHasta, y motivo son requeridos.");
    }

    // Convertir las fechas a días de la semana en español
    $start_date = strtotime($fechaDesde);
    $end_date = strtotime($fechaHasta);

    $dias_en_espanol = [
        'Sunday' => 'Domingo', 
        'Monday' => 'Lunes', 
        'Tuesday' => 'Martes', 
        'Wednesday' => 'Miercoles', 
        'Thursday' => 'Jueves', 
        'Friday' => 'Viernes', 
        'Saturday' => 'Sabado'
    ];

    // Array para guardar los registros a insertar
    $registros_faltas = [];

    for ($current_date = $start_date; $current_date <= $end_date; $current_date += (60 * 60 * 24)) {
        $dia_en_ingles = date('l', $current_date); // 'l' devuelve el nombre completo del día en inglés
        $dia_en_espanol = $dias_en_espanol[$dia_en_ingles];
        
        // Convertir la fecha actual en un formato Y-m-d para guardarlo en el registro de falta
        $fecha_actual = date('Y-m-d', $current_date);
        
        // Consulta SQL para obtener las materias del alumno en este día específico
        $sql = "SELECT m.idMaterias
                FROM materias m
                INNER JOIN dias_semana_has_materias dsm ON m.idMaterias = dsm.materias_idMaterias
                INNER JOIN dias_semana ds ON dsm.dias_semana_idDias_semana = ds.idDias_semana
                WHERE ds.dias = '$dia_en_espanol'
                AND m.carreras_idCarrera = $carrera_id";
        
        $result = $conexion->query($sql);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Crear un registro de falta justificada solo para este día y materia específica
                $registros_faltas[] = [
                    'inscripcion_asignatura_carreras_idCarrera' => $carrera_id, // Asignar el mismo carrera_id a cada inserción
                    'inscripcion_asignatura_alumno_legajo' => $legajo,
                    'materias_idMaterias' => $row['idMaterias'],
                    'fecha' => $fecha_actual,
                    'motivo' => $motivo
                ];
            }
        } else {
            die("Error en la consulta: " . $conexion->error);
        }
    }

    // Insertar los registros en la base de datos
    foreach ($registros_faltas as $registro) {
        // Asignar una materia a materias_idMaterias o materias_idMaterias1 dependiendo de cuál esté disponible
        $sql_insert = "INSERT INTO alumnos_justificados 
                        (inscripcion_asignatura_carreras_idCarrera, inscripcion_asignatura_alumno_legajo, materias_idMaterias, materias_idMaterias1, fecha, Motivo) 
                       VALUES 
                        ('{$registro['inscripcion_asignatura_carreras_idCarrera']}', 
                        '{$registro['inscripcion_asignatura_alumno_legajo']}', 
                        '{$registro['materias_idMaterias']}', 
                        NULL, 
                        '{$registro['fecha']}', 
                        '{$registro['motivo']}')";

        if (!$conexion->query($sql_insert)) {
            die("Error en la inserción: " . $conexion->error);
        }
    }
    
    // Al guardar la falta justificada, actualiza el estado del alumno a 2 (inactivo)
        

    // Devolver una respuesta exitosa
     echo "<script>
        alert('Faltas justificadas guardadas con éxito.');
        window.history.back();
    </script>";
}
?>
