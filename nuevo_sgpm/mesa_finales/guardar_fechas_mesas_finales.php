<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Configurar la zona horaria a Buenos Aires
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Recoger datos del formulario
    $fecha = $_POST['fecha'];
    $llamado = $_POST['llamado'];
    $tanda = $_POST['tanda'];
    $cupo = $_POST['cupo'];
    $tandaCargada = false; // Bandera para verificar si se cargó el módulo de tandas principal
    $mesaPedagogicaCargada = false; // Bandera para verificar si se cargaron las materias pedagógicas

    // Verificar si se completaron todos los campos para la tanda principal
    if (!empty($fecha) && !empty($llamado) && !empty($tanda) && !empty($cupo) && isset($_POST['carrera']) && isset($_POST['materias']) && !empty($_POST['carrera'][0]) && !empty($_POST['materias'][0])) {
        // Insertar la tanda en la base de datos
        $queryTanda = "INSERT INTO tandas (fecha, llamado, tanda, cupo) VALUES ('$fecha', '$llamado', '$tanda', '$cupo')";
        if (mysqli_query($conexion, $queryTanda)) {
            // Obtener el ID de la tanda recién creada
            $idtandas = mysqli_insert_id($conexion);

            // Recorrer todas las combinaciones de carreras y materias
            foreach ($_POST['carrera'] as $index => $carrera_id) {
                $materia_id = $_POST['materias'][$index];

                // Insertar cada combinación de materia y tanda en la tabla fechas_mesas_finales
                $queryMesa = "INSERT INTO fechas_mesas_finales (materias_idMaterias, tandas_idtandas) VALUES ('$materia_id', '$idtandas')";
                mysqli_query($conexion, $queryMesa);
            }
            $tandaCargada = true;
        } else {
            echo "<script>alert('Error al guardar la tanda: " . mysqli_error($conexion) . "');</script>";
            exit();
        }
    }

    // Guardar la Materia Principal y Pedagógica
    if (!empty($_POST['materia_principal_1']) && !empty($_POST['materia_pedagogica_2'])) {
        $materiaPrincipal = $_POST['materia_principal_1'];
        $materiaPedagogica = $_POST['materia_pedagogica_2'];

        // Insertar en la tabla mesas_pedagogicas
        $queryMesaPedagogica = "INSERT INTO mesas_pedagogicas (materias_idMaterias, materias_idMaterias1, fecha) VALUES ('$materiaPrincipal', '$materiaPedagogica', now())";
        if (mysqli_query($conexion, $queryMesaPedagogica)) {
            $mesaPedagogicaCargada = true;
        } else {
            echo "<script>alert('Error al guardar las materias pedagógicas: " . mysqli_error($conexion) . "');</script>";
            exit();
        }
    }

    // Mensaje de confirmación
    $mensaje = "Resultado de la carga:\\n";
    if ($tandaCargada) $mensaje .= "- Se cargó el módulo de tandas principal.\\n";
    if ($mesaPedagogicaCargada) $mensaje .= "- Se cargó la relación de materias pedagógicas.\\n";
    if (!$tandaCargada && !$mesaPedagogicaCargada) $mensaje .= "No se cargó ningún módulo.";

    echo "<script>alert('$mensaje'); window.location.href = '../gestionar_mesas_finales.php';</script>";
    exit();
}
?>
