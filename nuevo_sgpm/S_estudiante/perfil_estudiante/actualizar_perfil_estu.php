<?php

session_start();
include'../../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre_alumno'];
    $apellido = $_POST['apellido_alumno'];
    $dni = $_POST['dni_alumno'];
    $celular = $_POST['celular'];
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $legajo = $_SESSION['id']; // Obtener el legajo del estudiante desde la sesión

    // Consulta de actualización con prepared statements
$sql_update = "UPDATE alumno 
               SET nombre_alumno = ?, 
                   apellido_alumno = ?, 
                   dni_alumno = ?, 
                   celular = ?, 
                   correo = ?, 
                   usu_alumno = ?, 
                   pass_alumno = ?
               WHERE legajo = ?";

$stmt = mysqli_prepare($conexion, $sql_update);
mysqli_stmt_bind_param($stmt, 'sssssssi', $nombre, $apellido, $dni, $celular, $email, $usuario, $password, $legajo);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Datos actualizados correctamente'); window.location.href='../perfil_estudiante.php';</script>";
} else {
     echo "<script>alert('Error al actualizar los datos: " . mysqli_stmt_error($stmt) . "'); window.history.back();</script>";
}
}
?>