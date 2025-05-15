<?php
include './layout.php';

$carreraId  = isset($_GET['carrera'])   ? intval($_GET['carrera'])   : 0;
$cursoId    = isset($_GET['curso'])     ? intval($_GET['curso'])     : 0;
$comisionId = isset($_GET['comision'])  ? intval($_GET['comision'])  : 0;

if ($carreraId <= 0 || $cursoId <= 0 || $comisionId <= 0) {
    die('Parámetros inválidos.');
}

$sql_materias = "
  SELECT m.idMaterias, m.Nombre 
  FROM materias m
  WHERE m.carreras_idCarrera      = $carreraId
    AND m.cursos_idCursos         = $cursoId
    AND m.comisiones_idComisiones = $comisionId
  ORDER BY m.Nombre ASC
";
$query_materias = mysqli_query($conexion, $sql_materias);
$materias       = mysqli_fetch_all($query_materias, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Asistencia</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
   .table-comision-a th:nth-child(4),
.table-comision-a td:nth-child(4) {
  display: none;
}

/* Fuente general del cuerpo */
body {
  font-family: 'Poppins', sans-serif;
}

/* QUITA apariencia nativa */
input[type="radio"],
input[type="checkbox"] {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  margin: 0;
  cursor: pointer;
  position: relative;
}

/* RADIO BUTTONS */
input[type="radio"] {
  width: 18px;
  height: 18px;
  border: 2px solid #f3545d;
  border-radius: 50%;
  outline: none;
  transition: background 0.2s, border-color 0.2s;
  vertical-align: middle;
}
input[type="radio"]::after {
  content: "";
  width: 10px;
  height: 10px;
  background: transparent;
  border-radius: 50%;
  position: absolute;
  top: 2px;
  left: 2px;
  transition: background 0.2s;
}
input[type="radio"]:checked {
  border-color: #f3545d;
}
input[type="radio"]:checked::after {
  background: #f3545d;
}

/* CHECKBOXES */
input[type="checkbox"] {
  width: 18px;
  height: 18px;
  border: 2px solid #f3545d;
  border-radius: 4px;
  outline: none;
  transition: background 0.2s, border-color 0.2s;
  vertical-align: middle;
}
input[type="checkbox"]::after {
  content: "";
  font-size: 14px;
  color: #f3545d;
  position: absolute;
  top: -1px;
  left: 2px;
  opacity: 0;
  transition: opacity 0.2s;
}
input[type="checkbox"]:checked {
  background: #f3545d;
  border-color: #f3545d;
}
input[type="checkbox"]:checked::after {
  content: "✓";
  opacity: 1;
}

/* Acomoda el label para que no sepamos */
label {
  cursor: pointer;
  user-select: none;
}

/* Espacio entre input y texto */
label input {
  margin-right: 8px;
}

/* Encabezados de la tabla */
.table-comision-a th {
 
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: 0.5px;
}

/* Celdas y demás */
.table-comision-a td {
  font-family: 'Poppins', sans-serif;
  font-weight: 300;
  font-size: 0.95rem;
}
/* Contenedor de la tabla: fondo blanco y sombra suave */
.table-container {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  overflow-x: auto;
  overflow-y: visible;
  margin-bottom: 20px;
}

/* Quitar bordes laterales y dejar solo línea inferior sutil */
.table-comision-a {
  border: none;
  min-width: 600px;
}
.table-comision-a th,
.table-comision-a td {
  border: none;
  border-bottom: 1px solid #f3545d33; /* 20% de opacidad para suavizar */
  padding: 12px 15px;
  vertical-align: middle;
}

/* Encabezados más redondeados y con sombra interior */
.table-comision-a th {
  background: #f3545d;
  color: #fff;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  position: sticky;
  top: 0;
  box-shadow: inset 0 -3px 5px rgba(0,0,0,0.1);
}

/* Zebra stripes suaves */
.table-comision-a tbody tr:nth-child(even) td {
  background: #fff5f5;
}

/* Hover sobre fila */
.table-comision-a tbody tr:hover td {
  background: rgba(243,84,93,0.1);
  transition: background 0.2s;
}

/* Primera columna (N°) destacado */
.table-comision-a td:nth-child(1) {
  font-weight: 500;
  color: #f3545d;
  text-align: center;
  width: 50px;
}

/* Ajuste de texto en celdas de opciones */
.table-comision-a td:nth-child(4),
.table-comision-a td:nth-child(5),
.table-comision-a td:nth-child(6) {
  text-align: center;
  white-space: nowrap;
}

/* Botón Confirmar centrado y con sombra */
.boton-enviar {
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

    .contenido {
      max-width:1000px;
      margin:20px auto;
      padding:20px;
      background:#fff;
      border-radius:8px;
      box-shadow:0 4px 12px rgba(0,0,0,0.1);
      overflow:visible;
    }
    #reloj-buenos-aires {
      text-align:center;
      margin-bottom:20px;
      font-weight:bold;
      font-size:18px;
      color:#f3545d;
    }
    .filtro-materia {
      display:flex;
      align-items:center;
      gap:10px;
      margin-bottom:15px;
    }
    .filtro-materia label {
      font-weight:bold;
      color:#f3545d;
    }
    .filtro-materia select {
      flex:1;
      padding:8px 10px;
      border:1px solid #f3545d;
      border-radius:5px;
      background:#fff;
      font-size:1rem;
    }
    .options-container {
      display:flex;
      justify-content:flex-end;
      gap:20px;
      margin-bottom:10px;
    }
    .options-container label {
      display:flex;
      align-items:center;
      gap:6px;
      font-weight:bold;
      color:#5a5a5a;
    }
    .options-container input { transform:scale(1.2); }
    .table-container {
      overflow-x:auto;
      overflow-y:visible;
      margin-bottom:20px;
    }
    .table-comision-a {
      width:100%;
      border-collapse:collapse;
      min-width:700px; /* ajusta a 7 columnas */
    }
    .table-comision-a th,
    .table-comision-a td {
      border:1px solid #f3545d;
      padding:8px 10px;
      text-align:left;
    }
    .table-comision-a th {
      background:#f3545d;
      color:#fff;
      position:sticky;
      top:0;
    }
    .table-comision-a tr:nth-child(even) {
      background:#fafafa;
    }
    .boton-enviar {
      display:block;
      width:100%;
      max-width:250px;
      margin:0 auto 15px;
      padding:12px;
      font-size:1rem;
      color:#fff;
      background:#f3545d;
      border:none;
      border-radius:5px;
      cursor:pointer;
      transition:background .2s;
    }
    .boton-enviar:hover {
      background:#d13c42;
    }
    .modal-justificacion {
      position:absolute;
      background:#fff;
      border:1px solid #f3545d;
      padding:8px;
      z-index:100000;
      box-shadow:0 2px 5px rgba(0,0,0,0.2);
      width:200px;
      font-size:13px;
      border-radius:8px;
      display:none;
    }
    .modal-justificacion button {
      font-size:12px;
      padding:2px 8px;
      margin:5px 5px 0 0;
      border:1px solid #f3545d;
      background:#fff;
      border-radius:4px;
      cursor:pointer;
    }
    .modal-justificacion button:hover {
      background:#f3545d;
      color:#fff;
    }
    .modal-justificacion label {
      display:block;
      margin-bottom:3px;
    }
    @media (max-width:600px){
      .filtro-materia,
      .options-container {
        flex-direction:column;
        align-items:stretch;
      }
      .table-comision-a {
        min-width:0;
      }
    }
  </style>
  
</head>
<body>
  <div class="contenido">
  <div id="reloj-buenos-aires"></div>

  <form id="formAsistencia"
        action="./config_asistencia_tec/guardar_asistencia_tecnicatura.php"
        method="post">

    <!-- 1) Select de Materia -->
    <div class="filtro-materia">
      <label for="materiaPrimera">Materia:</label>
      <select id="materiaPrimera" name="materiaSeleccionada" required>
        <option value="" disabled selected>Seleccionar Materia</option>
        <?php foreach ($materias as $m): ?>
          <option value="<?= $m['idMaterias'] ?>">
            <?= htmlspecialchars($m['Nombre']) ?>
          </option>
        <?php endforeach ?>
      </select>
    </div>

    <!-- Checkbox para habilitar segundo select -->
    <div class="filtro-materia" style="margin-top: 10px;">
      <label><input type="checkbox" id="habilitarSegundaMateria"> Agregar materia pedagógica</label>
    </div>

    <!-- Segundo select oculto por defecto -->
    <div class="filtro-materia" id="contenedorSegundaMateria" style="display:none;">
      <label for="materiaSegunda">Materia Pedagógica:</label>
      <select id="materiaSegunda">
        <option value="" disabled selected>Seleccionar Materia</option>
        <?php foreach ($materias as $m): ?>
          <option value="<?= $m['idMaterias'] ?>">
            <?= htmlspecialchars($m['Nombre']) ?>
          </option>
        <?php endforeach ?>
      </select>
    </div>

    <!-- NUEVO campo oculto para enviar al backend -->
    <input type="hidden" id="materiaPedagogicaInput" name="materiaPedagogica" value="">

    <!-- 2) Checkboxes “Todos” -->
    <div class="options-container">
      <label><input type="checkbox" id="marcarTodosPresente"> Todos presentes</label>
      <label><input type="checkbox" id="marcarTodosAusente"> Todos ausentes</label>
    </div>

    <!-- 3) Tabla de Alumnos -->
    <div class="table-container">
      <table class="table-comision-a">
        <thead>
          <tr>
            <th>N°</th>
            <th>Apellido</th>
            <th>Nombre</th>
            <th></th>
            <th>Presente</th>
            <th>Ausente</th>
            <th>Ausente Justificado</th>
          </tr>
        </thead>
        <tbody id="alumnosContainer">
          <!-- get_alumnos.php inyecta aquí -->
        </tbody>
      </table>
    </div>

    <input type="hidden" name="carrera"  value="<?= $carreraId ?>">
    <input type="hidden" name="curso"    value="<?= $cursoId ?>">
    <input type="hidden" name="comision" value="<?= $comisionId ?>">
    <button type="submit" class="boton-enviar">Confirmar</button>
  </form>
