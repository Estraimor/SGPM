<?php
include 'layout_estudiante.php';

$alumno_legajo = $_SESSION['id'];
$idCarrera = $_SESSION["idCarrera"];
$idCurso = $_SESSION["idCurso"];
$idComision = $_SESSION["idComision"];
$hoy = date("Y-m-d");
$recursarHabilitado = ($hoy >= date("Y") . "-03-01" && $hoy <= date("Y") . "-05-01") || true;

// =================== AÑO DE INGRESO ===================
$queryInscripcion = "
    SELECT MIN(año_inscripcion) as año_ingreso 
    FROM inscripcion_asignatura 
    WHERE alumno_legajo = ? ";
$stmt = $conexion->prepare($queryInscripcion);
$stmt->bind_param("i", $alumno_legajo);
$stmt->execute();
$result = $stmt->get_result();
$añoIngreso = $result->fetch_assoc()['año_ingreso'] ?? date("Y");
$añoActual = date("Y") - $añoIngreso + 1;
$stmt->close();

// =================== MATERIAS CURSADAS ===================
$queryUltimas = "
    SELECT m.idMaterias, m.Nombre
    FROM materias m
    INNER JOIN inscripcion_asignatura ia 
        ON ia.carreras_idCarrera = m.carreras_idCarrera AND ia.Cursos_idCursos = m.cursos_idCursos
    WHERE ia.alumno_legajo = ? AND m.carreras_idCarrera = ?
    ORDER BY ia.año_inscripcion DESC
";
$stmt = $conexion->prepare($queryUltimas);
$stmt->bind_param("ii", $alumno_legajo, $idCarrera);
$stmt->execute();
$result = $stmt->get_result();
$ultimaMatriculadaPorNombre = [];
while ($row = $result->fetch_assoc()) {
    $nombre = $row['Nombre'];
    $idMateria = $row['idMaterias'];
    if (!isset($ultimaMatriculadaPorNombre[$nombre])) {
        $ultimaMatriculadaPorNombre[$nombre] = $idMateria;
    }
}
$stmt->close();

// =================== TODAS LAS MATERIAS DE ESA COMISIÓN ===================
$queryMaterias = "
    SELECT m.idMaterias, m.Nombre, m.cursos_idCursos, m.comisiones_idComisiones 
    FROM materias m
    WHERE m.carreras_idCarrera = ? AND m.comisiones_idComisiones = ?
";
$stmt = $conexion->prepare($queryMaterias);
$stmt->bind_param("ii", $idCarrera, $idComision);
$stmt->execute();
$result = $stmt->get_result();
$materiasPorNivel = [];
$nombresMaterias = [];
$comisionesMaterias = [];
$yaMostradas = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['idMaterias'];
    $nombre = $row['Nombre'];
    $nivel = $row['cursos_idCursos'];
    $comision = $row['comisiones_idComisiones'];
    if (
        isset($ultimaMatriculadaPorNombre[$nombre]) && $ultimaMatriculadaPorNombre[$nombre] == $id ||
        !isset($yaMostradas[$nombre])
    ) {
        $materiasPorNivel[$nivel][$id] = [
            'nombre' => $nombre,
            'nivel' => $nivel,
            'comision' => $comision
        ];
        $nombresMaterias[$id] = $nombre;
        $comisionesMaterias[$id] = $comision;
        $yaMostradas[$nombre] = true;
    }
}
$stmt->close();

// =================== NOTAS Y CONDICIONES ===================
$queryCondiciones = "
    SELECT m.Nombre, n.condicion 
    FROM notas n 
    INNER JOIN materias m ON m.idMaterias = n.materias_idMaterias
    WHERE alumno_legajo = ?
    ORDER BY fecha DESC
";
$stmt = $conexion->prepare($queryCondiciones);
$stmt->bind_param("i", $alumno_legajo);
$stmt->execute();
$result = $stmt->get_result();
$condicionesMaterias = [];
while ($row = $result->fetch_assoc()) {
    $nombre = strtolower($row['Nombre']);
    if (!isset($condicionesMaterias[$nombre])) {
        $condicionesMaterias[$nombre] = $row['condicion'];
    }
}
$stmt->close();

