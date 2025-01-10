<?php
include '../../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $legajos = $_POST['legajo'];
    $notas = $_POST['nota'];
    $tomos = $_POST['tomo'];
    $folios = $_POST['folio'];
    $idCarrera = $_POST['comision']; // Capturamos idCarrera
    $idMateria = $_POST['materia'];  // Capturamos idMateria
    $fecha_actual = date("Y-m-d");   // Fecha actual

    foreach ($legajos as $i => $legajo) {
        $nota = $notas[$i];
        $tomo = !empty($tomos[$i]) ? $tomos[$i] : NULL;
        $folio = !empty($folios[$i]) ? $folios[$i] : NULL;

        // Procesar solo si al menos uno de los dos campos está presente
        if ($tomo !== NULL || $folio !== NULL) {
            // Verificar si ya existe el registro
            $checkQuery = "SELECT idnotas_mesas_promocionados FROM notas_mesas_promocionados 
                           WHERE alumno_legajo = ? AND materias_idMaterias = ?";
            $stmtCheck = $conexion->prepare($checkQuery);
            $stmtCheck->bind_param("ii", $legajo, $idMateria);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();

            if ($resultCheck->num_rows > 0) {
                // Si existe, realizar un UPDATE
                $updateFields = [];
                $params = [];
                $types = "";

                if ($tomo !== NULL) {
                    $updateFields[] = "tomo = ?";
                    $params[] = $tomo;
                    $types .= "i";
                }
                if ($folio !== NULL) {
                    $updateFields[] = "folio = ?";
                    $params[] = $folio;
                    $types .= "i";
                }

                $updateFields[] = "fecha = ?";
                $params[] = $fecha_actual;
                $types .= "s";

                $updateQuery = "UPDATE notas_mesas_promocionados SET " . implode(", ", $updateFields) . "
                                WHERE alumno_legajo = ? AND materias_idMaterias = ?";
                $params[] = $legajo;
                $params[] = $idMateria;
                $types .= "ii";

                $stmtUpdate = $conexion->prepare($updateQuery);
                $stmtUpdate->bind_param($types, ...$params);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            } else {
                // Si no existe, realizar el INSERT
                $insertQuery = "INSERT INTO notas_mesas_promocionados 
                                (alumno_legajo, materias_idMaterias, nota, tomo, folio, fecha)
                                VALUES (?, ?, ?, ?, ?, ?)";
                $stmtInsert = $conexion->prepare($insertQuery);
                $stmtInsert->bind_param("iiddss", $legajo, $idMateria, $nota, $tomo, $folio, $fecha_actual);
                $stmtInsert->execute();
                $stmtInsert->close();
            }

            $stmtCheck->close(); // Cerrar el statement de verificación
        }
    }

    // Mostrar un alert y redirigir
    echo "<script>
            alert('Registro actualizado exitosamente.');
            window.location.href = './lista_promocionados.php?comision=$idCarrera&materia=$idMateria';
          </script>";
    exit();
} else {
    echo "Acceso denegado.";
}
?>
