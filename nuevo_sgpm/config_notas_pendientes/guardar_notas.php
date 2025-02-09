<?php
include '../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alumno_legajo = $_POST['legajo'];
    $materia = $_POST['materia'];
    $condicion = $_POST['condicion'];
    $notaFinal = $_POST['notaFinal'];
    $notaExamenFinal = $_POST['notaExamenFinal'] ?? NULL;
    $fecha = date('Y-m-d');

    // Insertar en la tabla notas
    $queryNotas = "INSERT INTO notas (alumno_legajo, materias_idMaterias, nota_final, condicion, fecha) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($queryNotas);
    $stmt->bind_param("iiiss", $alumno_legajo, $materia, $notaFinal, $condicion, $fecha);
    $stmt->execute();
    $stmt->close();

    // Si la condición es "Promoción", insertar en notas_mesas_promocionados
    if ($condicion === "Promocion") {
        $queryPromocion = "INSERT INTO notas_mesas_promocionados (alumno_legajo, materias_idMaterias, nota, fecha) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($queryPromocion);
        $stmt->bind_param("iiis", $alumno_legajo, $materia, $notaFinal, $fecha);
        $stmt->execute();
        $stmt->close();
    }

    // Si la condición es "Regular" y hay nota de examen final, insertar en nota_examen_final
    if ($condicion === "Regular" && !empty($notaExamenFinal)) {
        $queryExamenFinal = "INSERT INTO nota_examen_final (alumno_legajo, materias_idMaterias, nota, fecha) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($queryExamenFinal);
        $stmt->bind_param("iiis", $alumno_legajo, $materia, $notaExamenFinal, $fecha);
        $stmt->execute();
        $stmt->close();
    }

    echo "Nota guardada exitosamente.";
} else {
    echo "Error en la solicitud.";
}
?>
