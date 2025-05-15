<?php
//obtener_materias.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../../conexion/conexion.php';

// Validación segura de parámetros
$idCarrera  = isset($_GET['idCarrera'])  ? intval($_GET['idCarrera'])  : null;
$idCurso    = isset($_GET['idCurso'])    ? intval($_GET['idCurso'])    : null;
$idComision = isset($_GET['idComision']) ? intval($_GET['idComision']) : null;

// Si falta algún dato, devolvemos error
if (!$idCarrera || !$idCurso || !$idComision) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan parámetros']);
    exit;
}

// Consulta
$sql = "
  SELECT idMaterias, Nombre
  FROM materias
  WHERE carreras_idCarrera = $idCarrera
    AND cursos_idCursos = $idCurso
    AND comisiones_idComisiones = $idComision
    AND estado = 1
  ORDER BY Nombre
";

// Ejecutar consulta
$result = mysqli_query($conexion, $sql);

// Verificación por si hay error SQL
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta SQL', 'detalle' => mysqli_error($conexion)]);
    exit;
}

// Armar array de materias
$materias = [];
while ($fila = mysqli_fetch_assoc($result)) {
    $materias[] = $fila;
}

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode($materias);
?>
