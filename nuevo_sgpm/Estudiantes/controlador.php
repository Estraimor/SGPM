<?php

$server='localhost';
$user='u756746073_root';
$pass='POLITECNICOmisiones2023.';
$bd='u756746073_politecnico';
$conexion=mysqli_connect($server,$user,$pass,$bd, '3306');

if ($conexion) { echo ""; } else { echo "conexion not connected"; }


session_start();

// Verificar si existe un rol de preceptor en la sesión
if (isset($_SESSION["roles"])) {
    // Redireccionar según el rol del usuario usando switch case
    switch ($_SESSION["roles"]) {
        case 1:
            header("Location: ../index.php"); // Administracion
            exit();
        case 2:
            header("Location: ../index.php"); // Programador
            exit();
        case 3:
            header("Location: ../indexs/preceptor_1.php"); // Manu
            exit();
        case 4:
            header("Location: ../indexs/preceptor_2.php"); // Mariela
            exit();
        case 5:
            header("Location: ../indexs/preceptor_3.php"); // Carla kensel
            exit();
        default:
            echo '<div class="alert alert-danger" role="alert">!! ACCESO DENEGADO!!</div>';
    }
} else {
    echo '<div class="alert alert-danger" role="alert">!! ACCESO DENEGADO!!</div>';
}
?>
