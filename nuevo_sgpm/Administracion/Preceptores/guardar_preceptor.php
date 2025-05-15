<?php
// guardar_preceptor.php
include '../../conexion/conexion.php';
session_start();

// Sanitizar y castear
$carrera   = intval($_POST['carrera']);
$curso     = intval($_POST['curso']);
$comision  = intval($_POST['comision']);
$preceptor = intval($_POST['preceptor']);

// Nota: en tu tabla "preceptores" el campo de profesor aparece como
// `profesor_idProrfesor` (revisa si hay typo), aquí lo usamos tal cual.
$sql = "
  INSERT INTO preceptores
    (carreras_idCarrera, profesor_idProrfesor, cursos_idCursos, comisiones_idComisiones)
  VALUES
    ($carrera, $preceptor, $curso, $comision)
";

if (mysqli_query($conexion, $sql)) {
  $_SESSION['message'] = "🏷️ Preceptor asignado correctamente.";
} else {
  $_SESSION['error'] = "❌ Error al asignar preceptor: " . mysqli_error($conexion);
}

header("Location: asignar_preceptor.php");
exit;
