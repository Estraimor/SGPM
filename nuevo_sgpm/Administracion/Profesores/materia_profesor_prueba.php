<?php
session_start();
if (empty($_SESSION["id"])){header('Location: ../../../login/login.php');}

// Asumimos que también almacenas el rol en la sesión.
$rolUsuario = $_SESSION["roles"];

// Definimos los roles permitidos para esta página.
$rolesPermitidos = ['1', '2'];

// Verificar si el rol del usuario está en la lista de roles permitidos.
if (!in_array($rolUsuario, $rolesPermitidos)) {
    echo "<script>alert('Acceso restringido a esta página.');</script>";
    // Opcional: redirigir al usuario a otra página
    // header('Location: pagina_principal.php');
    exit; // Detener la ejecución del script.
}

// Set inactivity limit in seconds
$inactivity_limit = 14400;

// Check if the user has been inactive for too long
if (isset($_SESSION['time']) && (time() - $_SESSION['time'] > $inactivity_limit)) {
    // User has been inactive, so destroy the session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: ../../../login/login.php");
    exit; // Terminar el script después de redireccionar
} else {
    // Update the session time to the current time
    $_SESSION['time'] = time();
}
?>
<?php include'../../../conexion/conexion.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>SGPM</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="../../assets/img/Logo ISPM 2 transparante.png" type="image/x-icon"/>
	<!-- DATA TABLES -->
    <link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
     <script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>
	<!-- Fonts and icons -->
	<script src="../../assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Open+Sans:300,400,600,700"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"], urls: ['../../assets/css/fonts.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../assets/css/azzara.min.css">
	<link rel="stylesheet" href="../../assets/css/estilos.css">
    <link rel="stylesheet" href="./estilos-profes2_prueba.css">
    <!--<link rel="stylesheet" href="./profes.css">-->

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="../../assets/css/demo.css">
</head>
<body>
	<div class="wrapper">
		
		<div class="main-header" data-background-color="red">
			<div class="logo-header">
				
				<a href="../../index.php" class="logo">
					<img src="../../assets/img/Logo ISPM 2 transparante.png" width="45px" alt="navbar brand" class="navbar-brand">
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="fa fa-bars"></i>
					</span>
				</button>
				<button class="topbar-toggler more"><i class="fa fa-ellipsis-v"></i></button>
				<div class="navbar-minimize">
					<button class="btn btn-minimize btn-rounded">
						<i class="fa fa-bars"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->

			<!-- Navbar Header -->
			<nav class="navbar navbar-header navbar-expand-lg">
				
				<div class="container-fluid">
					<div class="collapse" id="search-nav">
						<form class="navbar-left navbar-form nav-search mr-md-3">
							
						</form>
					</div>
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<li class="nav-item toggle-nav-search hidden-caret">
							<a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false" aria-controls="search-nav">
								<i class="fa fa-search"></i>
							</a>
						</li>
						
						
						<li class="nav-item dropdown hidden-caret">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
								<div class="avatar-sm">
									<img src="../../assets/img/1361728.png" alt="..." class="avatar-img rounded-circle" >
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
							<li>
								<div class="user-box">
									<div class="avatar-lg"><img src="../../assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;"></div>
									<div class="u-text">
										<?php 
										$sql_profe = "SELECT p.idProrfesor, p.nombre_profe, p.apellido_profe, p.email FROM profesor p WHERE p.idProrfesor = '{$_SESSION["id"]}'";
										$query_nombre = mysqli_query($conexion, $sql_profe);

										// Comprobar si la consulta devolvió algún resultado
										if (mysqli_num_rows($query_nombre) > 0) {
											// Recorrer los resultados y hacer echo del nombre y apellido del profesor
											while ($row = mysqli_fetch_assoc($query_nombre)) { ?>
												<h4><?php echo $row['nombre_profe'] . " " . $row['apellido_profe']; ?></h4>
												<p class="text-muted email-text"><?php echo $row['email']; ?></p>
											<?php 
											} 
										} else {
											echo "<h4>Usuario</h4>";
											echo "<p class='text-muted'>Correo Electronico</p>";
										}
										?>
			
									</div>
								</div>
							</li>
								<li>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="#">Mi Perfil</a>
									<a class="dropdown-item" href="#">Cambiar Contraseña</a>
									<a class="dropdown-item" href="../../../login/cerrar_sesion.php">Cerrar Sesión</a>
								</li>
							</ul>
						</li>
						
					</ul>
				</div>
			</nav>
			<!-- End Navbar -->
		</div>

		<!-- Sidebar -->
		<div class="sidebar">
			
			<div class="sidebar-background"></div>
			<div class="sidebar-wrapper scrollbar-inner">
				
					<ul class="nav">
						<li class="nav-item">
							<a href="../../index.php">
								<i class="fas fa-home"></i>
								<p>Panel Principal</p>
							</a>
						</li>
						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Herramientas</h4>
						</li>
						<li class="nav-item">
							<a data-toggle="collapse" href="#base">
								<i class="fas fa-child"></i>
								<p>Estudiantes</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="base">
								<ul class="nav nav-collapse">
									<li>
										<a href="<?php
                                                    // Decidir el destino del enlace dependiendo del rol del usuario
                                                    if ($rolUsuario == '2') {
                                                        echo '../../Estudiantes/Tecnicatura/ABM_estudiante/nuevo_estudiante.php';
                                                    } else if ($rolUsuario == '1') {
                                                        echo '../../Estudiantes/Tecnicatura/ABM_estudiante/nuevo_estudiante_preceptor.php';
                                                    } else {
                                                        echo '../../Estudiantes/Tecnicatura/ABM_estudiante/nuevo_estudiante.php';
                                                    }
                                                ?>">
											<span class="sub-item">Nuevo Estudiante</span>
										</a>
									</li>
									<li>
										<a href="../../Estudiantes/FP/nuevo_estudianteFP.php">
											<span class="sub-item">Nuevo Estudiante FP</span>
										</a>
									</li>
									<li>
										<a href="../../Estudiantes/Tecnicatura/lista_estudiantes.php">
											<span class="sub-item">Lista Estudiantes</span>
										</a>
									</li>
									<li>
										<a href="../../Estudiantes/FP/lista_estudianteFP.php">
											<span class="sub-item">Lista Estudiantes FP</span>
										</a>
									</li>
									<li>
										<a href="../../Estudiantes/Tecnicatura/Informes/informe_asistencia_tecnicaturas.php">
											<span class="sub-item">Informe de Asistencias Técnicaturas</span>
										</a>
									</li>
									<li>
										<a href="#nohay">
											<span class="sub-item">Informe de Asistencias FP</span>
										</a>
									</li>
									<li>
										<a href="../../Estudiantes/Tecnicatura/Informes/informe_lista_estudiantes.php">
											<span class="sub-item">Imprimir Lista de Estudiantes Técnicaturas</span>
										</a>
									</li>
									<li>
										<a href="../../Estudiantes/FP/informesFP/informe_lista_estudiantesFP.php">
											<span class="sub-item">Imprimir Lista de Estudiantes FP</span>
										</a>
									</li>
									<li>
										<a href="../../Estudiantes/Tecnicatura/Retirados/estudiantes_retirados.php">
											<span class="sub-item">Retirados Antes de Tiempo</span>
										</a>
									</li>
								</ul>
							</div>
						</li>
						<li class="nav-item">
                			<a data-toggle="collapse" href="#takeAttendance">
                    			<i class="fas fa-pen-square"></i>
                    			<p>Tomar Asistencia</p>
                    			<span class="caret"></span>
                			</a>
                			<div class="collapse" id="takeAttendance">
								
                    <ul class="nav nav-collapse">
                        <li>
                            <a href="../../index_asistencia.php">
                                <span class="sub-item">Estudiantes Técnicaturas</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../Estudiantes/FP/ver_FPS.php">
                                <span class="sub-item">Estudiantes FP</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a data-toggle="collapse" href="#viewAttendance">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Ver Asistencia</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse" id="viewAttendance">
                    <ul class="nav nav-collapse">
                        <li>
                            <a href="../../ver_carreras.php">
                                <span class="sub-item">Estudiantes Técnicaturas</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../Estudiantes/FP/ver_asistenciaFPS.php">
                                <span class="sub-item">Estudiantes FP</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
						
						<li class="nav-item active submenu">
							<a data-toggle="collapse" href="#tables">
								<i class="fas fa-chalkboard-teacher"></i>
								<p>Profesores</p>
								<span class="caret"></span>
							</a>
							<div class="collapse show" id="tables">
								<ul class="nav nav-collapse">
									<li>
										<a href="./alta_docente.php">
											<span class="sub-item">Alta Docentes</span>
										</a>
									</li>
									<li>
										<a href="./lista_profesores.php">
											<span class="sub-item">Lista de Docentes</span>
										</a>
									</li>
									<li class="active">
										<a href="#">
											<span class="sub-item">Asignar Materia a Profesor</span>
										</a>
									</li>	
								</ul>
							</div>
						</li>

						<li class="nav-item">
                			<a data-toggle="collapse" href="#preceptors">
                    		<i class="fas fa-user-friends"></i>
                    		<p>Preceptores</p>
                    		<span class="caret"></span>
                			</a>
                			<div class="collapse" id="preceptors">
                    <ul class="nav nav-collapse">
                        <li>
                            <a href="./Administracion/Preceptores/herramienta.php">
                                <span class="sub-item">Nuevo Preceptor</span>
                            </a>
                        </li>
                        <li>
                            <a href="./Administracion/Preceptores/">
                                <span class="sub-item">Lista de Preceptores</span>
                            </a>
                        </li>
                       
						<li>
							<a href="">
								<span class="sub-item">Asignar carrera a Preceptor</span>
							</a>
						</li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a data-toggle="collapse" href="#alumnos">
                    <i class="fas fa-user-graduate"></i>
                    <p>Sistema Estudiantes</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse" id="alumnos">
                    <ul class="nav nav-collapse">
					<li>
						<a href="../../Administracion/Profesores/acta_volante_materias.php">
							<span class="sub-item">Actas Volantes</span>
						</a>
					</li>
						
		</div>
		<!-- End Sidebar -->
	</div>
	