</div>

<script>
let legajosYaListados = [];
let legajosSegundaMateria = [];

function actualizarReloj() {
  const ahora = new Date().toLocaleString("es-AR", {
    timeZone: "America/Argentina/Buenos_Aires",
    hour: "2-digit", minute: "2-digit", second: "2-digit",
    day: "2-digit", month: "2-digit", year: "numeric"
  });
  $('#reloj-buenos-aires').text("Fecha y Hora: " + ahora);
}
actualizarReloj();
setInterval(actualizarReloj, 1000);

$('#habilitarSegundaMateria').on('change', function () {
  if (this.checked) {
    $('#contenedorSegundaMateria').slideDown();
  } else {
    $('#contenedorSegundaMateria').slideUp();
    $('#materiaSegunda').val('');
    $('#materiaPedagogicaInput').val('');

    legajosSegundaMateria.forEach(legajo => {
      $(`#fila-legajo-${legajo}`).remove();
      legajosYaListados = legajosYaListados.filter(l => l != legajo);
    });
    legajosSegundaMateria = [];
  }
});

$('#materiaPrimera').on('change', function () {
  const mat = $(this).val();
  if (!mat) return $('#alumnosContainer').empty();

  $.get('config_asistencia_tec/get_alumnos.php', { materia: mat })
    .done(function (html) {
      $('#alumnosContainer').html(html);
      legajosYaListados = [];

      $('#alumnosContainer tr').each(function () {
        const id = $(this).attr('id');
        if (id && id.startsWith('fila-legajo-')) {
          const legajo = id.replace('fila-legajo-', '');
          legajosYaListados.push(legajo);
        }
      });

      legajosSegundaMateria = [];
      $('#materiaSegunda').val('');
      $('#contenedorSegundaMateria').hide();
      $('#habilitarSegundaMateria').prop('checked', false);
      $('#materiaPedagogicaInput').val('');
    })
    .fail(() => {
      $('#alumnosContainer').html('<tr><td colspan="7">Error al cargar alumnos.</td></tr>');
    });
});

