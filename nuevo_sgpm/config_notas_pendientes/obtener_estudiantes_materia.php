<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); // Asegurar que la salida sea JSON

include '../../conexion/conexion.php';
session_start();

if (isset($_POST['idMateria']) && isset($_POST['anio']) && isset($_POST['comision']) && isset($_POST['curso']) && isset($_POST['carrera'])) {
    $idMateria = $_POST['idMateria'];
    $anio = $_POST['anio'];
    $idComision = $_POST['comision'];
    $idCurso = $_POST['curso'];
    $idCarrera = $_POST['carrera'];

    // 🔹 Consulta optimizada: solo traer estudiantes que correspondan con los filtros
    $stmt = $conexion->prepare("
        SELECT DISTINCT 
            a.legajo, 
            a.nombre_alumno, 
            a.apellido_alumno,
            -- 🔹 Obtener la última nota y condición si existen
            COALESCE(
                (SELECT nota_final 
                 FROM notas 
                 WHERE alumno_legajo = a.legajo 
                   AND materias_idMaterias = m.idMaterias 
                   AND YEAR(fecha) = 2023
                 ORDER BY fecha DESC 
                 LIMIT 1), 'No disponible') AS nota_final,
            COALESCE(
                (SELECT condicion 
                 FROM notas 
                 WHERE alumno_legajo = a.legajo 
                   AND materias_idMaterias = m.idMaterias 
                   AND YEAR(fecha) = 2023
                 ORDER BY fecha DESC 
                 LIMIT 1), 'No disponible') AS condicion
        FROM alumno a
        JOIN materias m ON m.idMaterias = 408
        WHERE m.carreras_idCarrera = 18
          AND m.cursos_idCursos = 1
          AND m.comisiones_idComisiones = 2
          AND A.estado = 1
        ORDER BY a.apellido_alumno, a.nombre_alumno;
    ");

    if (!$stmt) {
        echo json_encode(['error' => 'Error en la consulta SQL: ' . $conexion->error]);
        exit();
    }

    $stmt->bind_param("iiiiiii", $anio, $anio, $idMateria, $idCarrera, $idCurso, $idComision);
    $stmt->execute();
    $result = $stmt->get_result();

    $estudiantes = [];

    while ($row = $result->fetch_assoc()) {
        $estudiantes[] = [
            'legajo' => $row['legajo'],
            'nombre' => $row['nombre_alumno'],
            'apellido' => $row['apellido_alumno'],
            'nota_final' => $row['nota_final'],
            'condicion' => $row['condicion']
        ];
    }

    if (empty($estudiantes)) {
        error_log("⚠️ La consulta no devolvió resultados.");
    } else {
        error_log("✅ Resultados obtenidos: " . print_r($estudiantes, true));
    }
    
    echo json_encode($estudiantes);
    $stmt->close();
} else {
    echo json_encode(['error' => 'Faltan parámetros en la petición.']);
    error_log("❌ Error: Faltan parámetros en la petición: " . print_r($_POST, true));
}
?>
