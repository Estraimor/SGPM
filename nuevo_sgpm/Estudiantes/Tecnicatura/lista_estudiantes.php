<?php
include '../../layout.php';
session_start();
?>
</head>
<body>
<div class="contenido">
<?php

$profesor_id = $_SESSION["id"];
$rolUsuario  = $_SESSION["roles"];

// --- 0) JOIN y WHERE para preceptores (igual que antes) ---
if ($rolUsuario != 1) {
    $joinPre = "
        INNER JOIN preceptores p
          ON p.carreras_idCarrera      = m.carreras_idCarrera
         AND p.Cursos_idCursos         = m.Cursos_idCursos
         AND p.Comisiones_idComisiones = m.Comisiones_idComisiones
    ";
    $wherePre = "AND p.profesor_idProrfesor = $profesor_id";
} else {
    $joinPre  = "";
    $wherePre = "";
}

// --- 1) Consulta de estudiantes SOLO PARA LA TABLA DE ABAJO ---
if ($rolUsuario == 1) {
    // Mostrar todos los alumnos (sin importar si están matriculados), y la carrera si existe
    $sqlEst = "
        SELECT a.*, c.nombre_carrera
        FROM alumno a
        LEFT JOIN matriculacion_materias mm ON mm.alumno_legajo = a.legajo
        LEFT JOIN materias m ON mm.materias_idMaterias = m.idMaterias
        LEFT JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
        WHERE a.estado = '1'
        GROUP BY a.legajo
    ";
} else {
    // Para preceptores: ver solo los alumnos en materias asignadas al preceptor
    $sqlEst = "
        SELECT DISTINCT a.*, c.nombre_carrera
        FROM matriculacion_materias mm
        INNER JOIN alumno a ON mm.alumno_legajo = a.legajo AND a.estado = '1'
        INNER JOIN materias m ON mm.materias_idMaterias = m.idMaterias
        $joinPre
        INNER JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
        WHERE 1 $wherePre
        GROUP BY a.legajo
    ";
}
$qEst = mysqli_query($conexion, $sqlEst);
$estudiantes = [];
while ($r = mysqli_fetch_assoc($qEst)) {
    $estudiantes[] = $r;
}

// --- 2) Contadores por carrera/curso/comisión (sin sumar aquí el total) ---
$sqlCnt = "
    SELECT
      c.nombre_carrera,
      cu.curso    AS curso,
      co.comision AS comision,
      COUNT(DISTINCT mm.alumno_legajo) AS cantidad
    FROM matriculacion_materias mm
    INNER JOIN alumno     a  ON mm.alumno_legajo       = a.legajo
                              AND a.estado               = '1'
    INNER JOIN materias   m  ON mm.materias_idMaterias = m.idMaterias
    INNER JOIN carreras   c  ON m.carreras_idCarrera   = c.idCarrera
    INNER JOIN cursos     cu ON m.Cursos_idCursos      = cu.idCursos
    INNER JOIN comisiones co ON m.Comisiones_idComisiones = co.idComisiones
    $joinPre
    WHERE 1 $wherePre
    GROUP BY c.nombre_carrera, cu.curso, co.comision
";
$qCnt = mysqli_query($conexion, $sqlCnt);

$counts = [];
while ($row = mysqli_fetch_assoc($qCnt)) {
    $carrera  = $row['nombre_carrera'];
    $curso     = $row['curso'];
    $comision = $row['comision'];
    $cant     = $row['cantidad'];

    if (!isset($counts[$carrera])) {
        $counts[$carrera] = [
            'total'    => 0,
            'detalles' => []
        ];
    }
    // Sumar sólo dentro de cada bloque
    $counts[$carrera]['total']     += $cant;
    $counts[$carrera]['detalles'][] = [
        'curso'    => $curso,
        'comision' => $comision,
        'cantidad' => $cant
    ];
}

// --- 3) Calcular total global a partir de alumnos únicos ---  
//    Esto garantiza que coincida exactamente con count($estudiantes)
// --- Total global basado solo en matriculacion_materias ---
$sqlTotal = "SELECT COUNT(DISTINCT alumno_legajo) AS total FROM matriculacion_materias";
$resTotal = mysqli_query($conexion, $sqlTotal);
$rowTotal = mysqli_fetch_assoc($resTotal);
$totalEstudiantes = $rowTotal['total'];

?>
  <!-- Contadores discriminados por carrera, curso y comisión -->
  <div class="contadores">
    <?php foreach ($counts as $carrera => $data): ?>
      <div class="carrera-block">
        <p class="carrera-title"><?= htmlspecialchars($carrera) ?>: <?= $data['total'] ?></p>
        <?php foreach ($data['detalles'] as $det): ?>
          <p class="detalle">
            <?= " {$det['curso']} año - {$det['comision']}: {$det['cantidad']}" ?>
          </p>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <div class="carrera-block total-global">
      <p class="carrera-title">Total estudiantes: <?= $totalEstudiantes ?></p>
    </div>
  </div>

  <!-- Tabla de estudiantes -->
  <div id="tablaContainerEstudiantes">
    <table id="tabla">
      <thead>
        <tr>
          <th class="legajo">Legajo</th>
          <th class="apellido">Apellido</th>
          <th class="nombre">Nombre</th>
          <th class="dni">DNI</th>
          <th class="celular">Celular</th>
          <th class="carrera">Carrera</th>
          <th class="acciones">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($estudiantes as $datos): ?>
          <tr>
            <td><?= $datos['legajo'] ?></td>
            <td><?= $datos['apellido_alumno'] ?></td>
            <td><?= $datos['nombre_alumno'] ?></td>
            <td><?= $datos['dni_alumno'] ?></td>
            <td><?= $datos['celular'] ?></td>
            <td><?= $datos['nombre_carrera'] ?></td>
            <td>
              <a href="./ABM_estudiante/modificar_estudianteT.php?legajo=<?= $datos['legajo'] ?>"
                 class="modificar-button"><i class="fas fa-pencil-alt"></i></a>
              <a href="#" onclick="return confirmarBorrado('<?= $datos['legajo'] ?>')"
                 class="borrar-button"><i class="fas fa-trash-alt"></i></a>
              <a href="info_alumnoT.php?legajo=<?= $datos['legajo'] ?>"
                 class="accion-button"><i class="fas fa-exclamation"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Botón exportar a Excel -->
  <form action="exel_alumnos.php" method="POST">
    <button type="submit">Descargar Excel Alumnos</button>
  </form>
</div>

<!-- Estilos -->
<style>
.contadores {
  display: flex;
  flex-wrap: wrap;
  margin-bottom: 20px;
  padding: 10px;
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 5px;
}
.carrera-block {
  margin-right: 30px;
}
.carrera-title {
  margin: 0;
  font-size: 16px;
  font-weight: bold;
  color: #f3545d;
}
.detalle {
  margin: 2px 0 0 10px;
  font-size: 12px;
  color: #333;
}
.total-global .carrera-title {
  color: #333;
}
</style>

<!-- Script -->
<script>
  var dataTable = new DataTable(document.querySelector('#tabla'));
  function confirmarBorrado(legajo) {
    if (confirm("¿Estás seguro de que quieres borrar este alumno?")) {
      window.location.href = "./ABM_estudiante/Borrado_logico_alumno.php?legajo=" + legajo;
    }
    return false;
  }
</script>
</body>
</html>
