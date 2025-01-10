<?php
include '../../../conexion/conexion.php';

// Recoger los datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$dni = $_POST['dni'];
$celular = $_POST['celular'];
$email = $_POST['email'];
$usuario = $_POST['usuario'];
$pass = $_POST['pass'];
$rol = $_POST['rol'];
$titulo = $_POST['Titulo'];
$direccion = $_POST['Direccion'];

// Preparar y vincular parámetros
$stmt = $conexion->prepare("INSERT INTO profesor (nombre_profe, apellido_profe, dni_profe, celular, email, usuario, pass, rol, Titulo, Direccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss", $nombre, $apellido, $dni, $celular, $email, $usuario, $pass, $rol, $titulo, $direccion);

// Ejecutar
if ($stmt->execute()) {
    echo "<script>alert('Profesor registrado exitosamente.'); window.location.href = '../../index.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

// Cerrar sentencia y conexión
$stmt->close();
$conexion->close();
?>
