<?php
session_start();
include '../../conexion/conexion.php'; // Conexión a la BD

header("Content-Type: application/json");

if (!isset($_SESSION["id"])) {
    echo json_encode(["success" => false, "error" => "No tienes sesión activa"]);
    exit;
}

$profesor_id = $_SESSION["id"]; // ID del profesor desde la sesión
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "No se recibieron datos"]);
    exit;
}

// Validación de datos esenciales
$comision = $data["comision"] ?? null;
$curso = $data["curso"] ?? null;
$carrera = $data["carrera"] ?? null;
$materia = $data["materia"] ?? null;
$turno = $data["turno"] ?? null;
$estudiantes = $data["estudiantes"] ?? [];

if (!$comision || !$curso || !$carrera || !$materia || !$turno || empty($estudiantes)) {
    echo json_encode(["success" => false, "error" => "Faltan datos obligatorios"]);
    exit;
}

$query = "INSERT INTO nota_examen_final (alumno_legajo, profesor_idProrfesor, materias_idMaterias, nota, fecha, turno, tomo, folio, llamado)
          VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE 
          nota = VALUES(nota), fecha = VALUES(fecha), turno = VALUES(turno), 
          tomo = VALUES(tomo), folio = VALUES(folio), llamado = VALUES(llamado)";

$stmt = $conexion->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Error en la preparación de la consulta"]);
    exit;
}

foreach ($estudiantes as $est) {
    $legajo = $est["legajo"];

    // Verifica si el primer llamado tiene nota válida (mayor o igual a 0)
    if (isset($est["nota1"]) && $est["nota1"] !== "" && $est["nota1"] !== null) {
        $nota1 = is_numeric($est["nota1"]) ? $est["nota1"] : null;
        
        // Si la nota es 0 (ausente), tomo y folio deben ser NULL
        $tomo1 = ($nota1 == 0) ? null : ($est["tomo1"] ?? null);
        $folio1 = ($nota1 == 0) ? null : ($est["folio1"] ?? null);
        $llamado1 = 1;

        $stmt->bind_param("iiidisss", $legajo, $profesor_id, $materia, $nota1, $turno, $tomo1, $folio1, $llamado1);
        $stmt->execute();
    }

    // Verifica si el segundo llamado tiene nota válida (mayor o igual a 0)
    if (isset($est["nota2"]) && $est["nota2"] !== "" && $est["nota2"] !== null) {
        $nota2 = is_numeric($est["nota2"]) ? $est["nota2"] : null;
        
        // Si la nota es 0 (ausente), tomo y folio deben ser NULL
        $tomo2 = ($nota2 == 0) ? null : ($est["tomo2"] ?? null);
        $folio2 = ($nota2 == 0) ? null : ($est["folio2"] ?? null);
        $llamado2 = 2;

        $stmt->bind_param("iiidisss", $legajo, $profesor_id, $materia, $nota2, $turno, $tomo2, $folio2, $llamado2);
        $stmt->execute();
    }
}

$stmt->close();
$conexion->close();

echo json_encode(["success" => true, "message" => "Notas guardadas correctamente"]);
?>
