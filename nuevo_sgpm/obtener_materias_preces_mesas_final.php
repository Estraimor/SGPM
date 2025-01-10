
<?php
session_start();
include '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrera_id'])) {
    $carreraId = $_POST['carrera_id'];
    $profesorId = $_SESSION['id']; // ID del profesor desde la sesión
    $rolUsuario = $_SESSION['roles']; // Rol del usuario desde la sesión

   
        $sql = "SELECT m.idMaterias, m.Nombre 
                FROM materias m
                WHERE m.carreras_idCarrera = '$carreraId'";
    
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
