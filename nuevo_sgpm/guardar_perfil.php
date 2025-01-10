<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../conexion/conexion.php';
session_start();

// Verificar que el usuario esté autenticado
if (empty($_SESSION["id"])) {
    echo "Error: Usuario no autenticado.";
    exit;
}

$idProfesor = $_SESSION["id"];

// Obtener los datos actuales de la base de datos para el profesor
$sql_actual = "SELECT * FROM profesor WHERE idProrfesor = '$idProfesor'";
$result = mysqli_query($conexion, $sql_actual);
$current_data = mysqli_fetch_assoc($result);

// Función para usar el valor de la base de datos si el campo está vacío
function getValue($field, $current_data) {
    return !empty($_POST[$field]) ? mysqli_real_escape_string($GLOBALS['conexion'], $_POST[$field]) : $current_data[$field];
}

// Asignar los valores, manteniendo los valores actuales si están vacíos
$nombre_profe = getValue('nombre_profe', $current_data);
$apellido_profe = getValue('apellido_profe', $current_data);
$dni_profe = getValue('dni_profe', $current_data);
$celular = getValue('celular', $current_data);
$email = getValue('email', $current_data);
$usuario = getValue('usuario', $current_data);
$pass = getValue('pass', $current_data);
$Titulo = getValue('Titulo', $current_data);
$Direccion = getValue('Direccion', $current_data);

// Verificar si se ha subido una imagen y procesarla
$avatar = null;
if (!empty($_FILES['imagen']['tmp_name'])) {
    $avatar = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
}

// Crear la consulta SQL de actualización, incluyendo la imagen si se ha subido o eliminándola si se indicó
if (!empty($_POST['eliminar_avatar']) && $_POST['eliminar_avatar'] == '1') {
    // Si se debe eliminar la imagen, establecer avatar como NULL
    $sql_update = "UPDATE profesor SET 
        nombre_profe = '$nombre_profe',
        apellido_profe = '$apellido_profe',
        dni_profe = '$dni_profe',
        celular = '$celular',
        email = '$email',
        usuario = '$usuario',
        pass = '$pass',
        Titulo = '$Titulo',
        Direccion = '$Direccion',
        avatar = NULL
    WHERE idProrfesor = '$idProfesor'";
} else {
    // Si no se debe eliminar la imagen, actualizar normalmente
    $sql_update = "UPDATE profesor SET 
        nombre_profe = '$nombre_profe',
        apellido_profe = '$apellido_profe',
        dni_profe = '$dni_profe',
        celular = '$celular',
        email = '$email',
        usuario = '$usuario',
        pass = '$pass',
        Titulo = '$Titulo',
        Direccion = '$Direccion'";

    if ($avatar) {
        $sql_update .= ", avatar = '$avatar'";
    }

    $sql_update .= " WHERE idProrfesor = '$idProfesor'";
}

// Ejecutar la consulta y verificar si se actualizó correctamente
if (mysqli_query($conexion, $sql_update)) {
    echo "
    <script>
        alert('Los datos se actualizaron correctamente.');
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 0);
    </script>
    ";
} else {
    echo "Error al actualizar los datos: " . mysqli_error($conexion);
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