$queryFinal = "
    SELECT m.Nombre, nf.nota 
    FROM nota_examen_final nf 
    INNER JOIN materias m ON m.idMaterias = nf.materias_idMaterias 
    WHERE nf.alumno_legajo = ? AND nf.nota >= 6
";
$stmt = $conexion->prepare($queryFinal);
$stmt->bind_param("i", $alumno_legajo);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $nombre = strtolower($row['Nombre']);
    $condicionesMaterias[$nombre] = 'promocion';
}
$stmt->close();

// =================== CORRELATIVIDADES ===================
$queryCorrel = "
    SELECT c.materias_idMaterias, c.materias_idMaterias1, c.tipo_correlatividad_idtipo_correlatividad, 
           m1.Nombre as nombre_materia, m2.Nombre as nombre_correlativa
    FROM correlatividades c
    INNER JOIN materias m1 ON m1.idMaterias = c.materias_idMaterias
    INNER JOIN materias m2 ON m2.idMaterias = c.materias_idMaterias1
";
$result = $conexion->query($queryCorrel);
$estadoMaterias = [];
$correlatividadesPendientes = [];
foreach ($result as $row) {
    $materia = $row['materias_idMaterias'];
    $nombreCorrelativa = strtolower($row['nombre_correlativa']);
    $tipo = $row['tipo_correlatividad_idtipo_correlatividad'];
    if (!isset($estadoMaterias[$materia])) $estadoMaterias[$materia] = 'habilitada';

    $tieneRegular = in_array(strtolower($condicionesMaterias[$nombreCorrelativa] ?? ''), ['regular', 'promocion']);
    $tienePromocion = strtolower($condicionesMaterias[$nombreCorrelativa] ?? '') === 'promocion';

    if ($tipo == 1 && !$tieneRegular) {
        $estadoMaterias[$materia] = 'bloqueada';
        $correlatividadesPendientes[$materia][] = "Debe regularizar: " . $row['nombre_correlativa'];
    }
    if ($tipo == 2 && !$tienePromocion) {
        $estadoMaterias[$materia] = 'bloqueada';
        $correlatividadesPendientes[$materia][] = "Debe aprobar: " . $row['nombre_correlativa'];
    }
}

// =================== MATRICULADAS ===================
$queryMatriculadas = "
    SELECT materias_idMaterias 
    FROM matriculacion_materias 
    WHERE alumno_legajo = ?
";
$stmt = $conexion->prepare($queryMatriculadas);
$stmt->bind_param("i", $alumno_legajo);
$stmt->execute();
$result = $stmt->get_result();
$materiasMatriculadas = [];
while ($row = $result->fetch_assoc()) {
    $materiasMatriculadas[$row['materias_idMaterias']] = true;
}
$stmt->close();

// =================== CURSANDO ===================
$queryCursando = "
    SELECT m.Nombre, m.comisiones_idComisiones 
    FROM matriculacion_materias mm 
    INNER JOIN materias m ON m.idMaterias = mm.materias_idMaterias 
    WHERE mm.alumno_legajo = ? AND YEAR(mm.año_matriculacion) = YEAR(CURDATE())
";
$stmt = $conexion->prepare($queryCursando);
$stmt->bind_param("i", $alumno_legajo);
$stmt->execute();
$result = $stmt->get_result();
$materiasCursandoNombre = [];
while ($row = $result->fetch_assoc()) {
    $materiasCursandoNombre[strtolower($row['Nombre'])] = $row['comisiones_idComisiones'];
}
$stmt->close();


// =================== RECURSABLES ===================
$materiasRecursables = [];
foreach ($materiasPorNivel as $nivel => $materiasNivel) {
    foreach ($materiasNivel as $idMateria => $datos) {
        $nombre = strtolower($datos['nombre']);
        $cond = strtolower($condicionesMaterias[$nombre] ?? '');
        if (
            (!isset($condicionesMaterias[$nombre]) || in_array($cond, ['recursa', 'abandono']))
            && strtolower($cond) !== 'promocion'
            && $recursarHabilitado
        ) {
            $materiasRecursables[$idMateria] = true;
        }
    }
}

