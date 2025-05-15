<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../../login/login.php');
    exit;
}

$conexion = mysqli_connect("localhost", "u756746073_root", "POLITECNICOmisiones2023.", "u756746073_politecnico", "3306");
if (!$conexion) {
    echo "Error de conexión.";
    exit;
}

// Datos recibidos por GET
$fecha = $_GET['fecha'] ?? '';
$carreraID = $_GET['carreraId'] ?? '';
$cursoID = $_GET['cursoId'] ?? '';
$comisionID = $_GET['comisionId'] ?? '';
$materiaFiltrada = $_GET['materiaId'] ?? '';

// Validación
if (empty($fecha) || empty($carreraID) || empty($cursoID) || empty($comisionID)) {
    echo "<tr><td colspan='5'>Error: parámetros incompletos.</td></tr>";
    exit;
}

try {
    // 1. Obtener materias según carrera, curso y comisión
    $sqlMaterias = "SELECT idMaterias, Nombre FROM materias 
                    WHERE carreras_idCarrera = $carreraID 
                      AND cursos_idCursos = $cursoID 
                      AND comisiones_idComisiones = $comisionID";

    $materiasQuery = mysqli_query($conexion, $sqlMaterias);
    if (!$materiasQuery) throw new Exception("Error cargando materias");

    $contador = 1;
    $huboAsistencia = false;

    // Contadores para resumen
    $totalPresente = 0;
    $totalAusente = 0;
    $totalJustificada = 0;
    $totalGeneral = 0;

    // 2. Por cada materia, buscar asistencia ese día
    while ($materia = mysqli_fetch_assoc($materiasQuery)) {
        $materiaID = $materia['idMaterias'];
        $materiaNombre = $materia['Nombre'];

        // Si se filtró por materia específica
        if (!empty($materiaFiltrada) && $materiaFiltrada != $materiaID) continue;

        $sqlAsistencia = "SELECT a.alumno_legajo, a.fecha, a.asistencia,
                                 al.nombre_alumno, al.apellido_alumno
                          FROM asistencia a
                          LEFT JOIN alumno al ON a.alumno_legajo = al.legajo
                          WHERE a.materias_idMaterias = $materiaID AND a.fecha = '$fecha'";

        $asistenciaQuery = mysqli_query($conexion, $sqlAsistencia);
        if (!$asistenciaQuery) throw new Exception("Error consultando asistencia");

        while ($asistencia = mysqli_fetch_assoc($asistenciaQuery)) {
    $huboAsistencia = true;

    $estado = intval($asistencia['asistencia']);
    if ($estado === 1) $totalPresente++;
    elseif ($estado === 2) $totalAusente++;
    elseif ($estado === 3) $totalJustificada++;

    $estadoTexto = match($estado) {
        1 => 'Presente',
        2 => 'Ausente',
        3 => 'Justificada',
        default => 'Sin datos',
    };

    $totalGeneral++;

    echo "<tr>
            <td>{$contador}</td>
            <td>{$asistencia['apellido_alumno']}</td>
            <td>{$asistencia['nombre_alumno']}</td>
            <td>{$materiaNombre}</td>
            <td>{$estadoTexto}</td>
          </tr>";
    $contador++;
}
    }

    if (!$huboAsistencia) {
        echo "<tr><td colspan='5'>No hay registros de asistencia para esta fecha.</td></tr>";
    } else {
        $porcPresente = $totalGeneral ? round(($totalPresente / $totalGeneral) * 100) : 0;
        $porcAusente = $totalGeneral ? round(($totalAusente / $totalGeneral) * 100) : 0;
        $porcJustificada = $totalGeneral ? round(($totalJustificada / $totalGeneral) * 100) : 0;

        echo "<tr><td colspan='5'><hr></td></tr>";
        echo "<tr><td colspan='5'><strong>Resumen de asistencia:</strong></td></tr>";
        echo "<tr><td colspan='5'>
            
                <div style='background:#d4edda;padding:10px 15px;border-radius:5px;'>
                    Presente: $totalPresente ($porcPresente%)
                </div>
                <div style='background:#f8d7da;padding:10px 15px;border-radius:5px;'>
                    Ausente: $totalAusente ($porcAusente%)
                </div>
                <div style='background:#fff3cd;padding:10px 15px;border-radius:5px;'>
                    Justificada: $totalJustificada ($porcJustificada%)
                </div>
          
        </td>
        </tr>";
    }

} catch (Exception $e) {
    echo "<tr><td colspan='5'>Error: {$e->getMessage()}</td></tr>";
}
?>
