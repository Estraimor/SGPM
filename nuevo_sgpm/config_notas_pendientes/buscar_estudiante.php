<?php
include '../../conexion/conexion.php';

if (isset($_POST['query']) || isset($_POST['dni'])) {
    $query = isset($_POST['query']) ? $_POST['query'] : '';
    $dni = isset($_POST['dni']) ? $_POST['dni'] : '';

    // Si se busca por DNI
    if (!empty($dni)) {
        $stmt = $conexion->prepare("SELECT legajo, dni_alumno, CONCAT(nombre_alumno, ' ', apellido_alumno) AS nombre_completo 
                                    FROM alumno 
                                    WHERE dni_alumno LIKE ? 
                                    ORDER BY apellido_alumno, nombre_alumno");
        $search = "%$dni%";
        $stmt->bind_param("s", $search);
    } 
    // Si se busca por nombre, apellido o legajo
    else {
        $stmt = $conexion->prepare("SELECT legajo, dni_alumno, CONCAT(nombre_alumno, ' ', apellido_alumno) AS nombre_completo 
                                    FROM alumno 
                                    WHERE legajo LIKE ? OR nombre_alumno LIKE ? OR apellido_alumno LIKE ? 
                                    ORDER BY apellido_alumno, nombre_alumno");
        $search = "%$query%";
        $stmt->bind_param("sss", $search, $search, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Generar opciones para el select
    while ($row = $result->fetch_assoc()) {
        echo "<option hidden>Selecciona estudiante</option>";
        echo "<option value='{$row['legajo']}'>{$row['nombre_completo']} (Legajo: {$row['legajo']} - DNI: {$row['dni_alumno']})</option>";
    }

    $stmt->close();
}
?>
