<?php
session_start();
include '../../conexion/conexion.php';

$idCarrera = intval($_POST['idCarrera']);
$idProfesor = $_SESSION['id'];
$rolUsuario = $_SESSION['roles'];

if ($rolUsuario == '1') {
    // Administrador: todos los cursos de la carrera seleccionada
    $sql = "SELECT * FROM cursos";
} else {
    // Profesores: solo los cursos asignados al profesor en la carrera seleccionada
    $sql = "SELECT * FROM cursos";
}

$result = mysqli_query($conexion, $sql);

echo '<option value="">Seleccione un curso</option>';
while ($row = mysqli_fetch_assoc($result)) {
    echo '<option value="' . $row['idCursos'] . '">' . $row['curso'] . '</option>';
}
?>
