<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Configurar la zona horaria a Buenos Aires
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Recoger datos del formulario
    $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
    $llamado = mysqli_real_escape_string($conexion, $_POST['llamado']);
    $tanda = mysqli_real_escape_string($conexion, $_POST['tanda']);
    $cupo = mysqli_real_escape_string($conexion, $_POST['cupo']);
    $tandaCargada = false; 
    $mesaPedagogicaCargada = false; 

    // Verificar campos obligatorios para la tanda principal
    if (!empty($fecha) && !empty($llamado) && !empty($tanda) && !empty($cupo) 
        && isset($_POST['carrera']) && isset($_POST['materias']) 
        && !empty($_POST['carrera'][0]) && !empty($_POST['materias'][0])) {
        
        // Insertar la tanda principal en la base de datos
        $queryTanda = "INSERT INTO tandas (fecha, llamado, tanda, cupo) VALUES ('$fecha', '$llamado', '$tanda', '$cupo')";
        
        if (mysqli_query($conexion, $queryTanda)) {
            $idtandas = mysqli_insert_id($conexion);

            // Insertar combinaciones de materia y tanda en fechas_mesas_finales
            foreach ($_POST['materias'] as $index => $materia_id) {
                $comision_id = mysqli_real_escape_string($conexion, $_POST['comision'][$index]);

                $queryMesa = "INSERT INTO fechas_mesas_finales (materias_idMaterias, tandas_idtandas, comision_id) 
                              VALUES ('$materia_id', '$idtandas', '$comision_id')";
                mysqli_query($conexion, $queryMesa);
            }
            $tandaCargada = true;
        } else {
            echo "<script>alert('Error al guardar la tanda: " . mysqli_error($conexion) . "');</script>";
            exit();
        }
    }

    // Guardar Materia Principal y Pedagógica
    if (!empty($_POST['materia_principal_1']) && !empty($_POST['materia_pedagogica_2'])) {
        $materiaPrincipal = mysqli_real_escape_string($conexion, $_POST['materia_principal_1']);
        $materiaPedagogica = mysqli_real_escape_string($conexion, $_POST['materia_pedagogica_2']);

        $queryMesaPedagogica = "INSERT INTO mesas_pedagogicas (materias_idMaterias, materias_idMaterias1, fecha) 
                                VALUES ('$materiaPrincipal', '$materiaPedagogica', NOW())";
        
        if (mysqli_query($conexion, $queryMesaPedagogica)) {
            $mesaPedagogicaCargada = true;
        } else {
            echo "<script>alert('Error al guardar las materias pedagógicas: " . mysqli_error($conexion) . "');</script>";
            exit();
        }
    }

    // Mensaje de Confirmación
    $mensaje = "Resultado de la carga:\\n";
    if ($tandaCargada) $mensaje .= "- Se cargó la tanda principal correctamente.\\n";
    if ($mesaPedagogicaCargada) $mensaje .= "- Se guardaron las materias pedagógicas asociadas.\\n";
    if (!$tandaCargada && !$mesaPedagogicaCargada) $mensaje .= "No se cargó ningún módulo.";

    echo "<script>alert('$mensaje'); window.location.href = '../gestionar_mesas_finales.php';</script>";
    exit();
}
?>
