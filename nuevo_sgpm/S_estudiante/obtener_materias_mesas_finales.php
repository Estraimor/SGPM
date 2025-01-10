<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';

session_start(); // Iniciar la sesión para usar las variables de sesión

// Obtener el legajo del alumno y la carrera desde la sesión
$alumno_legajo = $_SESSION['id'];
$idCarrera = $_SESSION['idCarrera']; // Suponiendo que la carrera ya está en la sesión

// -- Consulta para obtener las mesas finales y sus detalles para la carrera del estudiante
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
    m.carreras_idCarrera = '$idCarrera';
";


$result = mysqli_query($conexion, $query);
$mesas = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Convertir la fecha de la mesa a formato de fecha y hora
        $fecha_mesa = strtotime($row['fecha']);
        $hora_actual = time();
        
        // Calcular si faltan menos de 48 horas para la mesa
        $disponible = ($fecha_mesa - $hora_actual) > 1 * 3600;
        
        // Añadir el campo 'disponible' al resultado
        $row['disponible'] = $disponible;

        // Convertir los valores a enteros para mayor compatibilidad con JavaScript
        $row['llamado'] = (int) $row['llamado'];
        $row['tanda'] = (int) $row['tanda'];
        $row['cupo'] = (int) $row['cupo'];
        $row['nota_final'] = $row['nota_final'] !== null ? (float) $row['nota_final'] : null;
        $row['inscrito'] = (int) $row['inscrito'];
        $row['inscrito_mismo_llamado'] = (int) $row['inscrito_mismo_llamado'];
        
        $mesas[] = $row;
    }
}

// Enviar los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($mesas);
?>