$('#materiaSegunda').on('change', function () {
  const mat = $(this).val();
  if (!mat) return;

  $('#materiaPedagogicaInput').val(mat);

  legajosSegundaMateria.forEach(legajo => {
    $(`#fila-legajo-${legajo}`).remove();
    legajosYaListados = legajosYaListados.filter(l => l != legajo);
  });
  legajosSegundaMateria = [];

  $.get('config_asistencia_tec/get_alumnos.php', {
    materia: mat,
    excluir: legajosYaListados.join(',')
  })
    .done(function (html) {
      const tempDiv = $('<div>').html(html);
      const nuevasFilas = [];

      tempDiv.find('tr').each(function () {
        const id = $(this).attr('id');
        const legajo = id ? id.replace('fila-legajo-', '') : null;

        if (legajo && !legajosYaListados.includes(legajo)) {
          legajosYaListados.push(legajo);
          legajosSegundaMateria.push(legajo);
          nuevasFilas.push(this.outerHTML);
        }
      });

     if (nuevasFilas.length > 0) {
  $('#alumnosContainer').append(nuevasFilas.join(''));
  Swal.fire({
    title: 'Estudiantes agregados',
    text: 'Se sumaron nuevos estudiantes provenientes de la Unidad Curricular pedagógica seleccionada.',
    icon: 'info',
    confirmButtonColor: '#f3545d',
    background: '#fff'
  });
}
      // Eliminamos el alert innecesario en caso de que no haya nuevas filas.
    })
    .fail(() => {
      $('#alumnosContainer').append('<tr><td colspan="7">Error al cargar alumnos de la segunda materia.</td></tr>');
    });
});

