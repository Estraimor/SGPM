<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include '../../conexion/conexion.php';

// Verificar si la variable de sesión 'id' está configurada
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'No se encontró el legajo del estudiante en la sesión.']);
    exit();
}

$alumno_legajo = $_SESSION['id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['idFecha']) || !isset($data['tanda'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos recibidos.']);
    exit();
}

$idFecha = $data['idFecha'];
$tanda   = $data['tanda'];

// Verificar la conexión a la base de datos
if (!$conexion) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . mysqli_connect_error()]);
    exit();
}

// 1) Obtener la fecha y llamado de la mesa principal
$queryFecha = "
    SELECT t.fecha, t.llamado
    FROM tandas t
    JOIN fechas_mesas_finales fm ON t.idtandas = fm.tandas_idtandas
    WHERE fm.idfechas_mesas_finales = '$idFecha'
";
$resultFecha = mysqli_query($conexion, $queryFecha);
if (!$resultFecha) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener la fecha de la mesa: ' . mysqli_error($conexion)]);
    exit();
}
$rowFecha  = mysqli_fetch_assoc($resultFecha);
$fechaMesa = $rowFecha['fecha'];
$llamado   = $rowFecha['llamado'];

// 2) Verificar si el estudiante ya está inscrito en una mesa en la misma fecha
$queryVerificarFecha = "
    SELECT COUNT(*) AS count
    FROM mesas_finales mf
    JOIN fechas_mesas_finales fm ON mf.fechas_mesas_finales_idfechas_mesas_finales = fm.idfechas_mesas_finales
    JOIN tandas t ON fm.tandas_idtandas = t.idtandas
    WHERE mf.alumno_legajo = '$alumno_legajo'
      AND DATE(t.fecha) = DATE('$fechaMesa')
";
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

// 3) Verificar si hay cupo disponible
$queryCupo = "
    SELECT t.cupo
    FROM tandas t
    JOIN fechas_mesas_finales fm ON t.idtandas = fm.tandas_idtandas
    WHERE fm.idfechas_mesas_finales = '$idFecha'
";
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

// 4) Obtener la materia pedagógica si existe
$queryMateriasPedagogicas = "
    SELECT 
        CASE 
            WHEN mp.materias_idMaterias = fm.materias_idMaterias THEN mp.materias_idMaterias1
            WHEN mp.materias_idMaterias1 = fm.materias_idMaterias THEN mp.materias_idMaterias
            ELSE NULL
        END AS materia_asociada,
        fm.materias_idMaterias AS materia_principal_id
    FROM fechas_mesas_finales fm
    LEFT JOIN mesas_pedagogicas mp 
           ON fm.materias_idMaterias IN (mp.materias_idMaterias, mp.materias_idMaterias1)
    WHERE fm.idfechas_mesas_finales = '$idFecha'
";
$resultMateriasPedagogicas = mysqli_query($conexion, $queryMateriasPedagogicas);
$materiaPrincipalId = null;
$materiaAsociadaId  = null;

if ($resultMateriasPedagogicas && mysqli_num_rows($resultMateriasPedagogicas) > 0) {
    $rowMaterias         = mysqli_fetch_assoc($resultMateriasPedagogicas);
    $materiaPrincipalId  = $rowMaterias['materia_principal_id'];
    $materiaAsociadaId   = $rowMaterias['materia_asociada'];
}

// 📌 Funciones para inscripción y actualización de cupo
function inscribirEnMesa($conexion, $alumno_legajo, $materiaId, $fechaMesaId) {
    $queryInscribir = "
        INSERT INTO mesas_finales (alumno_legajo, materias_idMaterias, fechas_mesas_finales_idfechas_mesas_finales)
        VALUES ('$alumno_legajo', '$materiaId', '$fechaMesaId')
    ";
    return mysqli_query($conexion, $queryInscribir);
}
function actualizarCupo($conexion, $fechaMesaId) {
    $queryActualizarCupo = "
        UPDATE tandas t
        JOIN fechas_mesas_finales fm ON t.idtandas = fm.tandas_idtandas
        SET t.cupo = t.cupo - 1
        WHERE fm.idfechas_mesas_finales = '$fechaMesaId'
    ";
    return mysqli_query($conexion, $queryActualizarCupo);
}

// 5) Inscribir en la materia principal
$inscripcionPrincipal = inscribirEnMesa($conexion, $alumno_legajo, $materiaPrincipalId, $idFecha);
$cupoPrincipal        = actualizarCupo($conexion, $idFecha);

if (!$inscripcionPrincipal || !$cupoPrincipal) {
    echo json_encode(['success' => false, 'message' => 'Error al inscribirse en la mesa principal.']);
    exit();
}

$mensaje = "✅ Inscripción exitosa en la materia principal.";

// 6) Si hay materia pedagógica, buscar una mesa con la misma FECHA (no la misma tanda)
if ($materiaAsociadaId) {
    $queryFechaPedagogica = "
        SELECT fm.idfechas_mesas_finales
        FROM fechas_mesas_finales fm
        JOIN tandas t ON fm.tandas_idtandas = t.idtandas
        WHERE fm.materias_idMaterias = '$materiaAsociadaId'
          AND DATE(t.fecha) = DATE('$fechaMesa')
        LIMIT 1
    ";
    $resultFechaPedagogica = mysqli_query($conexion, $queryFechaPedagogica);

    if ($resultFechaPedagogica && mysqli_num_rows($resultFechaPedagogica) > 0) {
        $rowFechaPedagogica  = mysqli_fetch_assoc($resultFechaPedagogica);
        $idFechaPedagogica   = $rowFechaPedagogica['idfechas_mesas_finales'];

        // Inscribir en la materia pedagógica
        $inscripcionPedagogica = inscribirEnMesa($conexion, $alumno_legajo, $materiaAsociadaId, $idFechaPedagogica);
        $cupoPedagogico        = actualizarCupo($conexion, $idFechaPedagogica);

        if ($inscripcionPedagogica && $cupoPedagogico) {
            $mensaje .= " También inscripto en la materia pedagógica.";
        } else {
            $mensaje .= " ⚠️ Error al inscribirse en la materia pedagógica.";
        }
    } else {
        $mensaje .= " ❌ No se encontró una mesa disponible para la materia pedagógica en la misma fecha.";
    }
}

// 7) Responder con JSON
echo json_encode(['success' => true, 'message' => $mensaje]);
?>
