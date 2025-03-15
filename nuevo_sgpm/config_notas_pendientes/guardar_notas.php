<?php
include '../../conexion/conexion.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Verificar si el ID del profesor está en la sesión
if (!isset($_SESSION["id"]) || !is_numeric($_SESSION["id"])) {
    echo json_encode([
        "error" => "Sesión expirada o no iniciada.",
        "carrera" => $_POST['carrera'] ?? null,
        "curso" => $_POST['curso'] ?? null,
        "comision" => $_POST['comision'] ?? null,
        "materia" => $_POST['materia'] ?? null
    ]);
    exit;
}

$profesorId = intval($_SESSION["id"]); // Obtener y validar el ID del profesor

// Verificar si se enviaron los datos esperados
if (!isset($_POST['comision'], $_POST['curso'], $_POST['carrera'], $_POST['materia'], $_POST['estudiantes'])) {
    echo json_encode([
        "error" => "Faltan parámetros en la petición.",
        "carrera" => $_POST['carrera'] ?? null,
        "curso" => $_POST['curso'] ?? null,
        "comision" => $_POST['comision'] ?? null,
        "materia" => $_POST['materia'] ?? null
    ]);
    exit;
}

// Validar y limpiar datos de entrada
$comision = filter_var($_POST['comision'], FILTER_VALIDATE_INT);
$curso = filter_var($_POST['curso'], FILTER_VALIDATE_INT);
$carrera = filter_var($_POST['carrera'], FILTER_VALIDATE_INT);
$idMateria = filter_var($_POST['materia'], FILTER_VALIDATE_INT);
$estudiantes = $_POST['estudiantes'];

if (!$comision || !$curso || !$carrera || !$idMateria || !is_array($estudiantes)) {
    echo json_encode([
        "error" => "Datos inválidos.",
        "carrera" => $carrera,
        "curso" => $curso,
        "comision" => $comision,
        "materia" => $idMateria
    ]);
    exit;
}

// Obtener la fecha actual con el año 2023
$fechaActual = date("Y-m-d");
$fecha2023 = "2023-" . date("m-d", strtotime($fechaActual));

// Iniciar transacción
$conexion->begin_transaction();

try {
    // Preparar consultas de inserción y actualización
    $stmtInsert = $conexion->prepare("
        INSERT INTO notas (alumno_legajo, carreras_idCarrera, materias_idMaterias, profesor_idProfesor, nota_final, condicion, fecha)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmtUpdate = $conexion->prepare("
        UPDATE notas 
        SET nota_final = ?, condicion = ?, fecha = ? 
        WHERE alumno_legajo = ? AND carreras_idCarrera = ? AND materias_idMaterias = ?
    ");

    $stmtCheck = $conexion->prepare("
        SELECT idnotas FROM notas 
        WHERE alumno_legajo = ? AND carreras_idCarrera = ? AND materias_idMaterias = ?
    ");

    $countInserted = 0;
    $countUpdated = 0;

    foreach ($estudiantes as $estudiante) {
        // Validar que los datos del estudiante sean correctos
        if (!isset($estudiante['legajo'], $estudiante['nota_final'], $estudiante['condicion'])) {
            continue; // Si falta algún dato, ignorar este registro
        }

        $legajo = filter_var($estudiante['legajo'], FILTER_VALIDATE_INT);
        $notaFinal = filter_var($estudiante['nota_final'], FILTER_VALIDATE_FLOAT);
        $condicion = trim($estudiante['condicion']);

        if (!$legajo || $notaFinal === false || empty($condicion)) {
            continue; // Ignorar registros con datos inválidos
        }

        // Verificar si ya existe una nota para este estudiante
        $stmtCheck->bind_param("iii", $legajo, $carrera, $idMateria);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            // Actualizar la nota existente
            $stmtUpdate->bind_param("dssiii", $notaFinal, $condicion, $fecha2023, $legajo, $carrera, $idMateria);
            if ($stmtUpdate->execute()) {
                $countUpdated++;
            } else {
                throw new Exception("Error al actualizar la nota del alumno $legajo");
            }
        } else {
            // Insertar nueva nota con el ID del profesor
            $stmtInsert->bind_param("iiiidss", $legajo, $carrera, $idMateria, $profesorId, $notaFinal, $condicion, $fecha2023);
            if ($stmtInsert->execute()) {
                $countInserted++;
            } else {
                throw new Exception("Error al insertar la nota del alumno $legajo");
            }
        }
    }

    // Confirmar transacción
    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "Notas procesadas correctamente.",
        "insertados" => $countInserted,
        "actualizados" => $countUpdated,
        "carrera" => $carrera,
        "curso" => $curso,
        "comision" => $comision,
        "materia" => $idMateria
    ]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conexion->rollback();
    echo json_encode([
        "error" => "Transacción revertida: " . $e->getMessage(),
        "carrera" => $carrera,
        "curso" => $curso,
        "comision" => $comision,
        "materia" => $idMateria
    ]);
}

// Cerrar consultas preparadas
$stmtInsert->close();
$stmtUpdate->close();
$stmtCheck->close();
?>
