<?php
include '../../../conexion/conexion.php';

if (isset($_POST['enviar'])) {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $celular = $_POST['celular'];
    $legajo = $_POST['legajo'];
    $observaciones = $_POST['observaciones'];
    $trabaja = $_POST['trabaja'];
    
    $fecha_nacimiento = $_POST['edad'];

        // Convertir la fecha de nacimiento a un objeto DateTime
        $fecha_nacimiento_dt = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);

        // Verificar si se creó correctamente el objeto DateTime
        if ($fecha_nacimiento_dt instanceof DateTime) {
            // Obtener la fecha actual
            $fecha_actual = new DateTime();
    
            // Calcular la diferencia en años entre la fecha actual y la fecha de nacimiento
            $diff = $fecha_actual->diff($fecha_nacimiento_dt);
    
            // Calcular la edad restando el año de nacimiento del año actual
            $edad = $fecha_actual->format('Y') - $fecha_nacimiento_dt->format('Y');
    
            // Verificar si la fecha de nacimiento ya ha ocurrido este año
            if ($fecha_actual < $fecha_nacimiento_dt->add(new DateInterval('P'.$edad.'Y'))) {
                $edad--;
            }
    
            // Imprimir la edad
        } else {
            // Si $fecha_nacimiento_dt no es un objeto DateTime válido
        }

    
    $fp1 = isset($_POST['FP1']) ? $_POST['FP1'] : null; // Si FP1 no se seleccionó, establecer NULL
    $fp2 = isset($_POST['FP2']) ? $_POST['FP2'] : null; // Si FP2 no se seleccionó, establecer NULL
    $fp3 = isset($_POST['FP3']) ? $_POST['FP3'] : null; // Si FP3 no se seleccionó, establecer NULL
    $fp4 = isset($_POST['FP4']) ? $_POST['FP4'] : null; // Si FP4 no se seleccionó, establecer NULL

    

 //Insertar los datos en la tabla de la base de datos
     $sql = "INSERT INTO alumnos_fp (legajo_afp, nombre_afp, apellido_afp, dni_afp, celular_afp, observaciones_afp, trabaja_fp,edad, carreras_idCarrera, carreras_idCarrera1, carreras_idCarrera2, carreras_idCarrera3) 
            VALUES ('$legajo', '$nombre', '$apellido', '$dni', '$celular', '$observaciones', '$trabaja','$edad', $fp1, $fp2, $fp3, $fp4)";
    
    $query = mysqli_query($conexion, $sql);
    
     if ($query) {
         echo "Datos guardados correctamente.";
       // Redirigir a la página de nuevo_alumnofp.php
         header("Location: ../controlador.php");
         exit();
     } else {
         echo "Error al guardar los datos: " . mysqli_error($conexion);
     }
 }
?>
