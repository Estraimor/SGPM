<?php
include '../../conexion/conexion.php';

if (isset($_POST['idCarrera']) && isset($_POST['idCurso']) && isset($_POST['idComision']) && isset($_SESSION['id'])) {
    $idCarrera = $_POST['idCarrera'];
    $idCurso = $_POST['idCurso'];
    $idComision = $_POST['idComision'];
    $preceptor_id = $_SESSION['id'];

    $stmt = $conexion->prepare("
        SELECT a.legajo, CONCAT(a.nombre_alumno, ' ', a.apellido_alumno) AS nombre_completo
        FROM alumno a
        JOIN inscripcion_asignatura i ON a.legajo = i.alumno_legajo
        JOIN preceptores p ON i.carreras_idCarrera = p.carreras_idCarrera
        WHERE i.carreras_idCarrera = ? AND i.cursos_idCursos = ? AND i.comisiones_idComisiones = ? AND p.idpreceptores = ?
        ORDER BY a.apellido_alumno, a.nombre_alumno
    ");
    $stmt->bind_param("iiii", $idCarrera, $idCurso, $idComision, $preceptor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $tabla = "<table border='1'>
                <tr>
                    <th>Legajo</th>
                    <th>Nombre</th>
                    <th>Materia</th>
                    <th>Nota Final</th>
                    <th>Condición</th>
                    <th>Acciones</th>
                </tr>";

    while ($row = $result->fetch_assoc()) {
        $legajo = $row['legajo'];
        $nombre = $row['nombre_completo'];

        // Obtener materias según carrera/curso/comisión
        $stmtMaterias = $conexion->prepare("
            SELECT m.idMaterias, m.Nombre 
            FROM materias m
            WHERE m.carreras_idCarrera = ? AND m.cursos_idCursos = ? AND m.comisiones_idComisiones = ?
        ");
        $stmtMaterias->bind_param("iii", $idCarrera, $idCurso, $idComision);
        $stmtMaterias->execute();
        $resultMaterias = $stmtMaterias->get_result();

        while ($materia = $resultMaterias->fetch_assoc()) {
            $idMateria = $materia['idMaterias'];
            $nombreMateria = $materia['Nombre'];

            $tabla .= "<tr>
                        <td>{$legajo}</td>
                        <td>{$nombre}</td>
                        <td>{$nombreMateria}</td>
                        <td><input type='number' step='0.01' class='nota_final' data-legajo='{$legajo}' data-materia='{$idMateria}'></td>
                        <td>
                            <select class='condicion' data-legajo='{$legajo}' data-materia='{$idMateria}'>
                                <option value='Regular'>Regular</option>
                                <option value='Promocion'>Promoción</option>
                            </select>
                        </td>
                        <td><button class='guardarNota' data-legajo='{$legajo}' data-materia='{$idMateria}'>Guardar</button></td>
                    </tr>";
        }

        $stmtMaterias->close();
    }

    $tabla .= "</table>";
    echo $tabla;

    $stmt->close();
}
?>
