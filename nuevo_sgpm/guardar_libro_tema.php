<?php
include '../conexion/conexion.php';

// Verificamos que se haya enviado el formulario
if (isset($_POST['enviar'])) {
    // Recibir y limpiar (sugerido: usar funciones de validación) los datos del formulario
    $carrera    = intval($_POST['carrera']);
    $materia    = $_POST['materias'];  // Se espera que venga un string de materias (ej. "1,2,3") o un array
    $profesor   = intval($_POST['profesor']);
    $capacidades = trim($_POST['capacidades']);
    $contenidos  = trim($_POST['contenidos']);
    $evaluacion  = trim($_POST['evaluacion']);
    $fecha       = $_POST['fecha']; // Asegúrate de validar el formato de fecha
    $observacion = trim($_POST['observacion']);

    // Si $_POST['materias'] es un array, usamos el primer valor; si es string, lo convertimos a entero
    if (is_array($materia)) {
        $materia_id = intval($materia[0]);  
    } else {
        $materia_id = intval($materia);  
    }

    // Se recomienda usar prepared statements para evitar inyección SQL
    $sql = "INSERT INTO libro_tema 
            (profesor_idProrfesor, carreras_idCarrera, materias_idMaterias, capacidades, contenidos, evaluacion, observacion_diaria, fecha) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación: " . $conexion->error);
    }

    // Vincular parámetros (asumimos que capacidades, contenidos, evaluación, observacion y fecha son strings)
    $stmt->bind_param('iiisssss', $profesor, $carrera, $materia_id, $capacidades, $contenidos, $evaluacion, $observacion, $fecha);

    if ($stmt->execute()) {
        echo "<script>
                alert('Los datos se han guardado exitosamente.');
                window.location.href = 'pre_libro.php';
              </script>";
        exit();
    } else {
        echo "Error al guardar: " . $stmt->error;
    }
    
    $stmt->close();
}
?>
