<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../conexion/conexion.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

$materiaId = $_POST['materia'] ?? null;
$turno = $_POST['turno'] ?? null;
$año = $_POST['año'] ?? null;
$profesorId = $_SESSION['id'] ?? null;
$fecha = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notasPrimerLlamado = $_POST['nota_final_1'] ?? [];
    $ausentePrimerLlamado = $_POST['ausente_1'] ?? [];
    $notasSegundoLlamado = $_POST['nota_final_2'] ?? [];
    $ausenteSegundoLlamado = $_POST['ausente_2'] ?? [];
    $tomosPrimerLlamado = $_POST['tomo_1'] ?? [];
    $foliosPrimerLlamado = $_POST['folio_1'] ?? [];
    $tomosSegundoLlamado = $_POST['tomo_2'] ?? [];
    $foliosSegundoLlamado = $_POST['folio_2'] ?? [];
    $alumnoLegajo = $_POST['alumno_legajo'] ?? [];

    if (is_null($materiaId) || is_null($profesorId) || is_null($fecha)) {
        die("<script>alert('Faltan datos obligatorios.'); window.history.back();</script>");
    }

    try {
        foreach ($alumnoLegajo as $index => $legajo) {
            // ===================== PRIMER LLAMADO =====================
            $primerNota = trim($notasPrimerLlamado[$index] ?? '');
            $primerAusente = isset($ausentePrimerLlamado[$index]) ? 1 : null;
            $tomoPrimer = trim($tomosPrimerLlamado[$index] ?? '');
            $folioPrimer = trim($foliosPrimerLlamado[$index] ?? '');

            $hayTomoFolio = $tomoPrimer !== '' || $folioPrimer !== '';
            $hayNota = $primerNota !== '' && is_numeric($primerNota);
            $guardar = $hayNota || $primerAusente !== null || $hayTomoFolio;

            if ($guardar) {
                $notaFinalPrimer = $primerAusente ? 0 : ($hayNota ? floatval($primerNota) : null);
                $tomoPrimer = $tomoPrimer !== '' ? $tomoPrimer : null;
                $folioPrimer = $folioPrimer !== '' ? $folioPrimer : null;

                $checkQuery = "SELECT COUNT(*) AS count FROM nota_examen_final WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 1";
                $stmt = $conexion->prepare($checkQuery);
                $stmt->bind_param("ii", $legajo, $materiaId);
                $stmt->execute();
                $result = $stmt->get_result();
                $exists = $result->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($exists) {
                    $updateQuery = "UPDATE nota_examen_final 
                        SET nota = ?, tomo = ?, folio = ?, fecha = ?, turno = ? 
                        WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 1 AND turno = ?";
                    $stmt = $conexion->prepare($updateQuery);
                    $stmt->bind_param("dsssiiii", $notaFinalPrimer, $tomoPrimer, $folioPrimer, $fecha, $turno, $legajo, $materiaId, $turno);
                    if (!$stmt->execute()) {
                        throw new Exception("Error al actualizar primer llamado: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    $insertQuery = "INSERT INTO nota_examen_final 
                        (alumno_legajo, profesor_idProrfesor, materias_idMaterias, nota, fecha, turno, tomo, folio, llamado) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
                    $stmt = $conexion->prepare($insertQuery);
                    $stmt->bind_param("iisdsiis", $legajo, $profesorId, $materiaId, $notaFinalPrimer, $fecha, $turno, $tomoPrimer, $folioPrimer);
                    if (!$stmt->execute()) {
                        throw new Exception("Error al insertar primer llamado: " . $stmt->error);
                    }
                    $stmt->close();
                }
            }

            // ===================== SEGUNDO LLAMADO =====================
            $segundoNota = trim($notasSegundoLlamado[$index] ?? '');
            $segundoAusente = isset($ausenteSegundoLlamado[$index]) ? 1 : null;
            $tomoSegundo = trim($tomosSegundoLlamado[$index] ?? '');
            $folioSegundo = trim($foliosSegundoLlamado[$index] ?? '');

            $hayTomoFolio = $tomoSegundo !== '' || $folioSegundo !== '';
            $hayNota = $segundoNota !== '' && is_numeric($segundoNota);
            $guardar = $hayNota || $segundoAusente !== null || $hayTomoFolio;

            if ($guardar) {
                $notaFinalSegundo = $segundoAusente ? 0 : ($hayNota ? floatval($segundoNota) : null);
                $tomoSegundo = $tomoSegundo !== '' ? $tomoSegundo : null;
                $folioSegundo = $folioSegundo !== '' ? $folioSegundo : null;

                $checkQuery = "SELECT COUNT(*) AS count FROM nota_examen_final WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 2";
                $stmt = $conexion->prepare($checkQuery);
                $stmt->bind_param("ii", $legajo, $materiaId);
                $stmt->execute();
                $result = $stmt->get_result();
                $exists = $result->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($exists) {
                    $updateQuery = "UPDATE nota_examen_final 
                        SET nota = ?, tomo = ?, folio = ?, fecha = ?, turno = ? 
                        WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = 2 AND turno = ?";
                    $stmt = $conexion->prepare($updateQuery);
                    $stmt->bind_param("dsssiiii", $notaFinalSegundo, $tomoSegundo, $folioSegundo, $fecha, $turno, $legajo, $materiaId, $turno);
                    if (!$stmt->execute()) {
                        throw new Exception("Error al actualizar segundo llamado: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    $insertQuery = "INSERT INTO nota_examen_final 
                        (alumno_legajo, profesor_idProrfesor, materias_idMaterias, nota, fecha, turno, tomo, folio, llamado) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 2)";
                    $stmt = $conexion->prepare($insertQuery);
                    $stmt->bind_param("iisdsiis", $legajo, $profesorId, $materiaId, $notaFinalSegundo, $fecha, $turno, $tomoSegundo, $folioSegundo);
                    if (!$stmt->execute()) {
                        throw new Exception("Error al insertar segundo llamado: " . $stmt->error);
                    }
                    $stmt->close();
                }
            }
        }

        echo "<script>
            alert('Operación completada exitosamente.');
            window.location.href = '#';
        </script>";
    } catch (Exception $e) {
        echo "<script>alert('Error al guardar los datos: " . addslashes($e->getMessage()) . "'); </script>";
    }
}
?>
