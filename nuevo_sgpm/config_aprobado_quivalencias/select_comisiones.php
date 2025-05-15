<?php
include '../../conexion/conexion.php';
$curso = intval($_GET['curso']);
$legajo = intval($_GET['legajo']);

$sql = "SELECT * FROM comisiones ";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();

echo "<option value=''>Seleccionar...</option>";
while ($row = $res->fetch_assoc()) {
    echo "<option value='{$row['idComisiones']}'>{$row['comision']}</option>";
}
