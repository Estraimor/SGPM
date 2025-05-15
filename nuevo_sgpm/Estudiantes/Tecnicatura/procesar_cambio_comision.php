<?php
include '../../../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_comision'])) {
    $nuevo_id = intval($_POST['nueva_comision']);
    $legajo = intval($_POST['legajo']);
    $año_actual = date('Y');
    $fecha_actual = date('Y-m-d');

    // 1. Obtener curso y carrera del alumno en el año actual
    $sql_datos = "SELECT Cursos_idCursos, carreras_idCarrera 
                  FROM inscripcion_asignatura 
                  WHERE alumno_legajo = $legajo AND año_inscripcion = $año_actual
                  LIMIT 1";
    $res_datos = mysqli_query($conexion, $sql_datos);
    $row_datos = mysqli_fetch_assoc($res_datos);

    if (!$row_datos) {
        echo "Error: no se encontró inscripción del alumno en el año actual.";
        exit;
    }

    $idCurso = intval($row_datos['Cursos_idCursos']);
    $idCarrera = intval($row_datos['carreras_idCarrera']);

    // 2. Actualizar la comisión
    $sql_update = "UPDATE inscripcion_asignatura
                   SET Comisiones_idComisiones = $nuevo_id
                   WHERE alumno_legajo = $legajo 
                   AND año_inscripcion = $año_actual";
    mysqli_query($conexion, $sql_update);

    // 3. Obtener materias nuevas filtrando por carrera, curso y comisión
    $sql_materias = "SELECT idMaterias 
                     FROM materias 
                     WHERE carreras_idCarrera = $idCarrera 
                       AND cursos_idCursos = $idCurso 
                       AND comisiones_idComisiones = $nuevo_id";
    $res_materias = mysqli_query($conexion, $sql_materias);

    if ($res_materias && mysqli_num_rows($res_materias) > 0) {
        // 4. Borrar materias anteriores del año actual
        $sql_borrar = "DELETE FROM matriculacion_materias 
                       WHERE alumno_legajo = $legajo 
                       AND YEAR(año_matriculacion) = $año_actual";
        mysqli_query($conexion, $sql_borrar);

        // 5. Insertar nuevas materias
        while ($row = mysqli_fetch_assoc($res_materias)) {
            $idMateria = $row['idMaterias'];
            $sql_insert = "INSERT INTO matriculacion_materias (alumno_legajo, materias_idMaterias, año_matriculacion)
                           VALUES ($legajo, $idMateria, '$fecha_actual')";
            mysqli_query($conexion, $sql_insert);
        }

        // 6. Redirigir con mensaje de éxito
        header("Location: info_alumnoT.php?legajo=$legajo&exito=1");
        exit;
    } else {
        echo "No se encontraron materias en la nueva comisión.";
    }
} else {
    echo "Solicitud inválida.";
}
?>
