<?php
session_start();
include '../../../conexion/conexion.php';

$action = $_GET['action'] ?? '';
$idUsuario = $_SESSION['id'] ?? null;
$rolUsuario = $_SESSION['roles'] ?? null;

switch ($action) {
    case 'getCursos':
        $carrera = $_GET['carrera'] ?? null;
        if (!$carrera) exit;

        $query = "SELECT DISTINCT cu.idCursos, cu.curso 
                  FROM materias m 
                  INNER JOIN cursos cu ON m.cursos_idCursos = cu.idCursos 
                  WHERE m.carreras_idCarrera = '$carrera'";
        $res = mysqli_query($conexion, $query);

        echo '<option value="">Seleccione curso</option>';
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<option value='{$row['idCursos']}'>{$row['curso']}</option>";
        }
        break;

    case 'getComisiones':
        $carrera = $_GET['carrera'] ?? null;
        $curso = $_GET['curso'] ?? null;
        if (!$carrera || !$curso) exit;

        $query = "SELECT DISTINCT co.idComisiones, co.comision 
                  FROM materias m 
                  INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones 
                  WHERE m.carreras_idCarrera = '$carrera' AND m.cursos_idCursos = '$curso'";
        $res = mysqli_query($conexion, $query);

        echo '<option value="">Seleccione comisión</option>';
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<option value='{$row['idComisiones']}'>{$row['comision']}</option>";
        }
        break;

    case 'getMaterias':
    $carrera  = $_GET['carrera']   ?? null;
    $curso     = $_GET['curso']     ?? null;
    $comision  = $_GET['comision']  ?? null;
    $anio      = $_GET['anio']      ?? null;

    if (!$carrera || !$curso || !$comision || !$anio || !$idUsuario) {
        exit;
    }

    $whereExtra = ($rolUsuario == 1)
        ? ""
        : "AND m.profesor_idProrfesor = '$idUsuario'";

    $query = "
        SELECT
            m.idMaterias,
            m.Nombre AS materia,
            c.nombre_carrera,
            cu.curso,
            co.comision
        FROM materias m
        INNER JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
        INNER JOIN cursos cu   ON m.cursos_idCursos    = cu.idCursos
        INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones
        WHERE m.carreras_idCarrera    = '$carrera'
          AND m.cursos_idCursos        = '$curso'
          AND m.comisiones_idComisiones = '$comision'
          $whereExtra
    ";
    $res = mysqli_query($conexion, $query);

    // Abro el contenedor scrollable
    echo '<div class="table-responsive">';

    // Inicio de la tabla
    echo '<table class="tabla-materias">
            <thead>
              <tr>
                <th>Carrera</th>
                <th>Unidad Curricular</th>
                <th>Curso</th>
                <th>Comisión</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>';

    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                    <td>{$row['nombre_carrera']}</td>
                    <td>{$row['materia']}</td>
                    <td>{$row['curso']}</td>
                    <td>{$row['comision']}</td>
                    <td>
                      <a
                        href='gestion_notas.php?anio={$anio}&materiaId={$row['idMaterias']}'
                        class='btn-seleccionar'
                      >Seleccionar</a>
                    </td>
                  </tr>";
        }
    } else {
        echo '<tr><td colspan="5">No se encontraron materias asignadas.</td></tr>';
    }

    // Cierro tbody, tabla y contenedor
    echo '  </tbody>
          </table>
          </div>';
    break;
}
