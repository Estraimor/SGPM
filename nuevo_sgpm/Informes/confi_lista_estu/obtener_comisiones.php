
<?php
//obtener_comisiones.php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ajusta esta ruta hasta apuntar a tu conexión
$conexion = mysqli_connect('localhost', 'u756746073_root', 'POLITECNICOmisiones2023.', 'u756746073_politecnico', '3306');

session_start();
$idPreceptor = $_SESSION['id'];
$rolUsuario = $_SESSION["roles"];

if (! isset($_GET['idCarrera'], $_GET['idCurso'])) {
    echo json_encode(['error' => 'Parámetros incorrectos']);
    exit;
}

$idCarrera = intval($_GET['idCarrera']);
$idCurso   = intval($_GET['idCurso']);

try {
    if ($rolUsuario == 1) {
        $sql = "SELECT DISTINCT co.idComisiones, co.comision
                FROM materias m
                INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones
                WHERE m.carreras_idCarrera = ? AND m.cursos_idCursos = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ii', $idCarrera, $idCurso);
    } else {
        $sql = "SELECT DISTINCT co.idComisiones, co.comision
                FROM materias m
                INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones
                INNER JOIN preceptores p
                  ON p.comisiones_idComisiones = co.idComisiones
                 AND p.cursos_idCursos       = m.cursos_idCursos
                WHERE m.carreras_idCarrera = ?
                  AND m.cursos_idCursos    = ?
                  AND p.profesor_idProrfesor = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('iii', $idCarrera, $idCurso, $idPreceptor);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $comisiones = [];
    while ($row = $result->fetch_assoc()) {
        $comisiones[] = $row;
    }
    echo json_encode($comisiones);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
