<?php
session_start();
include '../../conexion/conexion.php';

$idCarrera = intval($_POST['idCarrera']);
$idProfesor = $_SESSION['id'];
$rolUsuario = $_SESSION['roles'];

if ($rolUsuario == '1') {
    // Administrador: todas las comisiones de la carrera seleccionada
    $sql = "SELECT * FROM comisiones";
} else {
    // Profesores: solo las comisiones asignadas al profesor en la carrera seleccionada
    $sql = "SELECT * FROM comisiones";
}

$result = mysqli_query($conexion, $sql);

echo '<option value="">Seleccione una comisión</option>';
while ($row = mysqli_fetch_assoc($result)) {
    echo '<option value="' . $row['idComisiones'] . '">' . $row['comision'] . '</option>';
}
?>
