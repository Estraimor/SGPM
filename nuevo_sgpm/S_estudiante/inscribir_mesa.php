<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();  // Asegurar que la sesión esté iniciada

include '../../conexion/conexion.php';

// Verificar si la variable de sesión 'id' (legajo del estudiante) está configurada
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'No se encontró el legajo del estudiante en la sesión.']);
    exit();
}

// Obtener el legajo del estudiante desde la sesión
$alumno_legajo = $_SESSION['id'];

// Obtener los datos enviados desde el frontend
$data = json_decode(file_get_contents("php://input"), true);

// Verificar que los datos se hayan recibido correctamente
if (!$data || !isset($data['idFecha']) || !isset($data['tanda'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos recibidos.']);
    exit();
}

$idFecha = $data['idFecha'];
$tanda = $data['tanda'];

// Verificar la conexión a la base de datos
if (!$conexion) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . mysqli_connect_error()]);
    exit();
}

// Obtener la fecha de la mesa seleccionada
$queryFecha = "
    SELECT t.fecha, t.llamado 
    FROM tandas t
    JOIN fechas_mesas_finales fm ON t.idtandas = fm.tandas_idtandas
    WHERE fm.idfechas_mesas_finales = '$idFecha'";

$resultFecha = mysqli_query($conexion, $queryFecha);

if (!$resultFecha) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener la fecha de la mesa: ' . mysqli_error($conexion)]);
    exit();
}

$rowFecha = mysqli_fetch_assoc($resultFecha);
$fechaMesa = $rowFecha['fecha'];
$llamado = $rowFecha['llamado'];

// Verificar si el estudiante ya está inscrito en una mesa en la misma fecha
$queryVerificarFecha = "
    SELECT COUNT(*) AS count 
    FROM mesas_finales mf
    JOIN fechas_mesas_finales fm ON mf.fechas_mesas_finales_idfechas_mesas_finales = fm.idfechas_mesas_finales
    JOIN tandas t ON fm.tandas_idtandas = t.idtandas
    WHERE mf.alumno_legajo = '$alumno_legajo' 
    AND DATE(t.fecha) = DATE('$fechaMesa')";

$resultVerificarFecha = mysqli_query($conexion, $queryVerificarFecha);

if (!$resultVerificarFecha) {
    echo json_encode(['success' => false, 'message' => 'Error en la verificación de inscripción en la misma fecha: ' . mysqli_error($conexion)]);
    exit();
}

$rowVerificarFecha = mysqli_fetch_assoc($resultVerificarFecha);

if ($rowVerificarFecha['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Solo puedes rendir una materia por día.']);
    exit();
}

// Verificar si hay cupo disponible para la mesa seleccionada
$queryCupo = "
    SELECT t.cupo 
    FROM tandas t
    JOIN fechas_mesas_finales fm ON t.idtandas = fm.tandas_idtandas
    WHERE fm.idfechas_mesas_finales = '$idFecha'";

$resultCupo = mysqli_query($conexion, $queryCupo);

if (!$resultCupo) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta de cupo: ' . mysqli_error($conexion)]);
    exit();
}

$rowCupo = mysqli_fetch_assoc($resultCupo);

if ($rowCupo['cupo'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Cupo agotado para esta mesa.']);
    exit();
}

// Obtener el ID de la materia principal y la materia pedagógica asociada desde la tabla mesas_pedagogicas, verificando en ambas direcciones
$queryMateriasPedagogicas = "
    SELECT 
        CASE 
            WHEN mp.materias_idMaterias = fm.materias_idMaterias THEN mp.materias_idMaterias1
            WHEN mp.materias_idMaterias1 = fm.materias_idMaterias THEN mp.materias_idMaterias
            ELSE NULL
        END AS materia_asociada,
        fm.materias_idMaterias AS materia_principal_id
    FROM fechas_mesas_finales fm
    LEFT JOIN mesas_pedagogicas mp ON fm.materias_idMaterias IN (mp.materias_idMaterias, mp.materias_idMaterias1)
    WHERE fm.idfechas_mesas_finales = '$idFecha'";

$resultMateriasPedagogicas = mysqli_query($conexion, $queryMateriasPedagogicas);

$materiaPrincipalId = null;
$materiaAsociadaId = null;

if ($resultMateriasPedagogicas && mysqli_num_rows($resultMateriasPedagogicas) > 0) {
    $rowMaterias = mysqli_fetch_assoc($resultMateriasPedagogicas);
    $materiaPrincipalId = $rowMaterias['materia_principal_id'];
    $materiaAsociadaId = $rowMaterias['materia_asociada'];
}

// Función para inscribir al estudiante en una mesa
function inscribirEnMesa($conexion, $alumno_legajo, $materiaId, $fechaMesaId) {
    $queryInscribir = "
        INSERT INTO mesas_finales (alumno_legajo, materias_idMaterias, fechas_mesas_finales_idfechas_mesas_finales)
        VALUES ('$alumno_legajo', '$materiaId', '$fechaMesaId')";
    return mysqli_query($conexion, $queryInscribir);
}

// Función para actualizar el cupo
function actualizarCupo($conexion, $fechaMesaId) {
    $queryActualizarCupo = "
        UPDATE tandas t
        JOIN fechas_mesas_finales fm ON t.idtandas = fm.tandas_idtandas
        SET t.cupo = t.cupo - 1
        WHERE fm.idfechas_mesas_finales = '$fechaMesaId'";
    return mysqli_query($conexion, $queryActualizarCupo);
}

// Inscripción en la mesa principal (siempre se hace esta inscripción)
$inscripcionPrincipal = inscribirEnMesa($conexion, $alumno_legajo, $materiaPrincipalId, $idFecha);
$cupoPrincipal = actualizarCupo($conexion, $idFecha);

if ($inscripcionPrincipal && $cupoPrincipal) {
    // Verificar si existe una materia pedagógica asociada
    if ($materiaAsociadaId) {
        // Obtener el idFecha correspondiente a la materia asociada con el mismo tanda y llamado
        $queryFechaPedagogica = "
            SELECT idfechas_mesas_finales 
            FROM fechas_mesas_finales fm
            JOIN tandas t ON fm.tandas_idtandas = t.idtandas
            WHERE fm.materias_idMaterias = '$materiaAsociadaId'
            AND t.tanda = '$tanda' 
            AND t.llamado = '$llamado'
            LIMIT 1";

        $resultFechaPedagogica = mysqli_query($conexion, $queryFechaPedagogica);
        
        if ($resultFechaPedagogica && mysqli_num_rows($resultFechaPedagogica) > 0) {
            $rowFechaPedagogica = mysqli_fetch_assoc($resultFechaPedagogica);
            $idFechaPedagogica = $rowFechaPedagogica['idfechas_mesas_finales'];

            // Inscribir en la mesa pedagógica
            $inscripcionPedagogica = inscribirEnMesa($conexion, $alumno_legajo, $materiaAsociadaId, $idFechaPedagogica);
            $cupoPedagogico = actualizarCupo($conexion, $idFechaPedagogica);

            if ($inscripcionPedagogica && $cupoPedagogico) {
                echo json_encode(['success' => true, 'message' => 'Inscripción exitosa en ambas mesas.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Inscripción en la mesa principal exitosa, pero fallo en la pedagógica.']);
            }
        } else {
            echo json_encode(['success' => true, 'message' => 'Inscripción exitosa solo en la mesa principal, sin mesa pedagógica.']);
        }
    } else {
        echo json_encode(['success' => true, 'message' => 'Inscripción exitosa solo en la mesa principal.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al inscribirse en la mesa principal.']);
}
?>
