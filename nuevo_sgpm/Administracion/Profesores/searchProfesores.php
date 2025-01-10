<?php
include'../../../conexion/conexion.php';
$search = isset($_POST['search']) ? $_POST['search'] : '';  // Comprueba si la variable está seteada.

if ($search) {
    $term = '%' . $search . '%';
    $query = "SELECT idProrfesor, nombre_profe, apellido_profe FROM profesor WHERE nombre_profe LIKE ? OR apellido_profe LIKE ? OR dni_profe LIKE ?";
    $stmt = $conexion->prepare($query);
    if ($stmt) {
        $stmt->bind_param("sss", $term, $term, $term);
        $stmt->execute();
        $result = $stmt->get_result();
        $profesores = [];
        while ($row = $result->fetch_assoc()) {
            $profesores[] = $row;
        }
        echo json_encode($profesores);
    } else {
        echo json_encode(['error' => 'Error preparing statement']);  // Añade error de preparación de consulta
    }
} else {
    echo json_encode(['error' => 'No search term provided']);  // Añade error de falta de término de búsqueda
}
?>
