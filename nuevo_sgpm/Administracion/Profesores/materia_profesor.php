<?php
include'../../layout.php'
?>
<head>
 <link rel="stylesheet" href="./estilos-profes2_prueba.css">
 <!-- DataTables (simple-datatables) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" />
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" defer></script>
</head>
<div class="contenido-profe">
    
    <?php
    $sql_profe="SELECT * FROM profesor p";
    $query_profe=mysqli_query($conexion,$sql_profe);
?>
<div class="container">
<?php
    $sql_profe="SELECT * FROM profesor p";
    $query_profe=mysqli_query($conexion,$sql_profe);
    ?>
<div class="container">
    <div class="left-box">
    <!-- Tabla de Profesores -->
    <table id="professorsTable">
        <thead>
            <tr>
                <th class="numero">#</th>
                <th class="apellido">Apellido</th>
                <th class="nombre">Nombre</th>
                <th class="seleccionarcheck">*</th>
            </tr>
        </thead>
        <tbody>
            <?php $contador=1; ?>
            <?php while($datosprofe=mysqli_fetch_assoc($query_profe)){ ?>
                <tr>
                    <td><?php echo $contador++; ?></td>
                    <td><?php echo $datosprofe['apellido_profe']; ?></td>
                    <td><?php echo $datosprofe['nombre_profe']; ?></td>
                    <td><input type="checkbox" name="professorSelect" value="<?php echo $datosprofe['idProrfesor']; ?>"></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>


<div class="middle-box">
        <h3>Materias Asignadas</h3>
        <form id="formAssignedMaterias" method="POST">
            <table id="assignedMaterias">
                <thead>
                    <tr>
                        <th class="materia">Materia</th>
                        <th style="width: 25px;" class="dias">*</th>
                        <th class="dias">L</th>
                        <th class="dias">M</th>
                        <th class="dias">X</th>
                        <th class="dias">J</th>
                        <th class="dias">V</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Las materias asignadas se cargarán aquí -->
                </tbody>
            </table>
            <div class="button-container">
            <button type="submit" class="botonguardar">Guardar Cambios</button>
            </div>
        </form>
    </div>
   

    <div class="right-box">
        <form id="formAvailableMaterias" method="POST">
            <select id="careerSelect">
                <option class="options" value="">Selecciona una carrera</option>
                <!-- Opciones llenadas dinámicamente por JS -->
            </select>
            <select id="cursoSelect">
    <option class="options" value="">Selecciona un curso</option>
</select>

<select id="comisionSelect">
    <option class="options" value="">Selecciona una comisión</option>
</select>
            <table id="availableMaterias">
                <!-- Las materias disponibles se cargarán aquí -->
            </table>
            <button id="inscribirMateriasBtn" style=" width: 19%;">  <i class="far fa-arrow-alt-circle-left"></i></button>
        </form>
    </div>
</div>
</div>



<script>
$(document).ready(function () {
    // Cargar carreras
    $.ajax({
        url: 'getCarreras.php',
        type: 'GET',
        dataType: 'json',
        success: function (carreras) {
            var select = $('#careerSelect');
            carreras.forEach(function (carrera) {
                select.append('<option value="' + carrera.idCarrera + '">' + carrera.nombre_carrera + '</option>');
            });
        }
    });

    // Cargar cursos
    $.ajax({
        url: 'getCursos.php',
        type: 'GET',
        dataType: 'json',
        success: function (cursos) {
            var select = $('#cursoSelect');
            cursos.forEach(function (curso) {
                select.append('<option value="' + curso.idCursos + '">' + curso.curso + '</option>');
            });
        }
    });

    // Cargar comisiones
    $.ajax({
        url: 'getComisiones.php',
        type: 'GET',
        dataType: 'json',
        success: function (comisiones) {
            var select = $('#comisionSelect');
            comisiones.forEach(function (comi) {
                select.append('<option value="' + comi.idComisiones + '">' + comi.comision + '</option>');
            });
        }
    });

    // Cargar materias solo si carrera + curso + comisión están seleccionados
    function cargarMateriasSiTodoSeleccionado() {
        var carreraId = $('#careerSelect').val();
        var cursoId = $('#cursoSelect').val();
        var comisionId = $('#comisionSelect').val();

        if (carreraId && cursoId && comisionId) {
            $.ajax({
                url: 'getMateriasFiltradas.php',
                type: 'POST',
                data: {
                    carreraId: carreraId,
                    cursoId: cursoId,
                    comisionId: comisionId
                },
                dataType: 'json',
                success: function (materias) {
                    var table = $('#availableMaterias');
                    table.empty();

                    if (materias.length > 0) {
                        materias.forEach(function (materia) {
                            table.append('<tr data-id="' + materia.idMaterias + '"><td>' + materia.nombre + '</td><td style="width: 30px;"><input type="checkbox"></td></tr>');
                        });
                    } else {
                        table.append('<tr><td colspan="2">No hay materias para esta combinación.</td></tr>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar materias filtradas:", error);
                }
            });
        } else {
            $('#availableMaterias').empty().append('<tr><td colspan="2">Seleccione carrera, curso y comisión.</td></tr>');
        }
    }

    $('#careerSelect, #cursoSelect, #comisionSelect').on('change', cargarMateriasSiTodoSeleccionado);

    // DataTable con buscador para la tabla de profesores
    const myTable = document.querySelector("#professorsTable");
    if (myTable) {
        new simpleDatatables.DataTable(myTable, {
            perPage: 1000,
            perPageSelect: [1000],
            labels: {
                placeholder: "Buscar profesor...",
                perPage: "{select} registros por página",
                noRows: "No se encontraron profesores",
                info: "Mostrando {start} a {end} de {rows} profesores"
            }
        });
    }

    // Lógica para selección de profesor
    var lastSelectedProfessorId = null;

    $('#professorsTable').on('change', 'input[name="professorSelect"]', function () {
        var profesorId = $(this).val();
        var isChecked = $(this).is(':checked');

        $('input[name="professorSelect"]').not(this).prop('checked', false);
        $('#assignedMaterias tbody').empty();

        if (isChecked && profesorId !== lastSelectedProfessorId) {
            cargarMateriasAsignadas(profesorId);
            lastSelectedProfessorId = profesorId;
        } else {
            lastSelectedProfessorId = null;
        }
    });

    function cargarMateriasAsignadas(profesorId) {
        $.ajax({
            url: 'getMateriasAsignadas.php',
            type: 'POST',
            data: { profesorId: profesorId },
            dataType: 'json',
            success: function (materias) {
                var table = $('#assignedMaterias tbody');
                table.empty();

                materias.forEach(function (materia) {
                    var dayIds = [1, 2, 3, 4, 5];
                    var daysCells = dayIds.map(dayId => `
                        <td class="day-cell">
                            <input type="checkbox" value="${dayId}" ${materia.dias && materia.dias[dayId] ? 'checked' : ''}>
                            <label></label><br>
                            <h4 style="font-size: 10px;">Entrada</h4>
                            <input type="time" class="input-hora" name="entrada_${dayId}_${materia.idMaterias}" value="${materia.dias && materia.dias[dayId] ? materia.dias[dayId].horario_entrada : ''}">
                            <h4 style="font-size: 10px;">Salida</h4>
                            <input type="time" class="input-hora" name="salida_${dayId}_${materia.idMaterias}" value="${materia.dias && materia.dias[dayId] ? materia.dias[dayId].horario_salida : ''}">
                        </td>`).join('');

                    table.append(`
                        <tr data-id="${materia.idMaterias}">
                            <td>${materia.nombre} (${materia.nombre_carrera})</td>
                            <td><input type="checkbox" ${materia.estado == 1 ? 'checked' : ''}></td>
                            ${daysCells}
                        </tr>
                    `);
                });

                if (materias.length === 0) {
                    table.append('<tr><td colspan="7">No hay materias asignadas.</td></tr>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar materias asignadas:", error);
            }
        });
    }

   $('#formAssignedMaterias').on('submit', function (e) {
    e.preventDefault();
    
    let ajaxRequests = [];
    let updates = [];
    let profesorId = $('input[name="professorSelect"]:checked').val();

    $('#assignedMaterias tr').each(function () {
        var materiaId = $(this).attr('data-id');

        // Detectar si la materia fue desmarcada (checkbox de estado)
        var estadoCheckbox = $(this).find('input[type="checkbox"]:first');
        var isChecked = estadoCheckbox.is(':checked');

       if (!isChecked) {
    if (materiaId && !isNaN(materiaId) && profesorId && !isNaN(profesorId)) {
        updates.push({
            materiaId: parseInt(materiaId),
            profesorId: parseInt(profesorId),
            desmarcado: true
        });
    } else {
        console.warn("❌ materiaId o profesorId inválido", { materiaId, profesorId });
    }
}

 
console.log("Updates a enviar:", updates);

        // Recorrer los días
        $(this).find('td.day-cell').each(function () {
            var checkbox = $(this).find('input[type="checkbox"]');
            var dayId = checkbox.val();
            var entrada = $(this).find(`input[name="entrada_${dayId}_${materiaId}"]`).val();
            var salida = $(this).find(`input[name="salida_${dayId}_${materiaId}"]`).val();

            if (checkbox.is(':checked')) {
                if (entrada && salida) {
                    ajaxRequests.push(
                        $.ajax({
                            url: 'guardar_dias_semana_profe.php',
                            type: 'POST',
                            data: {
                                materiaId: materiaId,
                                diaId: dayId,
                                entrada: entrada,
                                salida: salida
                            }
                        })
                    );
                }
            } else {
                ajaxRequests.push(
                    $.ajax({
                        url: 'borrar_dias_semana_profe.php',
                        type: 'POST',
                        data: {
                            materiaId: materiaId,
                            diaId: dayId
                        }
                    })
                );
            }
        });
    });

    // Esperar que todas las peticiones AJAX terminen
    Promise.all(ajaxRequests)
        .then(function () {
            if (updates.length > 0) {
                // Enviar materias desmarcadas
                $.ajax({
                    url: 'updateMaterias.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ updates }),
                    success: function () {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Guardado!',
                            text: 'Cambios guardados correctamente.',
                            confirmButtonColor: '#f3545d',
                            background: '#fff'
                        }).then(() => location.reload());
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al actualizar materias.',
                            confirmButtonColor: '#f3545d',
                            background: '#fff'
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: 'Cambios guardados correctamente.',
                    confirmButtonColor: '#f3545d',
                    background: '#fff'
                }).then(() => location.reload());
            }
        });
});


    // Inscripción de materias
    $('#inscribirMateriasBtn').on('click', function (e) {
        e.preventDefault();
        var profesorId = $('input[name="professorSelect"]:checked').val();
        if (!profesorId) {
            return Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Seleccioná un profesor antes de asignar materias.',
                confirmButtonColor: '#f3545d',
                background: '#fff'
            });
        }

        var checkedMaterias = $('#availableMaterias input[type="checkbox"]:checked');
        if (checkedMaterias.length === 0) {
            return Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Seleccioná al menos una materia para asignar.',
                confirmButtonColor: '#f3545d',
                background: '#fff'
            });
        }

        checkedMaterias.each(function () {
            var materiaId = $(this).closest('tr').data('id');

            $.ajax({
                url: 'asignar_materiasprofe.php',
                type: 'POST',
                data: { profesorId, materiaId },
                dataType: 'json',
                success: function (response) {
                    if (response.warning) {
                        Swal.fire({
                            icon: 'question',
                            title: '¿Reasignar?',
                            text: response.warning,
                            showCancelButton: true,
                            confirmButtonText: 'Sí',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#f3545d',
                            background: '#fff'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.post('asignar_materiasprofe.php', {
                                    profesorId,
                                    materiaId,
                                    force: true
                                }, function (res) {
                                    Swal.fire({
                                        icon: 'success',
                                        text: res.message || 'Materia reasignada correctamente.',
                                        confirmButtonColor: '#f3545d',
                                        background: '#fff'
                                    });
                                    cargarMateriasAsignadas(profesorId);
                                }, 'json');
                            }
                        });
                    } else if (response.message) {
                        Swal.fire({
                            icon: 'success',
                            text: response.message,
                            confirmButtonColor: '#f3545d',
                            background: '#fff'
                        });
                        cargarMateriasAsignadas(profesorId);
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un problema al asignar materias.',
                        confirmButtonColor: '#f3545d',
                        background: '#fff'
                    });
                }
            });
        });
    });
});
</script>



</body>
</html>

