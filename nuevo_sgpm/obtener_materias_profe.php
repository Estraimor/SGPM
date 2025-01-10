
<?php
session_start();
include '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrera_id'])) {
    $carreraId = $_POST['carrera_id'];
    $profesorId = $_SESSION['id']; // ID del profesor desde la sesión
    $rolUsuario = $_SESSION['roles']; // Rol del usuario desde la sesión

    // Consulta base para obtener materias dependiendo del rol
    if ($rolUsuario == 1 || $rolUsuario == 5) {
        // Si el rol es 1 o 5, obtener todas las materias de la carrera
        $sql = "SELECT m.idMaterias, m.Nombre 
                FROM materias m
                WHERE m.carreras_idCarrera = '$carreraId'";
    } else {
        // Si el rol es diferente, obtener solo las materias asociadas al usuario
        $sql = "SELECT m.idMaterias, m.Nombre 
                FROM materias m
                WHERE m.carreras_idCarrera = '$carreraId' 
                AND m.profesor_idProrfesor  = '$profesorId'";
    }

    $result = mysqli_query($conexion, $sql);

    $materias = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $materias[] = $row;
        }
    }

    echo json_encode($materias);
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
