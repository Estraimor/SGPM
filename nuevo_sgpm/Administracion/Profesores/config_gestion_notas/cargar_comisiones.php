<?php
include "conexion.php";
$cursoId = $_GET['cursoId'];
$query = "SELECT idComision, nombreComision FROM comisiones WHERE idCurso = '$cursoId'";
$result = mysqli_query($conexion, $query);

$comisiones = [];
while ($row = mysqli_fetch_assoc($result)) {
    $comisiones[] = $row;
}
echo json_encode($comisiones);
?>
