<?php
include '../conexion/conexion.php';

if (isset($_POST['enviar'])){
    $carrera = $_POST['carrera'];
    $materia = $_POST['materias'];  // Asegúrate de recibir el array correctamente
    $profesor = $_POST['profesor'];
    $capacidades = $_POST['capacidades'];
    $contenidos = $_POST['contenidos'];
    $evaluacion = $_POST['evaluacion'];
    $fecha = $_POST['fecha'];
    $observacion = $_POST['observacion'];

    // Si $_POST['materias'] es un array, asegurarte de que seleccionas un solo valor (el primero)
    if (is_array($materia)) {
        $materia_id = $materia[0];  // Selecciona el primer valor del array
    } else {
        $materia_id = $materia;  // Si no es un array, úsalo directamente
    }

    // Consulta para guardar los datos en la base de datos
    $sql = "INSERT INTO `libro_tema` (`profesor_idProrfesor`, `carreras_idCarrera`, `materias_idMaterias`, `capacidades`, `contenidos`, `evaluacion`, `observacion_diaria`, `fecha`) 
            VALUES ('$profesor', '$carrera', '$materia_id', '$capacidades', '$contenidos', '$evaluacion', '$observacion', '$fecha')";

    $query = mysqli_query($conexion, $sql);
    if ($query) {
        // Mostrar mensaje de éxito y redirigir
        echo "<script>
                alert('Los datos se han guardado exitosamente.');
                window.location.href = 'pre_libro.php';
              </script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}
?>