$('#formAsistencia').on('submit', function (e) {
  if (!$('input[type=radio]:checked').length) {
  e.preventDefault();
  Swal.fire({
    title: 'Asistencia no registrada',
    text: 'Debes marcar al menos una asistencia antes de confirmar.',
    icon: 'warning',
    confirmButtonColor: '#f3545d',
    background: '#fff'
  });
}
});

$('#marcarTodosPresente').on('change', function () {
  if (this.checked) {
    $('#marcarTodosAusente').prop('checked', false);
    $('input[type=radio][value="1"]').prop('checked', true);
  }
});
$('#marcarTodosAusente').on('change', function () {
  if (this.checked) {
    $('#marcarTodosPresente').prop('checked', false);
    $('input[type=radio][value="2"]').prop('checked', true);
  }
});

function mostrarModalJustificacion(radio, legajo) {
  const $modal = $('#modal-' + legajo).detach().appendTo('body');
  $('.modal-justificacion').hide();
  const pos = $(radio).offset();
  $modal.css({
    top: pos.top + $(radio).outerHeight() + 4,
    left: pos.left
  }).show();
}
function cerrarModal(legajo) {
  $('#modal-' + legajo).hide();
}
function abrirOpciones(legajo) {
  $('#modal-' + legajo).children(':not(.opciones-motivo)').hide();
  $('#opciones-' + legajo).show();
}
function guardarMotivoYCerrarModal(legajo) {
  const sel = document.querySelector(`#opciones-${legajo} input[type="radio"]:checked`);
  if (sel) cerrarModal(legajo);
}
$(document).on('click', function (e) {
  if (!$(e.target).closest('.modal-justificacion').length &&
      !$(e.target).is('input[type="radio"][value="3"]')) {
    $('.modal-justificacion').hide();
  }
});
</script>



</body>
</html>
