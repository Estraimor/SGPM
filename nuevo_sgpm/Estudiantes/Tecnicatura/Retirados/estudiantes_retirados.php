<?php
include'../../../layout.php'
?>
<div class="contenido">
<?php
  $sql_carreras="SELECT c.nombre_carrera,p.carreras_idCarrera 
  FROM preceptores p 
  INNER JOIN carreras c on p.carreras_idCarrera = c.idCarrera 
  WHERE p.profesor_idProrfesor = '{$_SESSION["id"]}';";
  $query_carrera=mysqli_query($conexion,$sql_carreras);
  ?>
  <br>
  
    <h2 class="form-container__h2">Estudiante retirado antes de Tiempo</h2>
	<form action="guardar_alumnos_rat.php" id="miFormularioRetirados" method="post">
    <div class="form-row">
        <select name="selectAlumno" id="selectAlumno">
            <option value="">Seleccionar alumno</option>
        </select>
        <select id="selectMateria" name="materia">
            <option value="">Seleccione Materia</option>
        </select>
    </div>
    <div class="form-row">
        <input type="datetime-local" class="form-container__input half" name="fecha" id="fechaRetirados">
        <input type="text" name="motivo" placeholder="Motivo de Retirado" class="form-container__input half">
    </div>
    <input type="hidden" id="carrera" name="carrera" value="">
    <input hidden name="profesor" value="<?php echo $_SESSION["id"]; ?>">
    <input type="submit" class="form-container__input" name="enviar" value="Confirmar">
</form>
    <table id="tablaRetiradosTiempo">
                <thead>
                    <tr>
                      <th>Apellido</th>
                        <th>Nombre</th>
                        <th>Legajo</th>
                        <th>Preceptor</th>
                        <th>Carrera</th>
                        <th>Materia</th>
                        <th>Motivo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                <?php
              $sql1 = "SELECT a2.nombre_alumno,a2.apellido_alumno,a2.legajo,p.nombre_profe,c.nombre_carrera,m.Nombre,fecha,a.motivo
              FROM alumnos_rat a
              INNER JOIN alumno a2 on a2.legajo = a.alumno_legajo
              INNER JOIN carreras c on c.idCarrera = a.carreras_idCarrera
              INNER JOIN profesor p on a.profesor_idProrfesor = p.idProrfesor
              INNER JOIN materias m on  a.materias_idMaterias = m.idMaterias
              where p.idProrfesor = {$_SESSION["id"]}";
            $query1 = mysqli_query($conexion, $sql1);
            while ($datos = mysqli_fetch_assoc($query1)) {
                ?>
                <tr>
                    <td><?php echo $datos['apellido_alumno']; ?></td>
                    <td><?php echo $datos['nombre_alumno']; ?></td>
                    <td><?php echo $datos['legajo']; ?></td>
                    <td><?php echo $datos['nombre_profe']; ?></td>
                    <td><?php echo $datos['nombre_carrera']; ?></td>
                    <td><?php echo $datos['Nombre']; ?></td>
                    <td><?php echo $datos['motivo']; ?></td>
                    <td><?php echo $datos['fecha']; ?></td>
                </tr>
                <?php
            }
            ?>
                </tbody>
            </table>
</div>


<script>
 function mostrarAlertaExitosa() {
    alert("Registro completado con éxito!");
}

