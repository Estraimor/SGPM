<?php include './layout.php'; ?>
</head>
<body>
<div class="contenido">
<?php

$sql = "
SELECT 
    a.legajo_afp, a.apellido_afp, a.nombre_afp, a.dni_afp, a.celular_afp,alumno_legajo,
    a.carreras_idCarrera, c1.nombre_carrera AS carrera_1,
    a.carreras_idCarrera1, c2.nombre_carrera AS carrera_2,
    a.carreras_idCarrera2, c3.nombre_carrera AS carrera_3,
    a.carreras_idCarrera3, c4.nombre_carrera AS carrera_4
FROM alumnos_fp a
LEFT JOIN carreras c1 ON a.carreras_idCarrera = c1.idCarrera
LEFT JOIN carreras c2 ON a.carreras_idCarrera1 = c2.idCarrera
LEFT JOIN carreras c3 ON a.carreras_idCarrera2 = c3.idCarrera
LEFT JOIN carreras c4 ON a.carreras_idCarrera3 = c4.idCarrera
";

$query = mysqli_query($conexion, $sql);

$contadorProgramacion = 0;
$contadorProgramacionWeb = 0;
$contadorMarketing = 0;
$contadorRedes = 0;
$totalEstudiantes = 0;
$estudiantes = [];

while ($datos = mysqli_fetch_assoc($query)) {
    // Si tiene vinculado un legajo de alumno tecnicatura
    if (!empty($datos['alumno_legajo'])) {
        $legajo_tec = intval($datos['alumno_legajo']);
        $stmt = $conexion->prepare("SELECT nombre_alumno, apellido_alumno, dni_alumno, celular FROM alumno WHERE legajo = ?");
        $stmt->bind_param("i", $legajo_tec);
        $stmt->execute();
        $resAlumno = $stmt->get_result();
        if ($alumno = $resAlumno->fetch_assoc()) {
            $datos['nombre_afp'] = $alumno['nombre_alumno'];
            $datos['apellido_afp'] = $alumno['apellido_alumno'];
            $datos['dni_afp'] = $alumno['dni_alumno'];
            $datos['celular_afp'] = $alumno['celular'];
        }
        $stmt->close();
    }

    $estudiantes[] = $datos;

    // Contadores de carreras
    $carreras = [
        intval($datos['carreras_idCarrera']),
        intval($datos['carreras_idCarrera1']),
        intval($datos['carreras_idCarrera2']),
        intval($datos['carreras_idCarrera3']),
    ];

    if (in_array(64, $carreras)) $contadorProgramacion++;
    if (in_array(8, $carreras))  $contadorProgramacionWeb++;
    if (in_array(14, $carreras)) $contadorMarketing++;
    if (in_array(15, $carreras)) $contadorRedes++;

    $totalEstudiantes++;
}

?>
  <div class="contadores">
    <p>Programación: <?= $contadorProgramacion; ?></p>
    <p>Programación Web: <?= $contadorProgramacionWeb; ?></p>
    <p>Marketing y Venta Digital: <?= $contadorMarketing; ?></p>
    <p>Redes Informáticas: <?= $contadorRedes; ?></p>
    <p>Total estudiantes: <?= $totalEstudiantes; ?></p>
</div>

  <div id="tablaContainerEstudiantes">
    <table id="tabla">
      <thead>
        <tr>
          <th class="legajo">Legajo</th>
          <th class="apellido">Apellido</th>
          <th class="nombre">Nombre</th>
          <th class="dni">DNI</th>
          <th class="celular">Celular</th>
          <th class="acciones">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($estudiantes as $datos): ?>
          <tr>
            <td><?= $datos['legajo_afp']; ?></td>
            <td><?= $datos['apellido_afp']; ?></td>
            <td><?= $datos['nombre_afp']; ?></td>
            <td><?= $datos['dni_afp']; ?></td>
            <td><?= $datos['celular_afp']; ?></td>
            <td>
              <a href="ver_detalles_fp.php?legajo=<?= $datos['legajo_afp']; ?>" class="btn btn-info btn-sm">Ver detalles</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <form action="exel_alumnos.php" method="POST">
    <button type="submit">Descargar Excel Alumnos</button>
  </form>
</div>

<style>
.contadores {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 10px;
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 5px;
}
.contadores p {
    margin: 0 15px;
    padding: 5px;
    font-size: 14px;
    color: #f3545d;
    font-weight: bold;
}
</style>

<script>
var myTable = document.querySelector("#tabla");
var dataTable = new DataTable(myTable); // corrección: usar variable correcta

function confirmarBorrado(legajo) {
    var respuesta = confirm("¿Estás seguro de que quieres borrar este alumno?");
    if (respuesta) {
        window.location.href = "./ABM_estudiante/Borrado_logico_alumno.php?legajo=" + legajo;
    }
    return false;
}
</script>
</body>
</html>
