<?php
$conexion = mysqli_connect('localhost', 'u756746073_root', 'POLITECNICOmisiones2023.', 'u756746073_politecnico', '3306');
header('Content-Type: application/json');

$idCarrera  = intval($_GET['idCarrera'] ?? 0);
$idCurso    = intval($_GET['idCurso'] ?? 0);
$idComision = intval($_GET['idComision'] ?? 0);

$materias = [];

if ($idCarrera && $idCurso && $idComision) {
    $sql = "SELECT idMaterias, Nombre 
            FROM materias 
            WHERE carreras_idCarrera = ? 
              AND cursos_idCursos = ? 
              AND comisiones_idComisiones = ? 
              AND estado = 1
            ORDER BY Nombre";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $idCarrera, $idCurso, $idComision);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    while ($fila = mysqli_fetch_assoc($resultado)) {
        $materias[] = $fila;
    }
}

echo json_encode($materias);