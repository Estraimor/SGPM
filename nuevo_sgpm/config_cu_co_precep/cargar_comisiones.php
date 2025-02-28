<?php
include '../../conexion/conexion.php';

$idCarrera = intval($_POST['idCarrera']);
$idProfesor = $_SESSION['id'];
$rolUsuario = $_SESSION['roles'];

if ($rolUsuario == '1') {
    // Administrador: todas las comisiones de la carrera seleccionada
    $sql = "SELECT * FROM comisiones WHERE carreras_idCarrera = $idCarrera";
} else {
    // Profesores: solo las comisiones asignadas al profesor en la carrera seleccionada
    $sql = "SELECT co.*
            FROM comisiones co
            JOIN preceptores p ON co.idComisiones = p.comisiones_idComisiones
            WHERE p.profesor_idProfesor = $idProfesor
            AND p.carreras_idCarrera = $idCarrera
            GROUP BY co.idComisiones";
}

$result = mysqli_query($conexion, $sql);

echo '<option value="">Seleccione una comisión</option>';
while ($row = mysqli_fetch_assoc($result)) {
    echo '<option value="' . $row['idComisiones'] . '">' . $row['nombreComision'] . '</option>';
}
?>
