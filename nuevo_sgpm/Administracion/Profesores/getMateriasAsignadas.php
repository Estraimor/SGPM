<?php
include '../../../conexion/conexion.php'; // Incluye tu script de conexión a la base de datos.

$profesorId = isset($_POST['profesorId']) ? $_POST['profesorId'] : null;

if ($profesorId) {
    $query = "SELECT m.idMaterias, m.Nombre, c.nombre_carrera, m.estado,
                     d.idDias_semana, dsm.horario_entrada, dsm.horario_salida
              FROM materias m
              INNER JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
              LEFT JOIN dias_semana_has_materias dsm ON m.idMaterias = dsm.materias_idMaterias
              LEFT JOIN dias_semana d ON dsm.dias_semana_idDias_semana = d.idDias_semana
              WHERE m.profesor_idProrfesor = ?
              ORDER BY m.idMaterias, d.idDias_semana";
    $stmt = $conexion->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $profesorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $materias = [];
        while ($row = $result->fetch_assoc()) {
            $materiaId = $row['idMaterias'];
            if (!isset($materias[$materiaId])) {
                $materias[$materiaId] = [
                    'idMaterias' => $materiaId,
                    'nombre' => $row['Nombre'],
                    'nombre_carrera' => $row['nombre_carrera'],
                    'estado' => $row['estado'],
                    'dias' => []
                ];
            }
            if ($row['idDias_semana']) {
                $materias[$materiaId]['dias'][$row['idDias_semana']] = [
                    'horario_entrada' => $row['horario_entrada'],
                    'horario_salida' => $row['horario_salida']
                ];
            }
        }
        echo json_encode(array_values($materias));
    } else {
        echo json_encode(['error' => 'Error preparando la consulta']);
    }
} else {
    echo json_encode(['error' => 'No se proporcionó ID del profesor']);
}
?>