</div>
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
            <table id="availableMaterias">
                <!-- Las materias disponibles se cargarán aquí -->
            </table>
            <button id="inscribirMateriasBtn" style=" width: 19%;">  <i class="far fa-arrow-alt-circle-left"></i></button>
        </form>
    </div>
</div>
</div>

<!--   Core JS Files   -->
<script src="../../assets/js/core/jquery.3.2.1.min.js"></script>
<script src="../../assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="../../assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Azzara JS -->
<script src="../../assets/js/ready.min.js"></script>

<script>
$(document).ready(function() {
    // Carga inicial de carreras
    $.ajax({
        url: 'getCarreras.php',
        type: 'GET',
        dataType: 'json',
        success: function(carreras) {
            var select = $('#careerSelect');
            carreras.forEach(function(carrera) {
                select.append('<option value="' + carrera.idCarrera + '">' + carrera.nombre_carrera + '</option>');
            });
        }
    });

    var lastSelectedProfessorId = null;

    $('#professorsTable').on('change', 'input[type="checkbox"][name="professorSelect"]', function() {
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

    $('#formAssignedMaterias').on('submit', function(e) {
        e.preventDefault();

        $('#assignedMaterias tr').each(function() {
            var materiaId = $(this).attr('data-id');
            $(this).find('td.day-cell').each(function() {
                var checkbox = $(this).find('input[type="checkbox"]');
                var dayId = checkbox.val();
                var entrada = $(this).find(`input[name="entrada_${dayId}_${materiaId}"]`).val();
                var salida = $(this).find(`input[name="salida_${dayId}_${materiaId}"]`).val();

                if (checkbox.is(':checked')) {
                    if (entrada && salida) {
                        $.ajax({
                            url: 'guardar_dias_semana_profe.php',
                            type: 'POST',
                            data: {
                                materiaId: materiaId,
                                diaId: dayId,
                                entrada: entrada,
                                salida: salida
                            },
                            success: function(response) {
                                alert('Respuesta: ' + JSON.stringify(response));
                                console.log('Datos guardados para el día ' + dayId + ' de la materia ' + materiaId, response);
                                location.reload(); // Recargar la página para reflejar los cambios
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al guardar datos: ' + error, xhr);
                            }
                        });
                    }
                } else {
                    $.ajax({
                        url: 'borrar_dias_semana_profe.php',
                        type: 'POST',
                        data: {
                            materiaId: materiaId,
                            diaId: dayId
                        },
                        success: function(response) {
                            console.log('Datos eliminados para el día ' + dayId + ' de la materia ' + materiaId, response);
                            location.reload(); // Recargar la página para reflejar los cambios
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al eliminar datos: ' + error, xhr);
                        }
                    });
                }
            });
        });
    });

    function cargarMateriasAsignadas(profesorId) {
        $.ajax({
            url: 'getMateriasAsignadas.php',
            type: 'POST',
            data: { profesorId: profesorId },
            dataType: 'json',
            success: function(materias) {
                var table = $('#assignedMaterias tbody');
                table.empty();
                materias.forEach(function(materia) {
                    var days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                    var dayIds = [1, 2, 3, 4, 5];
                    var daysCells = dayIds.map(dayId => `
                        <td class="day-cell">
                            <input type="checkbox" id="dia_${dayId}_${materia.idMaterias}" name="dia_${dayId}_${materia.idMaterias}" ${materia.dias && materia.dias[dayId] ? 'checked' : ''} value="${dayId}">
                            <label for="dia_${dayId}_${materia.idMaterias}"></label><br>
                            <h4 style="font-size: 10px;">Entrada</h4>
                            <input type="time"  class="input-hora" value="${materia.dias && materia.dias[dayId] ? materia.dias[dayId].horario_entrada : ''}" name="entrada_${dayId}_${materia.idMaterias}" placeholder="Entrada">
                            <h4 style="font-size: 10px;">Salida</h4>
                            <input type="time"  class="input-hora" value="${materia.dias && materia.dias[dayId] ? materia.dias[dayId].horario_salida : ''}" name="salida_${dayId}_${materia.idMaterias}" placeholder="Salida">
                        </td>
                    `).join('');

                    var row = `<tr data-id="${materia.idMaterias}">
                        <td>${materia.nombre} (${materia.nombre_carrera})</td>
                        <td><input type="checkbox" ${materia.estado == 1 ? 'checked' : ''}></td>
                        ${daysCells}
                    </tr>`;
                    table.append(row);
                });
                if (materias.length === 0) {
                    table.append('<tr><td colspan="7">No hay materias asignadas.</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar materias: ", status, error);
                $('#assignedMaterias tbody').append('<tr><td colspan="7">Error al cargar las materias asignadas.</td></tr>');
            }
        });
    }

    $('#careerSelect').on('change', function() {
        var carreraId = $(this).val();
        $.ajax({
            url: 'getMateriasPorCarrera.php',
            type: 'POST',
            data: { carreraId: carreraId },
            dataType: 'json',
            success: function(materias) {
                var table = $('#availableMaterias');
                table.empty();
                materias.forEach(function(materia) {
                    table.append('<tr data-id="' + materia.idMaterias + '"><td>' + materia.nombre + '</td><td style="width: 30px;" ><input type="checkbox"></td></tr>');
                });
            }
        });
    });

    // Asegura que solo se pueda seleccionar un checkbox a la vez en la tabla de profesores
    $('#professorsTable').on('change', 'input[type="checkbox"][name="professorSelect"]', function() {
        $('input[name="professorSelect"]').not(this).prop('checked', false);
    });

    // Maneja el evento de clic en el botón para inscribir materias
    $('#inscribirMateriasBtn').on('click', function(e) {
        e.preventDefault();  // Previene el comportamiento por defecto del formulario

        // Obtiene el ID del profesor seleccionado
        var profesorId = $('input[name="professorSelect"]:checked').val();
        if (!profesorId) {
            alert('Por favor, seleccione un profesor antes de asignar materias.');
            return;
        }

        // Obtiene los IDs de las materias seleccionadas
        var checkedMaterias = $('#availableMaterias input[type="checkbox"]:checked');
        if (checkedMaterias.length === 0) {
            alert('Por favor, seleccione al menos una materia para asignar.');
            return;
        }

        // Procesa cada materia seleccionada
        checkedMaterias.each(function() {
            var materiaId = $(this).closest('tr').data('id'); // Asegúrate de que cada fila tenga un `data-id` correspondiente

            // Envía la solicitud para asignar la materia al profesor
            $.ajax({
                url: 'asignar_materiasprofe.php',
                type: 'POST',
                data: { profesorId: profesorId, materiaId: materiaId },
                success: function(response) {
                    alert('Respuesta: ' + JSON.stringify(response));
                    console.log(response);
                    
                },
                error: function(xhr, status, error) {
                    alert('Error al asignar materias: ' + error);
                }
            });
        });
    });

    $('#formAssignedMaterias').on('submit', function(e) {
        e.preventDefault(); // Previene el comportamiento por defecto del formulario

        var updates = [];
        $('#assignedMaterias tr').each(function() {
            var materiaId = $(this).data('id');
            var isChecked = $(this).find('input[type="checkbox"]:first').is(':checked');

            if (!isChecked) { // Solo agregar a la lista si el checkbox está desmarcado
                updates.push({
                    materiaId: materiaId,
                    desmarcado: true
                });
            }
        });

        if (updates.length > 0) {
            $.ajax({
                url: 'updateMaterias.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ updates: updates }),
                success: function(response) {
                    
                    console.log(response);
                    location.reload(); // Recargar la página para reflejar los cambios
                },
                error: function(xhr, status, error) {
                    alert('Error al actualizar materias: ' + error);
                }
            });
        }
    });

    var myTable = document.querySelector("#professorsTable");
    var dataTable = new DataTable(myTable, {
        perPage: 1000,
        perPageSelect: [1000]
    });
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Escucha cambios en los checkboxes dentro de la tabla de profesores
    $('#professorsTable').on('change', 'input[type="checkbox"][name="professorSelect"]', function() {
        var isChecked = $(this).is(':checked');
        
        // Desmarca todos los demás checkboxes
        $('input[name="professorSelect"]').not(this).prop('checked', false);

        // Si se desmarca el checkbox actual, no hay necesidad de desmarcar otros
        if (!isChecked) {
            return;
        }

        // Opcional: puedes realizar acciones adicionales aquí si es necesario
    });
});



</script>

</body>
</html>

