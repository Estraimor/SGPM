<?php
include '../../../../conexion/conexion.php';

if (isset($_POST['Enviar'])) {
    // Sanitizar y validar los datos de entrada
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_alumno']);
    $apellido = mysqli_real_escape_string($conexion, $_POST['apellido_alumno']);
    $dni = mysqli_real_escape_string($conexion, $_POST['dni_alumno']);
    $celular = mysqli_real_escape_string($conexion, $_POST['celular']);
    $legajo = mysqli_real_escape_string($conexion, $_POST['legajo']);
    $fecha_nacimiento = mysqli_real_escape_string($conexion, $_POST['fecha_nacimiento']);
    $observaciones = mysqli_real_escape_string($conexion, $_POST['observaciones']);
    $trabaja_HS = mysqli_real_escape_string($conexion, $_POST['Trabaja_Horario']);
    $estado = mysqli_real_escape_string($conexion, $_POST['estado']);
    $titulo_secundario = mysqli_real_escape_string($conexion, $_POST['titulo_secundario']);
    $escuela_secundaria = mysqli_real_escape_string($conexion, $_POST['escuela_secundaria']);
    $ocupacion = mysqli_real_escape_string($conexion, $_POST['ocupacion']);
    $domicilio_laboral = mysqli_real_escape_string($conexion, $_POST['domicilio_laboral']);
    $pago = mysqli_real_escape_string($conexion, $_POST['pago']);
    $calle_domicilio = mysqli_real_escape_string($conexion, $_POST['calle_domicilio']);
    $barrio_domicilio = mysqli_real_escape_string($conexion, $_POST['barrio_domicilio']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    
    // Otros campos
    $ciudad_nacimiento = mysqli_real_escape_string($conexion, $_POST['ciudad_nacimiento']);
    $provincia_nacimiento = mysqli_real_escape_string($conexion, $_POST['provincia_nacimiento']);
    $pais_nacimiento = mysqli_real_escape_string($conexion, $_POST['pais_nacimiento']);
    $horario_laboral_desde = mysqli_real_escape_string($conexion, $_POST['horario_laboral_desde']);
    $horario_laboral_hasta = mysqli_real_escape_string($conexion, $_POST['horario_laboral_hasta']);
    $telefono_urgencias = mysqli_real_escape_string($conexion, $_POST['telefono_urgencias']);
    $discapacidad = mysqli_real_escape_string($conexion, $_POST['discapacidad']);
    
    // Documentación
    $titulo_original = mysqli_real_escape_string($conexion, $_POST['original_titulo']);
    $foto = mysqli_real_escape_string($conexion, $_POST['foto']);
    $folio = mysqli_real_escape_string($conexion, $_POST['folio']);
    $fotocopia_dni = mysqli_real_escape_string($conexion, $_POST['fotocopia_dni']);
    $partida_nacimiento = mysqli_real_escape_string($conexion, $_POST['fotocopia_partida_nacimiento']);
    $cuil_cons = mysqli_real_escape_string($conexion, $_POST['constancia_cuil']);
    $carrera = mysqli_real_escape_string($conexion, $_POST['carrera']);
    $cuil = mysqli_real_escape_string($conexion, $_POST['cuil']);
    
    // Actualizar datos del alumno
    $sql = "UPDATE alumno SET 
                nombre_alumno = '$nombre', 
                apellido_alumno = '$apellido', 
                dni_alumno = '$dni', 
                celular = '$celular', 
                fecha_nacimiento = '$fecha_nacimiento', 
                observaciones = '$observaciones', 
                Trabaja_Horario = '$trabaja_HS',
                estado = '$estado',
                Titulo_secundario = '$titulo_secundario',
                escuela_secundaria = '$escuela_secundaria',
                ocupacion = '$ocupacion',
                domicilio_laboral = '$domicilio_laboral',
                Pago = '$pago',
                calle_domicilio = '$calle_domicilio',
                barrio_domicilio = '$barrio_domicilio',
                correo = '$correo',
                ciudad_nacimiento = '$ciudad_nacimiento',
                provincia_nacimiento = '$provincia_nacimiento',
                pais_nacimiento = '$pais_nacimiento',
                cuil = '$cuil',
                horario_laboral_desde = '$horario_laboral_desde',
                horario_laboral_hasta = '$horario_laboral_hasta',
                telefono_urgencias = '$telefono_urgencias',
                discapacidad = '$discapacidad',
                original_titulo = '$titulo_original',
                fotos = '$foto',
                folio = '$folio',
                fotocopia_dni = '$fotocopia_dni',
                fotocopia_partida_nacimiento = '$partida_nacimiento',
                constancia_cuil = '$cuil_cons',
                carrera = '$carrera',
                cuil = '$cuil'
            WHERE legajo = '$legajo'";

    // Ejecutar la consulta
    $query = mysqli_query($conexion, $sql);

    // Actualizar carrera
    
    $carrera = $_POST["carreras"];
    $sql_update_carrera = "UPDATE inscripcion_asignatura SET carreras_idCarrera = '$carrera' 
                           WHERE alumno_legajo = '$legajo'";
    $query_carrera = mysqli_query($conexion, $sql_update_carrera);


   if ($query && $query_carrera) {
    $legajo = mysqli_real_escape_string($conexion, $_POST['legajo']);
    echo "<script>
        alert('Los datos del legajo: $legajo se actualizaron correctamente.');
        history.back();
    </script>";
} else {
    $error = mysqli_real_escape_string($conexion, mysqli_error($conexion));
    $legajo = mysqli_real_escape_string($conexion, $_POST['legajo']);
    echo "<script>
        alert('Error al actualizar los datos del legajo: $legajo. Error: $error');
        history.back();
    </script>";
}

}
?>
