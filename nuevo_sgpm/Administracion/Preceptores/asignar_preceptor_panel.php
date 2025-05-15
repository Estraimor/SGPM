<?php
include'../../layout.php';
include '../../../conexion/conexion.php';    // tu conexión a la base de datos
session_start();
?>
<link
  rel="stylesheet"
  href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"
/>
<script
  src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"
></script>
<style>
  .contenedor-panels { display: flex; gap: 16px; margin: 20px; }
  .panel {
    flex: 1; background: #fff; border-radius: 8px;
    padding: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  .panel h3 {
    margin-top: 0; font-size: 1.1rem;
    border-bottom: 1px solid #eee; padding-bottom: 6px;
  }
  tr.selected { background: #e0f7fa; }
  table { width:100%; border-collapse: collapse; }
  table th, table td { padding:6px; border:1px solid #ddd; }
  .btn { margin-top: 10px; }
  /* Contenedor general */

/* Ajuste de columnas: izquierda más angosta, centro más amplia */
.contenedor-panels {
  display: grid;
  grid-template-columns: 1fr 1.6fr 1fr;
  gap: 20px;
}

/* Checkbox custom */
input[type="checkbox"] {
  /* agrandar tamaño */
  width: 18px;
  height: 18px;
  /* color del ✔ cuando está marcado */
  accent-color: #f3545d;
  cursor: pointer;
  /* pequeño “lift” al pasar el mouse */
  transition: transform 0.1s;
}
input[type="checkbox"]:hover {
  transform: scale(1.1);
}

/* DataTable Profesores: th y td */
#tblProfesores th,
#tblProfesores td {
  padding: 10px 14px;
  border-bottom: 1px solid #eee;
  color: #444;
}

/* Encabezados con fondo rojo y texto blanco */
#tblProfesores th {
  background: #f3545d;
  color: #fff;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Filas alternadas y hover */
#tblProfesores tbody tr:nth-child(even) {
  background: #fcfcfc;
}
#tblProfesores tbody tr:hover {
  background: rgba(243, 84, 93, 0.05);
}

/* Panel medio: hacer más alto y agregar scroll si es necesario */
#tblComisiones {
  max-height: 500px;
  overflow-y: auto;
  display: block;
}
#tblComisiones thead {
  position: sticky;
  top: 0;
  background: #fff;
  z-index: 1;
}

/* Estilo de th y td en tabla central */
#tblComisiones th,
#tblComisiones td {
  padding: 10px 12px;
  border-bottom: 1px solid #eee;
  color: #555;
}
#tblComisiones th {
  background: #fafafa;
  font-weight: 600;
}

/* Input-groups en panel derecho: alinear checkbox y selects */
.input-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 16px;
}
.input-group label {
  margin-bottom: 6px;
  font-weight: 500;
  color: #333;
}

