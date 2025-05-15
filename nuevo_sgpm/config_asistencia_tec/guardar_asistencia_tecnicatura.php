<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Argentina/Buenos_Aires');

include '../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materiaPrimaria = isset($_POST['materiaSeleccionada']) ? intval($_POST['materiaSeleccionada']) : 0;
    $materiaPedagogica = isset($_POST['materiaPedagogica']) ? intval($_POST['materiaPedagogica']) : 0;
    $carrera  = isset($_POST['carrera'])  ? intval($_POST['carrera'])  : 0;
    $curso    = isset($_POST['curso'])    ? intval($_POST['curso'])    : 0;
    $comision = isset($_POST['comision']) ? intval($_POST['comision']) : 0;

    if ($materiaPrimaria <= 0 || !isset($_POST['asistencia']) || empty($_POST['asistencia'])) {
        echo "<script>
            alert('Debe seleccionar al menos una asistencia válida.');
            window.location.href = '../asistencia.php?carrera=$carrera&curso=$curso&comision=$comision';
        </script>";
        exit;
    }

    $fecha = date('Y-m-d');
    $materiasOrigen = $_POST['materia_origen'] ?? [];
    $asistencias = $_POST['asistencia'];

    $materiasUnicas = [$materiaPrimaria];
    if ($materiaPedagogica && $materiaPedagogica !== $materiaPrimaria) {
        $materiasUnicas[] = $materiaPedagogica;
    }

    $insertados = 0;
    $actualizados = 0;
    $justificadosNuevos = 0;
    $justificadosActualizados = 0;

    foreach ($asistencias as $legajo => $estado) {
        $estado = intval($estado);

        foreach ($materiasUnicas as $materia) {
            $check = "SELECT idasistencia, asistencia FROM asistencia 
                      WHERE alumno_legajo = $legajo 
                        AND materias_idMaterias = $materia 
                        AND fecha = '$fecha'";
            $res = mysqli_query($conexion, $check);

            if (mysqli_num_rows($res) > 0) {
                $fila = mysqli_fetch_assoc($res);
                if ($fila['asistencia'] != $estado) {
                    $update = "UPDATE asistencia 
                               SET asistencia = $estado 
                               WHERE idasistencia = {$fila['idasistencia']}";
                    mysqli_query($conexion, $update);
                    $actualizados++;
                }
            } else {
                $insert = "INSERT INTO asistencia (alumno_legajo, materias_idMaterias, fecha, asistencia) 
                           VALUES ($legajo, $materia, '$fecha', $estado)";
                mysqli_query($conexion, $insert);
                $insertados++;
            }

            if ($estado === 3) {
                $buscarMotivo = "SELECT Motivo FROM alumnos_justificados 
                                 WHERE inscripcion_asignatura_alumno_legajo = $legajo 
                                   AND fecha = '$fecha'
                                   AND Motivo IS NOT NULL
                                 ORDER BY idalumnos_justificados DESC LIMIT 1";
                $resMotivo = mysqli_query($conexion, $buscarMotivo);
                $motivo = null;

                if (mysqli_num_rows($resMotivo) > 0) {
                    $motivo = mysqli_fetch_assoc($resMotivo)['Motivo'];
                } else {
                    $motivo = $_POST['motivo'][$legajo] ?? null;
                    if ($motivo === "Otro") {
                        $motivo = $_POST['motivo_otro'][$legajo] ?? 'Otro';
                    }
                }

                $motivo = $motivo ? "'" . mysqli_real_escape_string($conexion, $motivo) . "'" : "NULL";

                $checkJust = "SELECT idalumnos_justificados FROM alumnos_justificados 
                              WHERE inscripcion_asignatura_alumno_legajo = $legajo 
                                AND materias_idMaterias = $materia 
                                AND fecha = '$fecha'";
                $resJust = mysqli_query($conexion, $checkJust);

                if (mysqli_num_rows($resJust) > 0) {
                    if ($motivo !== "NULL") {
                        $idJust = mysqli_fetch_assoc($resJust)['idalumnos_justificados'];
                        $updateJust = "UPDATE alumnos_justificados 
                                       SET Motivo = $motivo 
                                       WHERE idalumnos_justificados = $idJust";
                        mysqli_query($conexion, $updateJust);
                        $justificadosActualizados++;
                    }
                } else {
                    $insertJust = "INSERT INTO alumnos_justificados 
                                   (inscripcion_asignatura_alumno_legajo, materias_idMaterias, fecha, Motivo) 
                                   VALUES 
                                   ($legajo, $materia, '$fecha', $motivo)";
                    mysqli_query($conexion, $insertJust);
                    $justificadosNuevos++;
                }
            }
        }
    }

  $msg = "Asistencia registrada correctamente.\nInsertados: $insertados | Modificados: $actualizados";
if ($justificadosNuevos > 0 || $justificadosActualizados > 0) {
    $msg .= "\nJustificados nuevos: $justificadosNuevos | Actualizados: $justificadosActualizados";
}

$msgJS = json_encode(nl2br($msg)); // convierte saltos de línea y escapa comillas para JavaScript
$redirectURL = "../asistencia.php?carrera=$carrera&curso=$curso&comision=$comision";

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Asistencia registrada</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <script>
    Swal.fire({
      title: '¡Asistencia registrada!',
      html: <?= $msgJS ?>,
      icon: 'success',
      confirmButtonColor: '#f3545d',
      background: '#fff'
    }).then(() => {
      window.location.href = "<?= $redirectURL ?>";
    });
  </script>
</body>
</html>
<?php
exit;
} else {
    echo "<script>
        alert('Acceso inválido.');
        window.location.href = '../asistencia.php?carrera=27&curso=1&comision=1';
    </script>";
}
?>
