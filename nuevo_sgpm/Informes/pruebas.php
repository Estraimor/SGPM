<?php
// informe_lista_estudiantes.php actualizado con carga de materias y comisiones
include '../layout.php';
session_start();

$conexion = mysqli_connect('localhost', 'u756746073_root', 'POLITECNICOmisiones2023.', 'u756746073_politecnico', '3306');

$idPreceptor = $_SESSION['id'];
$rolUsuario  = $_SESSION['roles'];

// Consulta para obtener las carreras
if ($rolUsuario == 1) {
    $sql_carreras = "SELECT DISTINCT c.idCarrera, c.nombre_carrera FROM carreras c";
} else {
    $sql_carreras = "SELECT DISTINCT c.idCarrera, c.nombre_carrera 
                     FROM carreras c
                     INNER JOIN preceptores p ON c.idCarrera = p.carreras_idCarrera
                     WHERE p.profesor_idProrfesor = '{$idPreceptor}'";
}
$consulta_carreras = mysqli_query($conexion, $sql_carreras);

// Consulta para obtener cursos
if ($rolUsuario == 1) {
    $sql_cursos = "SELECT DISTINCT cu.idCursos, cu.curso FROM cursos cu";
} else {
    $sql_cursos = "SELECT DISTINCT cu.idCursos, cu.curso 
                   FROM cursos cu
                   INNER JOIN preceptores p ON cu.idCursos = p.cursos_idCursos
                   WHERE p.profesor_idProrfesor = '{$idPreceptor}'";
}
$consulta_cursos = mysqli_query($conexion, $sql_cursos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Estudiantes</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
     <style>
      /* Contenedor principal */
      .contenido {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        font-family: 'Arial', sans-serif;
      }
      /* Panel de filtros */
      .select-container {
        flex: 1;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        border: 1px solid #ddd;
      }
      .select-container h2 {
        color: #f3545d;
        font-weight: bold;
        margin-bottom: 20px;
      }
      .select-container select,
      .select-container input[type="submit"] {
        width: 100%;
        padding: 12px;
        border-radius: 5px;
        font-size: 1rem;
        font-family: inherit;
        margin-bottom: 20px;
      }
      .select-container select {
        border: 1px solid #f3545d;
      }
      .select-container input[type="submit"] {
        background-color: #f3545d;
        color: #fff;
        border: none;
        cursor: pointer;
        transition: background-color .3s;
      }
      .select-container input[type="submit"]:hover {
        background-color: #d64545;
      }

      /* Contenedor de la tabla */
      .table-container {
        flex: 2;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        border: 1px solid #ddd;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      }
      .table-container h3 {
        color: #f3545d;
        font-size: 1.2rem;
        margin-bottom: 16px;
      }
      #totalEstudiantes {
        font-weight: bold;
      }

      /* Tabla de estudiantes */
      .student-table {
        width: 100%;
        border-collapse: collapse;
        display: none;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: inset 0 0 0 1px #ddd;
      }
      .student-table thead {
        background-color: #f3545d;
      }
      .student-table thead th {
        color: #fff;
        text-align: left;
        padding: 12px 10px;
        font-weight: 600;
      }
      .student-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
      }
      .student-table tbody tr:hover {
        background-color: #ffecec;
      }
      .student-table td {
        padding: 10px;
        color: #333;
        font-size: 0.9rem;
      }
      .student-table .no-data td {
        text-align: center;
        font-style: italic;
        color: #666;
      }
    </style>
  <div class="contenido">
    <div class="select-container">
      <h2>Selecciona una carrera</h2>
      <form action="../../../indexs/generar_exel_alumnos.php" method="post">
        <select name="carrera" id="seleccionar_carrera">
          <option hidden>Selecciona una carrera</option>
          <?php while ($fila = mysqli_fetch_assoc($consulta_carreras)): ?>
            <option value="<?= $fila['idCarrera'] ?>"><?= $fila['nombre_carrera'] ?></option>
          <?php endwhile; ?>
        </select>

        <select name="curso" id="seleccionar_curso">
          <option hidden>Selecciona un curso</option>
          <?php mysqli_data_seek($consulta_cursos, 0); ?>
          <?php while ($fila = mysqli_fetch_assoc($consulta_cursos)): ?>
            <option value="<?= $fila['idCursos'] ?>"><?= $fila['curso'] ?></option>
          <?php endwhile; ?>
        </select>

        <select name="comision" id="seleccionar_comision">
          <option hidden>Selecciona una comisión</option>
        </select>

        <select name="materia" id="seleccionar_materia">
          <option hidden>Selecciona una materia</option>
        </select>

        <input type="submit" value="Generar Lista de Estudiantes">
      </form>
    </div>

    <div class="table-container">
      <h3>Total de Estudiantes: <span id="totalEstudiantes">0</span></h3>
      <table id="tablaEstudiantes" class="student-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

<script>
$(function() {
  $('#seleccionar_curso').on('change', function() {
    let idCarrera = $('#seleccionar_carrera').val();
    let idCurso = $(this).val();

    if (!idCarrera || !idCurso) return;

    $.ajax({
      url: './confi_lista_estu/obtener_comisiones.php',
      data: { idCarrera, idCurso },
      dataType: 'json',
      success: function(data) {
        let sel = $('#seleccionar_comision').empty().append('<option hidden>Selecciona una comisión</option>');
        if (data.length) {
          data.forEach(c => sel.append(`<option value="${c.idComisiones}">${c.comision}</option>`));
        } else {
          sel.append('<option class="no-data">No hay comisiones disponibles</option>');
        }
      },
      error: function(xhr) {
        alert("Error al obtener comisiones: " + xhr.responseText);
      }
    });
  });

  $('#seleccionar_comision').on('change', function() {
    let idCarrera = $('#seleccionar_carrera').val(),
        idCurso = $('#seleccionar_curso').val(),
        idComision = $(this).val();

    if (!idCarrera || !idCurso || !idComision) return;

    $.ajax({
      url: './confi_lista_estu/obtener_materias.php',
      data: { idCarrera, idCurso, idComision },
      dataType: 'json',
      success: function(data) {
        let sel = $('#seleccionar_materia').empty().append('<option hidden>Selecciona una materia</option>');
        if (data.length) {
          data.forEach(m => sel.append(`<option value="${m.idMaterias}">${m.Nombre}</option>`));
        } else {
          sel.append('<option class="no-data">No hay materias disponibles</option>');
        }
      },
      error: function(xhr) {
        alert("Error al obtener materias: " + xhr.responseText);
      }
    });
  });

  $('#seleccionar_materia').on('change', function() {
    let idMateria = $(this).val();
    if (!idMateria) return;

    $.ajax({
      url: 'obtener_estudiantes.php',
      data: { idMateria },
      dataType: 'json',
      success: function(data) {
        let tbl = $('#tablaEstudiantes'),
            body = tbl.find('tbody').empty();

        if (data.length) {
          data.forEach((e, i) => {
            body.append(`<tr><td>${i+1}</td><td>${e.nombre_alumno}</td><td>${e.apellido_alumno}</td><td>${e.dni_alumno}</td></tr>`);
          });
          $('#totalEstudiantes').text(data.length);
        } else {
          body.append('<tr class="no-data"><td colspan="4">No hay estudiantes matriculados.</td></tr>');
          $('#totalEstudiantes').text(0);
        }
        tbl.show();
      },
      error: function(xhr) {
        alert("Error al obtener los estudiantes: " + xhr.responseText);
      }
    });
  });
});
</script>
</body>
</html>