function closeSuccessMessage() {
    // Aquí puedes agregar código para cerrar cualquier mensaje de éxito o realizar acciones después del envío
    console.log("El formulario se ha enviado correctamente.");
}
</script>
<script>
    $(document).ready(function() {
    // Función para actualizar el selector de alumnos
    function actualizarSelectAlumnos(alumnos) {
        var selectAlumno = $('#selectAlumno');
        selectAlumno.empty(); // Vaciar el select para llenarlo de nuevo

        // Agregar una opción por cada alumno recibido
        alumnos.forEach(function(alumno) {
            var option = $('<option>', {
                value: alumno.legajo, // Utilizamos el legajo del alumno como valor
                text: alumno.nombre_alumno + ' ' + alumno.apellido_alumno + ' (' + alumno.legajo + ')'
            });
            selectAlumno.append(option);
        });
    }

    // Llamada inicial para llenar el selector de alumnos
    $.ajax({
        url: 'obtener_opciones.php',
        method: 'POST',
        data: {},
        dataType: 'json', // Especificar que esperamos datos JSON en la respuesta
        success: function(response) {
            actualizarSelectAlumnos(response.alumnos);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });

    // Función para filtrar los selectores al escribir en los inputs
    $('#filtroAlumno').on('input', function() {
        var alumno = $(this).val();

        // Llamar a la función para actualizar los selectores
        actualizarSelects(alumno);
    });

    function actualizarSelects(alumno) {
        $.ajax({
            url: 'obtener_opciones.php',
            method: 'POST',
            data: { alumno: alumno },
            dataType: 'json', // Especificar que esperamos datos JSON en la respuesta
            success: function(response) {
                actualizarSelectAlumnos(response.alumnos);

                // Actualizar el valor del input hidden con el id de la carrera del primer alumno
                var carreraHidden = $('#carrera');
                if (response.alumnos.length > 0) {
                    carreraHidden.val(response.alumnos[0].carreras_idCarrera);
                    // Llamar a la función para actualizar el selector de materias
                    actualizarSelectMaterias(response.alumnos[0].carreras_idCarrera);
                } else {
                    carreraHidden.val(''); // Si no hay alumnos, vaciar el valor del input hidden
                    // Limpiar el selector de materias
                    limpiarSelectMaterias();
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Función para actualizar el selector de materias
    function actualizarSelectMaterias(idCarrera) {
        $.ajax({
            url: 'obtener_materias.php',
            method: 'POST',
            data: { carrera: idCarrera },
            dataType: 'json',
            success: function(response) {
                var selectMateria = $('#selectMateria');
                selectMateria.empty(); // Vaciar el select para llenarlo de nuevo

                // Agregar una opción por cada materia recibida
                response.materias.forEach(function(materia) {
                    var option = $('<option>', {
                        value: materia.idMaterias,
                        text: materia.Nombre
                    });
                    selectMateria.append(option);
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Función para limpiar el selector de materias
    function limpiarSelectMaterias() {
        var selectMateria = $('#selectMateria');
        selectMateria.empty(); // Vaciar el select
        selectMateria.append('<option value="">Seleccione Materia</option>'); // Agregar la opción por defecto
    }

    // Interceptamos el envío del formulario para cambiar los valores de los selectores por los IDs
    $('#miFormularioRetirados').submit(function() {
        var alumnoSeleccionado = $('#selectAlumno').val();

        // Actualizamos los valores de los selectores por los IDs
        $('#selectAlumno').val(alumnoSeleccionado);
        // El valor de la materia ya es el ID, no es necesario cambiarlo
    });

    // Evento change para el select de alumno
    $('#selectAlumno').change(function() {
        // Obtener el valor seleccionado en el select de alumno
        var legajoAlumno = $(this).val();

        // Enviar una solicitud AJAX para obtener la carrera del alumno
        $.ajax({
            url: 'obtener_carrera.php', // Ruta al script PHP que obtiene la carrera
            method: 'POST',
            data: { legajo: legajoAlumno }, // Datos a enviar al servidor
            dataType: 'json', // Especificar que esperamos datos JSON en la respuesta
            success: function(response) {
                // Rellenar el campo de carrera con el valor obtenido
                $('#carrera').val(response.carrera);
                // Llamar a la función para actualizar el selector de materias
                actualizarSelectMaterias(response.carrera);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

// dataTables de Alumnos //
var myTable = document.querySelector("#tablaRetiradosTiempo");
var dataTable = new DataTable(tablaRetiradosTiempo);
</script>

</body>
</html>

