<?php
include'../../../../conexion/conexion.php';
// Obtener datos de la solicitud
$legajo = $_POST['legajo'];
$fechaDesde = $_POST['desde'];
$fechaHasta = $_POST['hasta'];

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

$registros_faltas = [];

for ($current_date = $start_date; $current_date <= $end_date; $current_date += (60 * 60 * 24)) {
    $dia_en_ingles = date('l', $current_date); // 'l' devuelve el nombre completo del día en inglés
    $dia_en_espanol = $dias_en_espanol[$dia_en_ingles];
    
    // Convertir la fecha actual en un formato Y-m-d para mostrarlo en la vista
    $fecha_actual = date('Y-m-d', $current_date);
    
    // Consulta SQL para obtener las materias del alumno en este día específico
    $sql = "SELECT m.idMaterias, m.Nombre
            FROM materias m
            INNER JOIN dias_semana_has_materias dsm ON m.idMaterias = dsm.materias_idMaterias
            INNER JOIN dias_semana ds ON dsm.dias_semana_idDias_semana = ds.idDias_semana
            INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = m.carreras_idCarrera
            WHERE ia.alumno_legajo = $legajo
              AND ds.dias = '$dia_en_espanol'";
    
    $result = $conexion->query($sql);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Crear un registro de vista de falta justificada solo para este día y materia específica
            $registros_faltas[] = [
                'fecha' => $fecha_actual,
                'materia' => $row['Nombre'],
                'idMateria' => $row['idMaterias'],
                'dia' => $dia_en_espanol
            ];
        }
    } else {
        die("Error en la consulta: " . $conexion->error);
    }
}

// Devolver los registros para la vista en formato JSON
echo json_encode(['registros' => $registros_faltas]);

$conexion->close();
?>