/* Asegurar que selects expandan al 100% */
.input-group select {
  width: 100%;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 8px;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.input-group select:focus {
  border-color: #f3545d;
  box-shadow: 0 0 0 2px rgba(243,84,93,0.2);
}

/* Botón */
.btn-primary {
  padding: 10px 16px;
  border-radius: 8px;
  background: #f3545d;
  color: #fff;
  font-weight: 600;
  transition: background 0.2s, transform 0.1s;
}
.btn-primary:hover {
  background: #d9434b;
  transform: translateY(-1px);
}
.btn-primary:active {
  background: #b8323e;
  transform: translateY(0);
}

/* Responsive: en pantallas pequeñas, apilar */
@media (max-width: 992px) {
  .contenedor-panels {
    grid-template-columns: 1fr;
  }
}


</style>
<div class="contenido">
<div class="contenedor-panels">

  <!-- IZQUIERDA: Profesores -->
  <div class="panel">
    <h3>Docentes</h3>
    <table id="tblProfesores" class="display">
      <thead>
        <tr><th>ID</th><th>Apellido</th><th>Nombre</th></tr>
      </thead><tbody></tbody>
    </table>
  </div>

  <!-- CENTRAL: Comisiones asignadas -->
  <div class="panel">
    <h3>Comisiones asignadas</h3>
    <table id="tblComisiones">
      <thead>
        <tr>
          <th></th>
          <th>Carrera</th>
          <th>Curso</th>
          <th>Comisión</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <!-- DERECHA: Selección de nueva asignación -->
  <div class="panel">
    <h3>Nueva Asignación</h3>
    <div class="input-group">
      <label>Carrera:</label>
      <select id="selCarreraAgregar">
        <option value="">--Selecciona--</option>
      </select>
    </div>
    <div class="input-group">
      <label>Curso:</label>
      <select id="selCursoAgregar">
        <option value="">--Selecciona--</option>
      </select>
    </div>
    <div class="input-group">
      <label>Comisión:</label>
      <select id="selComisionAgregar">
        <option value="">--Selecciona--</option>
      </select>
    </div>
    <button id="btnAgregar" class="btn btn-primary">
      Agregar Asignación
    </button>
  </div>

</div>
</div>
<script>
$(function(){
  let profSel = null;
  // Ajusta esto a la ruta real (fíjate en que solo hay UN slash entre nuevo_sgpm y Administracion)
  const AJAX_BASE = '/SGPM2/nuevo_sgpm//Administracion/Preceptores/ajax/';

  // 1) DataTable para Profesores
  const dtProf = $('#tblProfesores').DataTable({
    ajax: {
      url: AJAX_BASE + 'get_profesores.php',
      dataSrc: 'data'
    },
    columns: [
      { data: 'idProfesor' },
      { data: 'apellido_profe' },
      { data: 'nombre_profe' }
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
    }
  });

  // 2) Carga selects de Nueva Asignación
  function cargarSelect(nombreArchivo, $sel, idField, textField) {
    $.getJSON(AJAX_BASE + nombreArchivo, resp => {
      resp.data.forEach(item => {
        $sel.append(
          `<option value="${item[idField]}">${item[textField]}</option>`
        );
      });
    })
    .fail((_,__,err) => console.error(`Error cargando ${nombreArchivo}:`, err));
  }
  cargarSelect('get_carreras.php',   $('#selCarreraAgregar'),   'idCarrera',   'nombre_carrera');
  cargarSelect('get_cursos.php',     $('#selCursoAgregar'),     'idCursos',    'curso');
  cargarSelect('get_comisiones.php', $('#selComisionAgregar'),  'idComisiones','comision');

  // 3) Al seleccionar profesor
  $('#tblProfesores tbody').on('click','tr', function(){
    const d = dtProf.row(this).data();
    if(!d) return;
    profSel = d.idProfesor;
    $('#tblProfesores tr.selected').removeClass('selected');
    $(this).addClass('selected');

    // Pinto comisiones asignadas
    $('#tblComisiones tbody').empty();
    $.getJSON(AJAX_BASE + 'get_comisiones_por_profesor.php', { profesor: profSel })
      .done(list => {
        list.forEach(r => {
          $('#tblComisiones tbody').append(`
            <tr>
              <td>
                <input type="checkbox" class="chkDel"
                       data-carrera="${r.idCarrera}"
                       data-curso="${r.idCurso}"
                       data-comision="${r.idComision}"
                       checked>
              </td>
              <td>${r.nombre_carrera}</td>
              <td>${r.curso}</td>
              <td>${r.comision}</td>
            </tr>
          `);
        });
      })
      .fail((_,__,err) => console.error('Error get_comisiones_por_profesor:', err));
  });

  // 4) Desasignar con confirmación
  $('#tblComisiones').on('change','.chkDel', function(){
    if(!profSel || this.checked) return;
    const cb     = this,
          $row   = $(cb).closest('tr'),
          idCarr = cb.dataset.carrera,
          idCur  = cb.dataset.curso,
          idCom  = cb.dataset.comision;

    Swal.fire({
      title: '¿Estás seguro?',
      text: 'Se eliminará esta asignación.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, desasignar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#f3545d',
      background: '#fff'
    }).then(res => {
      if(!res.isConfirmed){
        $(cb).prop('checked', true);
        return;
      }
      $.post(AJAX_BASE + 'remove_asignacion.php', {
        profesor: profSel,
        carrera:  idCarr,
        curso:    idCur,
        comision: idCom
      }, resp => {
        if(resp.success){
          $row.fadeOut(200, ()=> $row.remove());
          Swal.fire({
            title: 'Desasignado',
            text: 'Se eliminó correctamente.',
            icon: 'success',
            confirmButtonColor: '#f3545d',
            background: '#fff'
          });
        } else {
          Swal.fire({
            title: 'Error',
            text: resp.msg || 'No se pudo desasignar.',
            icon: 'error',
            confirmButtonColor: '#f3545d',
            background: '#fff'
          });
          $(cb).prop('checked', true);
        }
      }, 'json')
      .fail(() => {
        Swal.fire({
          title: 'Error',
          text: 'Fallo de servidor.',
          icon: 'error',
          confirmButtonColor: '#f3545d',
          background: '#fff'
        });
        $(cb).prop('checked', true);
      });
    });
  });

  // 5) Agregar asignación con confirmación
  $('#btnAgregar').click(function(){
    if(!profSel){
      return Swal.fire({
        title: '¡Atención!',
        text: 'Selecciona primero un profesor.',
        icon: 'info',
        confirmButtonColor: '#f3545d',
        background: '#fff'
      });
    }
    const idCarr = +$('#selCarreraAgregar').val(),
          idCur  = +$('#selCursoAgregar').val(),
          idCom  = +$('#selComisionAgregar').val();
    if(!idCarr||!idCur||!idCom){
      return Swal.fire({
        title: 'Faltan datos',
        icon: 'warning',
        confirmButtonColor: '#f3545d',
        background: '#fff'
      });
    }
    Swal.fire({
      title: '¿Confirmas la asignación?',
      html: `
        Carrera: <b>${$('#selCarreraAgregar option:selected').text()}</b><br>
        Curso:   <b>${$('#selCursoAgregar option:selected').text()}</b><br>
        Comisión:<b>${$('#selComisionAgregar option:selected').text()}</b>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, asignar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#f3545d',
      background: '#fff'
    }).then(res => {
      if(!res.isConfirmed) return;
      $.post(AJAX_BASE + 'asignar_asignacion.php', {
        profesor: profSel,
        carrera:  idCarr,
        curso:    idCur,
        comision: idCom
      }, resp => {
        if(resp.success){
          Swal.fire({
            title: 'Asignado',
            text: resp.msg || 'Asignación creada.',
            icon: 'success',
            confirmButtonColor: '#f3545d',
            background: '#fff'
          });
          $('#tblProfesores tr.selected').click();
        } else {
          Swal.fire({
            title: 'Aviso',
            text: resp.msg || 'No se creó la asignación.',
            icon: 'info',
            confirmButtonColor: '#f3545d',
            background: '#fff'
          });
        }
      }, 'json')
      .fail(() => {
        Swal.fire({
          title: 'Error',
          text: 'Fallo de servidor.',
          icon: 'error',
          confirmButtonColor: '#f3545d',
          background: '#fff'
        });
      });
    });
  });

});
</script>
