<?php
include '../../conexion/conexion.php';

if (isset($_POST['anio']) && isset($_POST['legajo']) && isset($_POST['idMateria'])) {
    $anio = $_POST['anio'];
    $legajo = $_POST['legajo'];
    $idMateria = $_POST['idMateria'];

    $stmt = $conexion->prepare("
        SELECT nota_final, condicion 
        FROM notas 
        WHERE alumno_legajo = ? AND materias_idMaterias = ? AND YEAR(fecha) = ?
        ORDER BY fecha DESC 
        LIMIT 1
    ");

    $stmt->bind_param("iii", $legajo, $idMateria, $anio);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode([
        'nota_final' => $row['nota_final'] ?? 'No disponible',
        'condicion' => $row['condicion'] ?? 'No disponible'
    ]);

    $stmt->close();
}
?>