// =================== PRIMER AÑO ===================
$aprobadasPrimerAño = false;
foreach ($materiasPorNivel[1] ?? [] as $id => $datos) {
    $nombre = strtolower($datos['nombre']);
    if (strtolower($condicionesMaterias[$nombre] ?? '') === 'promocion') {
        $aprobadasPrimerAño = true;
        break;
    }
}
?>




<div class="contenido">
    <style>
        .no-hover-fade tbody tr:hover td {
            color: inherit !important;
        }
        .table-success { background-color: #d4edda !important; }
        .table-warning { background-color: #fff3cd !important; }
        .table-danger { background-color: #f8d7da !important; }
        .recursando { background-color: #f3e5f5 !important; }
    </style>

    <h3>Inscripción A Unidades Curriculares</h3>
    <?php foreach ($materiasPorNivel as $nivel => $materias): ?>
        <h3 class="mt-4">Año <?= $nivel ?></h3>
        <table class="table table-bordered table-striped no-hover-fade">
            <thead>
                <tr>
                    <th>Unidades Curriculares</th>
                    <th>Nivel</th>
                    <th>Condición</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
<?php
$materiasMostradas = [];

foreach ($materias as $idMateria => $datos):
    $claseFila = '';
    $accion = '';
    $modalId = "modal_$idMateria";
    $nivel = $datos['nivel'];
    $nombreMateria = $datos['nombre'];
    $nombreKey = strtolower($nombreMateria);
    $idComisionMateria = $datos['comision'];

    if (isset($materiasMostradas[$nombreKey])) continue;
    $materiasMostradas[$nombreKey] = true;

    $condicionNotas = $condicionesMaterias[$nombreKey] ?? null;
    $condicionTexto = isset($condicionNotas) ? " (" . ucfirst($condicionNotas) . ")" : "";

    $yaRecursaOtra = false;
    $estaCursandoOtra = false;

    foreach ($materiasMatriculadas as $idMatriculada => $_) {
        if (
            isset($nombresMaterias[$idMatriculada]) &&
            strtolower($nombresMaterias[$idMatriculada]) === $nombreKey &&
            $idMatriculada != $idMateria
        ) {
            $yaRecursaOtra = true;
            break;
        }
    }

    if (isset($materiasCursandoNombre[$nombreKey])) {
        $claseFila = 'table-success';
        $condicion = 'Cursando actualmente';
        $accion = '<span class="badge badge-info">Cursando</span>';
    } elseif ($yaRecursaOtra || $estaCursandoOtra) {
        $claseFila = 'recursando';
        $condicion = 'Recursando en otra comisión';
        $accion = '<span class="badge badge-secondary">Recursando en otra comisión</span>';
    } elseif (isset($condicionNotas) && in_array(strtolower($condicionNotas), ['recursa', 'abandono'])) {
        $claseFila = 'recursando';
        $condicion = 'Recursa' . $condicionTexto;

        if (in_array($idCarrera, [27, 18])) {
            $opcionesComisiones = '';

            $stmtOtras = $conexion->prepare("
                SELECT m.idMaterias, m.Nombre, c.comision
                FROM materias m
                INNER JOIN comisiones c ON m.comisiones_idComisiones = c.idComisiones
                WHERE m.Nombre = ? AND m.carreras_idCarrera = ? AND m.idMaterias != ? AND m.comisiones_idComisiones != ?
            ");
            $stmtOtras->bind_param("siii", $nombreMateria, $idCarrera, $idMateria, $idComision);
            $stmtOtras->execute();
            $resultOtras = $stmtOtras->get_result();
            while ($fila = $resultOtras->fetch_assoc()) {
                $opcionesComisiones .= '<li>
                    <form method="POST" action="guardar_matriculacion.php">
                        <input type="hidden" name="materias_idMaterias" value="' . $fila['idMaterias'] . '">
                        <input type="hidden" name="materia_origen" value="' . $idMateria . '">
                        <button type="submit" class="btn btn-sm btn-outline-info">' . htmlspecialchars($fila['Nombre']) . ' (Comisión ' . htmlspecialchars($fila['comision']) . ')</button>
                    </form>
                </li>';
            }
            $stmtOtras->close();

            $opcionesComisiones .= '<li>
                <form method="POST" action="guardar_matriculacion.php">
                    <input type="hidden" name="materias_idMaterias" value="' . $idMateria . '">
                    <input type="hidden" name="materia_origen" value="' . $idMateria . '">
                    <input type="hidden" name="comision_actual" value="' . $idComisionMateria . '">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Recursar en comisión actual</button>
                </form>
            </li>';

            $modalIdRecursa = "modal_recursa_" . $idMateria;
            $accion = '
            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#' . $modalIdRecursa . '">Elegir comisión</button>
            <div class="modal fade" id="' . $modalIdRecursa . '" tabindex="-1" role="dialog">
              <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Elegí una comisión para recursar</h5>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body"><ul>' . $opcionesComisiones . '</ul></div>
              </div></div>
            </div>';
        } else {
            $accion = '
            <form method="POST" action="guardar_matriculacion.php">
                <input type="hidden" name="materias_idMaterias" value="' . $idMateria . '">
                <button type="submit" class="btn btn-outline-info btn-sm">Recursar</button>
            </form>';
        }

    } elseif (strtolower($condicionNotas) === 'promocion') {
        $claseFila = 'table-success';
        $condicion = 'Aprobada' . $condicionTexto;
        $accion = '<span class="badge badge-success">Aprobada</span>';

    } elseif (strtolower($condicionNotas) === 'regular') {
        $claseFila = 'table-success';
        $condicion = 'Aprobada (Regular)';
        $accion = '<span class="badge badge-success">Aprobada</span>';

    } elseif (strtolower($condicionNotas) === 'libre') {
        $claseFila = 'table-warning';
        $condicion = 'Cursada no aprobada (Libre)';
        $accion = '<span class="badge badge-warning">Cursada no aprobada</span>';

    } elseif (isset($estadoMaterias[$idMateria]) && $estadoMaterias[$idMateria] === 'bloqueada') {
        $claseFila = 'table-danger';
        $condicion = 'No habilitada (Falta correlativa)';
        $accion = '<button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#' . $modalId . '">No habilitada</button>
        <div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog">
          <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Correlatividades pendientes</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"><ul>';
        foreach ($correlatividadesPendientes[$idMateria] ?? [] as $falta) {
            $accion .= '<li>' . htmlspecialchars($falta) . '</li>';
        }
        $accion .= '</ul></div></div></div></div>';

    } elseif (
        $nivel <= $añoActual &&
        (
            $nivel == $añoActual ||
            ($nivel == 1 && $añoActual > 1) ||
            ($nivel == 2 && $añoActual == 1 && $aprobadasPrimerAño) ||
            ($nivel == 3 && $añoActual == 2 && $aprobadasPrimerAño)
        )
    ) {
        $claseFila = '';
        $condicion = 'Habilitada para cursar';
        $accion = '
        <form method="POST" action="guardar_matriculacion.php">
            <input type="hidden" name="materias_idMaterias" value="' . $idMateria . '">
            <button type="submit" class="btn btn-primary btn-sm">Inscribirse</button>
        </form>';

    } else {
        $claseFila = 'table-danger';
        $condicion = 'Año no habilitado';
        $accion = '<span class="badge badge-danger">No habilitada</span>';
    }

    echo "<tr class='$claseFila'>
        <td>" . htmlspecialchars($nombreMateria) . "</td>
        <td>$nivel</td>
        <td>$condicion</td>
        <td>$accion</td>
    </tr>";

endforeach;
?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>






<style>
  /* Variables de color */
  :root {
    --primary: #f3545d;
    --white: #ffffff;
    --light-gray: #f9f9f9;
    --danger: #ff4b5c;
  }

  /* Contenedor general */
  .contenido {
    padding: 20px;
  }

  /* Evita que el hover de tablas afecte al contenido del modal */
  .no-hover-fade tbody tr:hover td {
    color: inherit !important;
  }

  /* Tablas generales */
  table {
    width: 100%;
    max-width: 500px;
    margin: 20px 0;
    border-collapse: collapse;
  }
  th{
      color:white !important;
  }
  table, th, td {
    border: 1px solid #ddd;
  }
  table th, table td {
    padding: 12px 15px;
    text-align: left;
    color: #333333;
  }
  /* Encabezados de tabla */
  table th {
    background-color: var(--primary);
    color: var(--white);
    font-weight: bold;
    position: sticky;
    top: 0;
  }
  /* Filas alternas */
  table tbody tr:nth-child(even) {
    background-color: var(--light-gray);
  }
  /* Hover solo en tablas */
  table tbody tr:hover {
    background-color: var(--primary);
    color: var(--white);
    cursor: pointer;
  }

  /* Mensajes de error */
  .error-message {
    color: red;
    display: none;
    font-size: 14px;
    margin-top: 5px;
  }

  /* Botón genérico pequeño */
  .button_est {
    background-color: var(--danger);
    color: var(--white);
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease;
  }
  .button_est:hover {
    background-color: #e04350;
  }
  /* —— Unificar “No habilitada” —— */
.btn-danger,
.badge-danger {
  background-color: var(--primary) !important;
  color: var(--white) !important;
  border: none !important;
  padding: 0.375rem 0.75rem !important;
  font-size: 0.9em !important;
  border-radius: 0.25rem !important;
  display: inline-block;
  text-align: center;
  vertical-align: middle;
  cursor: default;
}

/* Opcional: si quieres que el cursor no cambie a mano cuando sea <span> */
.badge-danger {
  cursor: default !important;
}

  /* Contenedor de tabla estética */
  .table-box {
    max-width: 50%;
    padding: 20px;
    border-radius: 8px;
    background-color: rgba(255,255,255,0.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .titulo-unidad {
    font-size: 1.5em;
    font-weight: bold;
    color: var(--primary);
    text-align: center;
    margin-bottom: 20px;
  }

  /* Botón “dar de baja” */
  .button-dar-baja {
    background-color: var(--primary);
    color: var(--white);
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.1);
  }
  .button-dar-baja:hover {
    background-color: var(--white);
    color: var(--primary);
    box-shadow: 0px 4px 7px rgba(0, 0, 0, 0.15);
    transform: scale(1.03);
  }
  .button-dar-baja:active {
    transform: scale(0.95);
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
  }

  /* Botón de inscripción */
  .boton-inscripcion {
    background-color: var(--primary);
    color: var(--white);
    border: none;
    padding: 10px 20px;
    font-size: 1em;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  .boton-inscripcion:hover {
    background-color: #d3444c;
  }
  .boton-inscripcion:focus {
    outline: none;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
  }

  /* Estado “recursando” */
  .recursando {
    background-color: #e7d6f5 !important;
  }

  /* Responsividad */
  @media (max-width: 768px) {
    table th, table td {
      padding: 8px 10px;
      font-size: 0.9em;
    }
    .titulo-unidad {
      font-size: 1.2em;
    }
    .boton-inscripcion {
      font-size: 0.9em;
      padding: 8px 15px;
    }
    .table-box {
      max-width: 100%;
    }
  }

  /* —— Estilos para Bootstrap Modal —— */
  .modal-backdrop {
    z-index: 1040 !important;
    background-color: rgba(0, 0, 0, 0.5);
  }
  .modal {
    z-index: 1050 !important;
  }
  .modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
  }
  .modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100% - 3.5rem);
  }
  .modal-content {
    background-color: var(--white);
    color: #000000;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    border: none;
  }
  .modal-header {
    background-color: var(--primary);
    color: var(--white);
    border-bottom: none;
    padding: 1rem 1.5rem;
  }
  .modal-header .close {
    color: var(--white);
    opacity: 1;
  }
  .modal-header .close:hover {
    color: #e0e0e0;
  }
  .modal-body {
    padding: 1.5rem;
    color: #333333;
  }
  .modal-footer {
    padding: 1rem 1.5rem;
    border-top: none;
    background-color: var(--light-gray);
  }
  .modal-content .btn {
    background-color: var(--primary);
    color: var(--white);
    border: none;
    border-radius: 4px;
    padding: 0.5rem 1rem;
    transition: background-color 0.2s ease;
  }
  .modal-content .btn:hover {
    background-color: #d3444c;
  }
</style>




</body>
</html>

