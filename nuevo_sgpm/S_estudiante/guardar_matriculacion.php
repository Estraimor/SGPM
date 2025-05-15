<?php 
session_start();
include '../../conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $alumno_legajo = $_SESSION['id'] ?? null;
    $materia_id = $_POST['materias_idMaterias'] ?? null;
    $materia_origen = $_POST['materia_origen'] ?? null;
    $fecha = date("Y-m-d");

    if ($alumno_legajo && $materia_id) {
        $queryExiste = "SELECT * FROM matriculacion_materias WHERE alumno_legajo = ? AND materias_idMaterias = ?";
        $stmt = $conexion->prepare($queryExiste);
        $stmt->bind_param("ii", $alumno_legajo, $materia_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            echo "<script>alert('Ya estás matriculado en esta materia.'); window.location.href='matricular_materia.php';</script>";
        } else {
            $stmt->close();
            $queryInsertar = "INSERT INTO matriculacion_materias (alumno_legajo, materias_idMaterias, año_matriculacion) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($queryInsertar);
            $stmt->bind_param("iis", $alumno_legajo, $materia_id, $fecha);

            if ($stmt->execute()) {
                echo "<script>alert('¡Matriculación guardada correctamente!'); window.location.href='matricular_materia.php';</script>";
            } else {
                echo "<script>alert('Error al guardar: " . $stmt->error . "'); window.location.href='matricular_materia.php';</script>";
            }
        }

        $stmt->close();
    } else {
        echo "<script>alert('Faltan datos obligatorios.'); window.location.href='matricular_materia.php';</script>";
    }
} else {
    echo "<script>alert('Acceso no válido.'); window.location.href='matricular_materia.php';</script>";
}
?>
