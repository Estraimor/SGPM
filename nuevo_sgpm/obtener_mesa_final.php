<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../conexion/conexion.php';

$idMesa = $_POST['idMesa'];

$query = "SELECT t.idtandas AS idfechas_mesas_finales, t.fecha, t.llamado, t.tanda, t.cupo 
          FROM fechas_mesas_finales fmf
          JOIN tandas t ON fmf.tandas_idtandas = t.idtandas
          WHERE fmf.idfechas_mesas_finales = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $idMesa);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>
