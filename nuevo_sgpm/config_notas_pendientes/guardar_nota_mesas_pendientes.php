<?php
session_start();
include '../../conexion/conexion.php';

header("Content-Type: application/json");

if (!isset($_SESSION["id"])) {
    echo json_encode(["success" => false, "error" => "No tienes sesión activa"]);
    exit;
}

$profesor_id = $_SESSION["id"];
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "No se recibieron datos"]);
    exit;
}

$comision = $data["comision"] ?? null;
$carrera = $data["carrera"] ?? null;
$materia = $data["materia"] ?? null;
$turno = $data["turno"] ?? null;
$estudiantes = $data["estudiantes"] ?? [];

if (!$comision || !$carrera || !$materia || !$turno || empty($estudiantes)) {
    echo json_encode(["success" => false, "error" => "Faltan datos obligatorios"]);
    exit;
}

// Recorremos estudiantes
foreach ($estudiantes as $est) {
    $legajo = $est["legajo"];

    // === PRIMER LLAMADO ===
    if (isset($est["nota1"]) && $est["nota1"] !== "") {
        $nota1 = is_numeric($est["nota1"]) ? floatval($est["nota1"]) : 0.0;
        $tomo1 = ($nota1 == 0) ? null : ($est["tomo1"] ?? null);
        $folio1 = ($nota1 == 0) ? null : ($est["folio1"] ?? null);
        $llamado1 = 1;

        $verificar = $conexion->prepare("SELECT idnota_examen_final FROM nota_examen_final WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = ?");
        $verificar->bind_param("iii", $legajo, $materia, $llamado1);
        $verificar->execute();
        $verificar->store_result();

        if ($verificar->num_rows > 0) {
            $update = $conexion->prepare("UPDATE nota_examen_final SET nota = ?, fecha = NOW(), turno = ?, tomo = ?, folio = ?, profesor_idProrfesor = ? WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = ?");
            $update->bind_param("dissiiii", $nota1, $turno, $tomo1, $folio1, $profesor_id, $legajo, $materia, $llamado1);
            $update->execute();
            $update->close();
        } else {
            $insert = $conexion->prepare("INSERT INTO nota_examen_final (alumno_legajo, profesor_idProrfesor, materias_idMaterias, nota, fecha, turno, tomo, folio, llamado) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)");
            $insert->bind_param("iiidisss", $legajo, $profesor_id, $materia, $nota1, $turno, $tomo1, $folio1, $llamado1);
            $insert->execute();
            $insert->close();
        }

        $verificar->close();
    }

   // === SEGUNDO LLAMADO ===
if (array_key_exists("nota2", $est) && ($est["nota2"] !== null)) {
    $nota2 = is_numeric($est["nota2"]) ? floatval($est["nota2"]) : 0.0;
    $tomo2 = ($nota2 == 0) ? null : ($est["tomo2"] ?? null);
    $folio2 = ($nota2 == 0) ? null : ($est["folio2"] ?? null);
    $llamado2 = 2;

    $verificar = $conexion->prepare("SELECT idnota_examen_final FROM nota_examen_final WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = ?");
    $verificar->bind_param("iii", $legajo, $materia, $llamado2);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        $update = $conexion->prepare("UPDATE nota_examen_final SET nota = ?, fecha = NOW(), turno = ?, tomo = ?, folio = ?, profesor_idProrfesor = ? WHERE alumno_legajo = ? AND materias_idMaterias = ? AND llamado = ?");
        $update->bind_param("dissiiii", $nota2, $turno, $tomo2, $folio2, $profesor_id, $legajo, $materia, $llamado2);
        $update->execute();
        $update->close();
    } else {
        $insert = $conexion->prepare("INSERT INTO nota_examen_final (alumno_legajo, profesor_idProrfesor, materias_idMaterias, nota, fecha, turno, tomo, folio, llamado) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)");
        $insert->bind_param("iiidisss", $legajo, $profesor_id, $materia, $nota2, $turno, $tomo2, $folio2, $llamado2);
        $insert->execute();
        $insert->close();
    }

    $verificar->close();
}
}

$conexion->close();
echo json_encode(["success" => true, "message" => "Notas guardadas correctamente"]);
?>
