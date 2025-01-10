 <?php
//  ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
include '../conexion/conexion.php';

// Habilitar el reporte de errores para MySQLi
$conexion->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

// Configurar la zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Variables críticas
$materiaId = $_POST['materia'] ?? null;
$turno = $_POST['turno'] ?? null;
$año = $_POST['año'] ?? null;
$profesorId = $_SESSION['id'] ?? null;
$fecha = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $notasPrimerLlamado = $_POST['nota_final_1'] ?? [];
    $ausentePrimerLlamado = $_POST['ausente_1'] ?? [];
    $notasSegundoLlamado = $_POST['nota_final_2'] ?? [];
    $ausenteSegundoLlamado = $_POST['ausente_2'] ?? [];
    $tomosPrimerLlamado = $_POST['tomo_1'] ?? [];
    $foliosPrimerLlamado = $_POST['folio_1'] ?? [];
    $tomosSegundoLlamado = $_POST['tomo_2'] ?? [];
    $foliosSegundoLlamado = $_POST['folio_2'] ?? [];
    $alumnoLegajo = $_POST['alumno_legajo'] ?? [];

    // Validación inicial
    if (is_null($materiaId) || is_null($profesorId) || is_null($fecha)) {
        die("<script>alert('Faltan datos obligatorios.'); window.history.back();</script>");
    }

    try {
        foreach ($alumnoLegajo as $index => $legajo) {
    // Procesar Primer Llamado
    $primerNota = isset($notasPrimerLlamado[$index]) ? $notasPrimerLlamado[$index] : null;
    $primerAusente = isset($ausentePrimerLlamado[$index]) ? $ausentePrimerLlamado[$index] : null;
    $tomoPrimer = $tomosPrimerLlamado[$index] ?? null;
    $folioPrimer = $foliosPrimerLlamado[$index] ?? null;
    $notaFinalPrimer = $primerAusente ? 0 : $primerNota;

  if (!empty($primerNota) || !empty($primerAusente) || $primerNota == 0.00) {
    // Verificar si existe un registro previo
    $checkQuery = "SELECT COUNT(*) AS count FROM nota_examen_final WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 1";
    $stmt = $conexion->prepare($checkQuery);
    $stmt->bind_param("ii", $legajo, $materiaId);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->fetch_assoc()['count'] > 0;

   if ($exists) {
    if ($primerNota == 0.00) {
        // Solo actualizar tomo y folio si la nota es 0.00
        $updateQuery = "UPDATE nota_examen_final 
                        SET tomo = ?, folio = ?, fecha = ? 
                        WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 1 AND turno = ?";
        $stmt = $conexion->prepare($updateQuery);
        $stmt->bind_param("sssiii", $tomoPrimer, $folioPrimer, $fecha, $legajo, $materiaId, $turno);
    } else {
        // Actualizar todos los campos si la nota no es 0.00
        $updateQuery = "UPDATE nota_examen_final 
                        SET nota = ?, tomo = ?, folio = ?, fecha = ?, turno = ? 
                        WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 1 AND turno = ?";
        $stmt = $conexion->prepare($updateQuery);
        $stmt->bind_param("dsssiiii", $notaFinalPrimer, $tomoPrimer, $folioPrimer, $fecha, $turno, $legajo, $materiaId, $turno);
    }
    $stmt->execute();
    } else {
        // Insertar nuevo registro si no existe
        $insertQuery = "INSERT INTO nota_examen_final (alumno_legajo, profesor_idProrfesor, materias_idMaterias, nota, fecha, turno, tomo, folio, llamado) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $conexion->prepare($insertQuery);
        $stmt->bind_param("iisdssss", $legajo, $profesorId, $materiaId, $notaFinalPrimer, $fecha, $turno, $tomoPrimer, $folioPrimer);
        $stmt->execute();
    }
}

    // Procesar Segundo Llamado
$segundoNota = isset($notasSegundoLlamado[$index]) ? $notasSegundoLlamado[$index] : null;
$segundoAusente = isset($ausenteSegundoLlamado[$index]) ? $ausenteSegundoLlamado[$index] : null;
$tomoSegundo = $tomosSegundoLlamado[$index] ?? null;
$folioSegundo = $foliosSegundoLlamado[$index] ?? null;
$notaFinalSegundo = $segundoAusente ? 0 : $segundoNota;

// Procesar Segundo Llamado
$segundoNota = isset($notasSegundoLlamado[$index]) ? $notasSegundoLlamado[$index] : null;
$segundoAusente = isset($ausenteSegundoLlamado[$index]) ? $ausenteSegundoLlamado[$index] : null;
$tomoSegundo = $tomosSegundoLlamado[$index] ?? null;
$folioSegundo = $foliosSegundoLlamado[$index] ?? null;
$notaFinalSegundo = $segundoAusente ? 0 : $segundoNota;

if (!empty($segundoNota) || !empty($segundoAusente) || $segundoNota == 0.00) {
    // Verificar si existe un registro previo
    $checkQuery = "SELECT COUNT(*) AS count FROM nota_examen_final WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 2";
    $stmt = $conexion->prepare($checkQuery);
    $stmt->bind_param("ii", $legajo, $materiaId);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->fetch_assoc()['count'] > 0;

    if ($exists) {
    if ($segundoNota == 0.00) {
        // Solo actualizar tomo y folio si la nota es 0.00
        $updateQuery = "UPDATE nota_examen_final 
                        SET tomo = ?, folio = ?, fecha = ? 
                        WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 2 AND turno = ?";
        $stmt = $conexion->prepare($updateQuery);
        $stmt->bind_param("sssiii", $tomoSegundo, $folioSegundo, $fecha, $legajo, $materiaId, $turno);
    } else {
        // Actualizar todos los campos si la nota no es 0.00
        $updateQuery = "UPDATE nota_examen_final 
                        SET nota = ?, tomo = ?, folio = ?, fecha = ?, turno = ? 
                        WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 2 AND turno = ?";
        $stmt = $conexion->prepare($updateQuery);
        $stmt->bind_param("dsssiiii", $notaFinalSegundo, $tomoSegundo, $folioSegundo, $fecha, $turno, $legajo, $materiaId, $turno);
    }
    $stmt->execute();

    } else {
        // Insertar nuevo registro si no existe
        $insertQuery = "INSERT INTO nota_examen_final (alumno_legajo, profesor_idProrfesor, materias_idMaterias, nota, fecha, turno, tomo, folio, llamado) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 2)";
        $stmt = $conexion->prepare($insertQuery);
        $stmt->bind_param("iisdssss", $legajo, $profesorId, $materiaId, $notaFinalSegundo, $fecha, $turno, $tomoSegundo, $folioSegundo);
        $stmt->execute();
    }
}

}

        // // Redirigir con parámetros
         echo "<script>
             alert('Operación completada exitosamente.');
             window.location.href = 'nota_examen_final.php?materia=$materiaId&turno=$turno&año=$año';
         </script>";
    } catch (Exception $e) {
        echo "<script>alert('Error al guardar los datos: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>