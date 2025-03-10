<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';

session_start(); 

// Obtener el legajo, la carrera y la comisión del alumno desde la sesión
$alumno_legajo = $_SESSION['id'];
$idCarrera     = $_SESSION['idCarrera']; 
$idComision    = $_SESSION['idComision']; // Nuevo: la comisión del alumno

// Consulta para obtener las mesas finales y sus detalles, filtrando por carrera y comisión
$query = "
    SELECT 
        fm.idfechas_mesas_finales, 
        m.Nombre AS materia, 
        t.fecha, 
        t.llamado, 
        t.tanda, 
        t.cupo,
        ne.nota AS nota_final,
        (SELECT condicion 
         FROM notas 
         WHERE alumno_legajo = '$alumno_legajo' 
           AND materias_idMaterias = m.idMaterias 
           AND condicion IN ('Regular', 'Libre') 
           AND condicion IS NOT NULL 
         ORDER BY fecha DESC
         LIMIT 1) AS condicion_alumno,
        IFNULL(
            (SELECT 1 
             FROM mesas_finales mf
             WHERE mf.alumno_legajo = '$alumno_legajo' 
               AND mf.fechas_mesas_finales_idfechas_mesas_finales = fm.idfechas_mesas_finales
             LIMIT 1), 0) AS inscrito,
        IFNULL(
            (SELECT 1 
             FROM mesas_finales mf
             JOIN fechas_mesas_finales fmf ON mf.fechas_mesas_finales_idfechas_mesas_finales = fmf.idfechas_mesas_finales
             JOIN tandas tn ON fmf.tandas_idtandas = tn.idtandas
             WHERE mf.alumno_legajo = '$alumno_legajo'
               AND mf.materias_idMaterias = fm.materias_idMaterias
               AND tn.llamado = t.llamado
             LIMIT 1), 0) AS inscrito_mismo_llamado
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
        AND m.comisiones_idComisiones = '$idComision'  -- <-- Filtro adicional por la comisión
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
        
        // 3) Verificar si faltan más de 5 minutos (300 segundos)
        //    para la fecha/hora de la mesa
        $faltan_segundos = $fecha_mesa - $hora_actual;
        if ($faltan_segundos <= 300) {
            // Si faltan 5 min o menos, NO agregamos este registro
            continue; 
        }
        
        // (Opcional) Si quieres dejar el campo “disponible” en el JSON, 
        // puedes hacerlo así:
        $row['disponible'] = $faltan_segundos > 300; 
        //  o simplemente $row['disponible'] = true;
        
        // 4) Convertir valores o terminar de setear tus campos como antes
        $row['llamado'] = (int) $row['llamado'];
        $row['tanda']   = (int) $row['tanda'];
        $row['cupo']    = (int) $row['cupo'];
        $row['nota_final'] = $row['nota_final'] !== null ? (float) $row['nota_final'] : null;
        $row['inscrito'] = (int) $row['inscrito'];
        $row['inscrito_mismo_llamado'] = (int) $row['inscrito_mismo_llamado'];
    
        $mesas[] = $row;
    }
}

// Responder en formato JSON
header('Content-Type: application/json');
echo json_encode($mesas);
?>
