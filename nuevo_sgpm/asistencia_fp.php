<?php
include './layout.php';

$excluidas = [18, 27, 55, 46];
$año_actual = date('Y');

// Traer carreras válidas
$sql = "SELECT DISTINCT i.carreras_idCarrera, c.nombre_carrera
        FROM inscripcion_fp i
        INNER JOIN carreras c ON c.idCarrera = i.carreras_idCarrera
        WHERE c.idCarrera NOT IN (18,27,55,46)
        ORDER BY c.nombre_carrera";
$query = mysqli_query($conexion, $sql);
$carreras = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('./fondo.jpg') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      padding: 0;
    }

    .contenido {
      max-width: 900px;
      margin: 40px auto;
      padding: 30px;
      background: rgba(255,255,255,0.95);
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      color: #f3545d;
      text-align: center;
      margin-bottom: 25px;
    }

    .filtro-materia label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
      color: #444;
    }

    .filtro-materia select {
      width: 100%;
      padding: 10px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background: #f3545d;
      color: white;
      font-weight: 600;
    }

    tr:nth-child(even) {
      background-color: #fdf2f2;
    }

    .boton-enviar {
      background: #f3545d;
      color: white;
      border: none;
      padding: 12px 20px;
      font-size: 1rem;
      border-radius: 6px;
      cursor: pointer;
      display: block;
      margin: 20px auto 0;
    }

    .boton-enviar:hover {
      background: #d6383e;
    }
  </style>

<div class="contenido">
  <h2>Registro de Asistencia FP</h2>

  <div class="filtro-materia">
    <label for="select-carrera">Seleccionar Carrera:</label>
    <select id="select-carrera">
      <option value="">-- Elegí una carrera --</option>
      <?php foreach ($carreras as $c): ?>
        <option value="<?= $c['carreras_idCarrera'] ?>">
          <?= htmlspecialchars($c['nombre_carrera']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <form method="post" action="config_asistencia_fp/guardar_asistencia_fp.php">
    <input type="hidden" name="carrera" id="carreraHidden">
    <div class="table-container">
      <table id="tabla-asistencia" style="display:none;">
        <thead>
          <tr>
            <th>N°</th>
            <th>Legajo</th>
            <th>Apellido</th>
            <th>Nombre</th>
            <th>Presente</th>
            <th>Ausente</th>
          </tr>
        </thead>
        <tbody id="alumnosContainer"></tbody>
      </table>
    </div>
    <button type="submit" class="boton-enviar" style="display:none;" id="btn-confirmar">Confirmar Asistencia</button>
  </form>
</div>

<script>
$(document).ready(function () {
  $('#select-carrera').on('change', function () {
    const carreraId = $(this).val();
    $('#alumnosContainer').empty();
    $('#tabla-asistencia, #btn-confirmar').hide();

    if (!carreraId) return;

    $('#carreraHidden').val(carreraId);

    $.ajax({
      url: './config_asistencia_fp/get_alumnos_fp.php',
      method: 'GET',
      data: { carrera: carreraId },
      success: function (html) {
        $('#alumnosContainer').html(html);
        $('#tabla-asistencia, #btn-confirmar').show();
      },
      error: function () {
        $('#alumnosContainer').html('<tr><td colspan="6">Error al cargar estudiantes.</td></tr>');
      }
    });
  });
});
</script>
