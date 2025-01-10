<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye tu archivo de conexión
include '../../conexion/conexion.php';

// Verifica si se envió la fecha
if (isset($_POST['fecha'])) {
    $fecha = $_POST['fecha'];

    // Consulta SQL original
    $sql = "SELECT 
        t.tanda,
        t.llamado,
        m.Nombre AS materia,
        c.nombre_carrera AS carrera,
        COUNT(DISTINCT CASE WHEN n.condicion = 'Regular' THEN mf.alumno_legajo END) AS cantidad_regular,
        COUNT(DISTINCT CASE WHEN n.condicion = 'Libre' THEN mf.alumno_legajo END) AS cantidad_libre
    FROM mesas_finales mf
    JOIN notas n ON mf.alumno_legajo = n.alumno_legajo 
        AND mf.materias_idMaterias = n.materias_idMaterias
    JOIN materias m ON mf.materias_idMaterias = m.idMaterias
    JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
    JOIN fechas_mesas_finales fm ON mf.fechas_mesas_finales_idfechas_mesas_finales = fm.idfechas_mesas_finales
    JOIN tandas t ON fm.tandas_idtandas = t.idtandas
    WHERE DATE(t.fecha) = ?
    GROUP BY 
        t.tanda, 
        t.llamado, 
        m.Nombre, 
        c.nombre_carrera
    ORDER BY 
        t.tanda, 
        t.llamado, 
        c.nombre_carrera, 
        m.Nombre;";

    // Prepara y ejecuta la consulta
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Error al preparar la consulta: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();

    // Arma los resultados separados por condición
    $libres = [];
    $regulares = [];

    while ($row = $result->fetch_assoc()) {
        if ($row['cantidad_libre'] > 0) {
            $libres[] = [
                'materia' => $row['materia'],
                'carrera' => $row['carrera'],
                'tanda' => $row['tanda'],
                'llamado' => $row['llamado'],
                'cantidad' => $row['cantidad_libre']
            ];
        }
        if ($row['cantidad_regular'] > 0) {
            $regulares[] = [
                'materia' => $row['materia'],
                'carrera' => $row['carrera'],
                'tanda' => $row['tanda'],
                'llamado' => $row['llamado'],
                'cantidad' => $row['cantidad_regular']
            ];
        }
    }

    // Retorna los resultados en formato JSON
    echo json_encode(['libres' => $libres, 'regulares' => $regulares]);
} else {
    echo json_encode(['error' => 'Fecha no proporcionada']);
}
?>
