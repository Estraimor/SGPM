<?php
include '../../conexion/conexion.php';
session_start();

if (isset($_POST['carrera']) && isset($_POST['curso']) && isset($_POST['comision']) && isset($_POST['anio'])) {
    $idCarrera = $_POST['carrera'];
    $idCurso = $_POST['curso'];
    $idComision = $_POST['comision'];
    $anio = $_POST['anio'];

    error_log("Carrera: $idCarrera, Curso: $idCurso, Comisión: $idComision, Año: $anio");

    $stmt = $conexion->prepare("
        SELECT idMaterias, Nombre
        FROM materias 
        WHERE carreras_idCarrera = ? 
        AND cursos_idCursos = ? 
        AND comisiones_idComisiones = ?
    ");

    if (!$stmt) {
        echo "<p>Error en la consulta SQL</p>";
        exit();
    }

    $stmt->bind_param("iii", $idCarrera, $idCurso, $idComision);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li><input type='checkbox' class='materiaCheckbox' value='{$row['idMaterias']}'> {$row['Nombre']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No hay materias disponibles para esta selección.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Error: Datos incompletos.</p>";
    error_log("POST Data: " . print_r($_POST, true));
}
?>
