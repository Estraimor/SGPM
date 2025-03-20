<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';

session_start(); 

// Obtener datos del estudiante desde la sesión
$alumno_legajo = $_SESSION['id'];
$idCarrera     = $_SESSION['idCarrera']; 
$idComision    = $_SESSION['idComision']; 

// Consulta SQL con todas las validaciones
$query = "
    SELECT 
        fm.idfechas_mesas_finales, 
        m.Nombre AS materia, 
        m.idMaterias,
        t.fecha, 
        t.llamado, 
        t.tanda, 
        t.cupo,
        ne.nota AS nota_final,

        -- Obtener la última condición del alumno en la materia (Regular, Libre o Promoción)
        (SELECT condicion 
         FROM notas 
         WHERE alumno_legajo = '$alumno_legajo' 
           AND materias_idMaterias = m.idMaterias 
         ORDER BY fecha DESC
         LIMIT 1) AS condicion_alumno,

        -- Verificar si ya está inscripto en esta mesa final específica
        IFNULL(
            (SELECT 1 
             FROM mesas_finales mf
             WHERE mf.alumno_legajo = '$alumno_legajo' 
               AND mf.fechas_mesas_finales_idfechas_mesas_finales = fm.idfechas_mesas_finales
             LIMIT 1), 0) AS inscrito,

        -- Verificar si la materia ya está aprobada (nota >= 6)
        IFNULL(
            (SELECT 1 
             FROM nota_examen_final ne
             WHERE ne.alumno_legajo = '$alumno_legajo'
               AND ne.materias_idMaterias = fm.materias_idMaterias
               AND ne.nota >= 6
             LIMIT 1), 0) AS materia_aprobada,

        -- Verificar si la materia tiene condición Promocion
        IFNULL(
            (SELECT 1
             FROM notas n
             WHERE n.alumno_legajo = '$alumno_legajo'
               AND n.materias_idMaterias = fm.materias_idMaterias
               AND n.condicion = 'Promocion'
             LIMIT 1), 0) AS promocionada,

        -- Último turno en el que el estudiante rindió esta materia
        IFNULL(
            (SELECT MAX(ne.turno) 
             FROM nota_examen_final ne
             WHERE ne.alumno_legajo = '$alumno_legajo'
               AND ne.materias_idMaterias = fm.materias_idMaterias
             LIMIT 1), 0) AS ultimo_turno,

        -- Bloquear inscripción si llegó al turno 7 y no aprobó
        IFNULL(
            (SELECT 1 
             FROM nota_examen_final ne
             WHERE ne.alumno_legajo = '$alumno_legajo'
               AND ne.materias_idMaterias = fm.materias_idMaterias
               AND ne.turno = 7
               AND ne.nota < 6
             LIMIT 1), 0) AS bloqueo_inscripcion

    FROM 
        fechas_mesas_finales fm
    JOIN 
        materias m ON fm.materias_idMaterias = m.idMaterias
    JOIN 
        tandas t ON fm.tandas_idtandas = t.idtandas
    LEFT JOIN 
        nota_examen_final ne ON ne.alumno_legajo = '$alumno_legajo' 
        AND ne.materias_idMaterias = m.idMaterias
    WHERE 
        m.carreras_idCarrera = '$idCarrera'
        AND m.comisiones_idComisiones = '$idComision';
";

// Ejecutar la consulta y procesar resultados
$result = mysqli_query($conexion, $query);
$mesas = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // 1) Obtener timestamp de la fecha/hora de la mesa
        $fecha_mesa = strtotime($row['fecha']);
        // 2) Timestamp de la hora actual
        $hora_actual = time();
        
        // 3) Verificar si faltan más de 5 minutos (300 segundos) para la fecha/hora de la mesa
        $faltan_segundos = $fecha_mesa - $hora_actual;
        if ($faltan_segundos <= 300) {
            // Si faltan 5 min o menos, NO agregamos este registro
            continue; 
        }

        // Campo disponible basado en el tiempo restante
        $row['disponible'] = $faltan_segundos > 300; 

        // Convertir valores a enteros o flotantes
        $row['nota_final'] = $row['nota_final'] !== null ? (float) $row['nota_final'] : null;
        $row['inscrito'] = (int) $row['inscrito'];
        $row['materia_aprobada'] = (int) $row['materia_aprobada'];
        $row['promocionada'] = (int) $row['promocionada'];
        $row['ultimo_turno'] = (int) $row['ultimo_turno'];
        $row['bloqueo_inscripcion'] = (int) $row['bloqueo_inscripcion'];

        $mesas[] = $row;
    }
}

// Responder con JSON
header('Content-Type: application/json');
echo json_encode($mesas);
