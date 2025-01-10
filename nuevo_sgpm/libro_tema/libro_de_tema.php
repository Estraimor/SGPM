<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../login/login.php');
    exit;
}

// Asumimos que también almacenas el rol en la sesión.
$rolUsuario = $_SESSION["roles"];

// Definimos los roles permitidos para esta página.
$rolesPermitidos = ['1', '2', '3', '4'];

// Verificar si el rol del usuario está en la lista de roles permitidos.
if (!in_array($rolUsuario, $rolesPermitidos)) {
    echo "<script>alert('Acceso restringido a esta página.');</script>";
    // Opcional: redirigir al usuario a otra página
    // header('Location: pagina_principal.php');
    exit; // Detener la ejecución del script.
}

$idPreceptor = $_SESSION['id'];
include '../conexion/conexion.php';

// Consulta para obtener las carreras asociadas al preceptor
$queryCarreras = "SELECT p.carreras_idCarrera, c.nombre_carrera FROM preceptores p
                  INNER JOIN carreras c ON c.idCarrera = p.carreras_idCarrera
                  WHERE p.profesor_idProrfesor = $idPreceptor";
$resultCarreras = mysqli_query($conexion, $queryCarreras);
$carreras = mysqli_fetch_all($resultCarreras, MYSQLI_ASSOC);

// Set inactivity limit in seconds
$inactivity_limit = 1200;

// Check if the user has been inactive for too long
if (isset($_SESSION['time']) && (time() - $_SESSION['time'] > $inactivity_limit)) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php");
    exit;
} else {
    $_SESSION['time'] = time();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>SGPM</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="assets/img/Logo ISPM 2 transparante.png" type="image/x-icon"/>

	<!-- Fonts and icons -->
	<script src="assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Open+Sans:300,400,600,700"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"], urls: ['./assets/css/fonts.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/azzara.min.css">
	<link rel="stylesheet" href="assets/css/estilos.css">

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="assets/css/demo.css">
</head>
<body>
	<div class="wrapper">
		
		<div class="main-header" data-background-color="red">
			<div class="logo-header">
				
				<a href="index.php" class="logo">
					<img src="assets/img/Logo ISPM 2 transparante.png" width="45px" alt="navbar brand" class="navbar-brand">
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
									<img src="assets/img/1361728.png" alt="..." class="avatar-img rounded-circle" >
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
							<li>
								<div class="user-box">
									<div class="avatar-lg"><img src="assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;"></div>
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
									<a class="dropdown-item" href="proximamente.php">Mi Perfil</a>
									<a class="dropdown-item" href="proximamente.php">Cambiar Contraseña</a>
									<a class="dropdown-item" href="../login/cerrar_sesion.php">Cerrar Sesión</a>
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
							<a href="index.php">
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
						<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
						<li class="nav-item">
							<a data-toggle="collapse" href="#base">
								<i class="fas fa-child"></i>
								<p>Estudiantes</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="base">
								<ul class="nav nav-collapse">
									<?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
									<li>
										<a href="./Estudiantes/Tecnicatura/ABM_estudiante/nuevo_estudiante.php">
											<span class="sub-item">Nuevo Estudiante</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="./Estudiantes/FP/nuevo_estudianteFP.php">
											<span class="sub-item">Nuevo Estudiante FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="./Estudiantes/Tecnicatura/lista_estudiantes.php">
											<span class="sub-item">Lista Estudiantes</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="./Estudiantes/FP/lista_estudianteFP.php">
											<span class="sub-item">Lista Estudiantes FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="./Estudiantes/Tecnicatura/Informes/informe_asistencia_tecnicaturas.php">
											<span class="sub-item">Informe de Asistencias Técnicaturas</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="proximamente.php">
											<span class="sub-item">Informe de Asistencias FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="./Estudiantes/Tecnicatura/Informes/informe_lista_estudiantes.php">
											<span class="sub-item">Imprimir Lista de Estudiantes Técnicaturas</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="./Estudiantes/FP/informesFP/informe_lista_estudiantesFP.php">
											<span class="sub-item">Imprimir Lista de Estudiantes FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="./Estudiantes/Tecnicatura/Retirados/estudiantes_retirados.php">
											<span class="sub-item">Retirados Antes de Tiempo</span>
										</a>
									</li>
								</ul>
							</div>
						</li>
						<?php endif; ?>
						<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
						<li class="nav-item">
                			<a data-toggle="collapse" href="#takeAttendance">
                    			<i class="fas fa-pen-square"></i>
                    			<p>Tomar Asistencia</p>
                    			<span class="caret"></span>
                			</a>
                			<div class="collapse" id="takeAttendance">
								
                    <ul class="nav nav-collapse">
					<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
                        <li>
                            <a href="index_asistencia.php">
                                <span class="sub-item">Estudiantes Técnicaturas</span>
                            </a>
                        </li>
						<?php endif; ?>
					<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                        <li>
                            <a href="./Estudiantes/FP/ver_FPS.php">
                                <span class="sub-item">Estudiantes FP</span>
                            </a>
                        </li>
						<?php endif; ?>
                    </ul>
                </div>
            </li>
			<?php endif; ?>
			<?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
            <li class="nav-item">
                <a data-toggle="collapse" href="#viewAttendance">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Ver Asistencia</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse" id="viewAttendance">
                    <ul class="nav nav-collapse">
						<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
                        <li>
                            <a href="ver_carreras.php">
                                <span class="sub-item">Estudiantes Técnicaturas</span>
                            </a>
                        </li>
						<?php endif; ?>
						<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                        <li>
                            <a href="./Estudiantes/FP/ver_asistenciaFPS.php">
                                <span class="sub-item">Estudiantes FP</span>
                            </a>
                        </li>
						<?php endif; ?>
                    </ul>
                </div>
            </li>
			<?php endif; ?>
				<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
						<li class="nav-item">
							<a data-toggle="collapse" href="#tables">
								<i class="fas fa-chalkboard-teacher"></i>
								<p>Profesores</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="tables">
								<ul class="nav nav-collapse">
									<li>
										<a href="./Administracion/Profesores/alta_docente.php">
											<span class="sub-item">Alta Docentes</span>
										</a>
									</li>
									<li>
										<a href="./Administracion/Profesores/lista_profesores.php">
											<span class="sub-item">Lista de Docentes</span>
										</a>
									</li>
									<li>
										<a href="./Administracion/Profesores/materia_profesor.php">
											<span class="sub-item">Asignar Materia a Profesor</span>
										</a>
									</li>	
									
								</ul>
							</div>
						</li>
						<?php endif; ?>
						<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
						<li class="nav-item">
                			<a data-toggle="collapse" href="#preceptors">
                    		<i class="fas fa-user-friends"></i>
                    		<p>Preceptores</p>
                    		<span class="caret"></span>
                			</a>
                			<div class="collapse" id="preceptors">
                    <ul class="nav nav-collapse">
                        <li>
                            <a href="proximamente.php">
                                <span class="sub-item">Nuevo Preceptor</span>
                            </a>
                        </li>
                        <li>
                            <a href="proximamente.php">
                                <span class="sub-item">Lista de Preceptores</span>
                            </a>
                        </li>
                       
						<li>
							<a href="proximamente.php">
								<span class="sub-item">Asignar carrera a Preceptor</span>
							</a>
						</li>
                    </ul>
                </div>
            </li>
			<?php endif; ?>
			<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
			<li class="nav-item">
                <a data-toggle="collapse" href="#alumnos">
                    <i class="fas fa-user-graduate"></i>
                    <p>Sistema Estudiantes</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse" id="alumnos">
                    <ul class="nav nav-collapse">
					<li>
						<a href="./Administracion/Profesores/acta_volante_materias.php">
							<span class="sub-item">Actas Volantes</span>
						</a>
					</li>
                       
                    </ul>
                </div>
            </li>
			<?php endif; ?>
			<?php if ($rolUsuario == '4'): ?>
			<li class="nav-item active">
                <a data-toggle="collapse" href="#newMenu">
                    <i class="fas fa-book"></i>
                    <p>Utilidades</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse show" id="newMenu">
                    <ul class="nav nav-collapse">
                        <li>
                            <a href="./trabajospracticos.php">
                                <span class="sub-item">Trabajos prácticos</span>
                            </a>
                        </li>
                        <li>
                            <a href="./parciales.php">
                                <span class="sub-item">Parciales</span>
                            </a>
                        </li>
						<li class="active">
                            <a href="#">
                                <span class="sub-item">Libro de Temas</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
			<?php endif; ?>
		</div>
		<!-- End Sidebar -->
	</div>
	
</div>
<div class="contenido">
<table id="tablaLibros" border="1">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Carrera</th>
            <th>Materia</th>
            <th>Capacidades</th>
            <th>Contenidos</th>
            <th>Evaluación</th>
            <th>Observaciones diarias</th>
            <th> Acciones </th>
        </tr>
    </thead>
    <tbody>
        
    </tbody>
</table>

<script>
    $(document).ready(function() {
        function cargarDatos() {
            $.ajax({
                url: 'ajax_libro_tema.php',
                method: 'GET',
                success: function(response) {
                    const data = JSON.parse(response);
                    const tbody = $('#tablaLibros tbody');
                    tbody.empty();
                    data.forEach(row => {
                        const newRow = $(`
                            <tr>
                                <td class="fecha">${row.fecha}</td>
                                <td>${row.nombre_carrera}</td>
                                <td>${row.materia_nombre}</td>
                                <td class="editable" data-campo="capacidades">${row.capacidades}</td>
                                <td class="editable" data-campo="contenidos">${row.contenidos}</td>
                                <td class="editable" data-campo="evaluacion">${row.evaluacion}</td>
                                <td class="editable" data-campo="observacion_diaria">${row.observacion_diaria}</td>
                                <td>
                                    <button class="btn-modificar">Modificar</button>
                                    <button class="btn-borrar">Borrar</button>
                                    <button type="submit" class="btn-actualizar" style="display: none;">Actualizar cambios</button>
                                    <input type="hidden" class="materia-id" value="${row.idMaterias}">
                                    <input type="hidden" class="carrera-id" value="${row.idCarrera}">
                                </td>
                            </tr>
                        `);
                        tbody.append(newRow);
                    });

                    // Delegación de eventos para los botones
                    tbody.on('click', '.btn-modificar', function() {
                        var fila = $(this).closest('tr');
                        fila.find('.editable').each(function() {
                            var contenido = $(this).text();
                            $(this).html('<input type="text" value="' + contenido + '">');
                        });
                        fila.find('.btn-modificar').hide();
                        fila.find('.btn-borrar').hide();
                        fila.find('.btn-actualizar').show();
                    });

                    tbody.on('click', '.btn-actualizar', function() {
                        var fila = $(this).closest('tr');
                        var datos = {
                            original_fecha: fila.find('.fecha').text(),
                            materia: fila.find('.materia-id').val(),
                            carrera: fila.find('.carrera-id').val(),
                            profesor: <?php echo $_SESSION['id']; ?>
                        };

                        fila.find('.editable').each(function() {
                            var campo = $(this).data('campo');
                            var contenido = $(this).find('input').val();
                            datos[campo] = contenido;
                            $(this).text(contenido);
                        });

                        $.ajax({
                            url: 'update_libro_tema.php',
                            method: 'POST',
                            data: datos,
                            success: function(response) {
                                console.log('Datos actualizados exitosamente');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al actualizar los datos:', error);
                            }
                        });

                        fila.find('.btn-actualizar').hide();
                        fila.find('.btn-modificar').show();
                        fila.find('.btn-borrar').show();
                    });

                    tbody.on('click', '.btn-borrar', function() {
                        var fila = $(this).closest('tr');
                        if (confirm('¿Está seguro de que desea borrar este registro?')) {
                            var datos = {
                                fecha: fila.find('.fecha').text(),
                                materia: fila.find('.materia-id').val(),
                                carrera: fila.find('.carrera-id').val(),
                                profesor: <?php echo $_SESSION['id']; ?>
                            };

                            $.ajax({
                                url: 'delete_libro_tema.php',
                                method: 'POST',
                                data: datos,
                                success: function(response) {
                                    fila.remove();
                                    console.log('Registro borrado exitosamente');
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error al borrar el registro:', error);
                                }
                            });
                        }
                    });

                    // Agregar fila de inputs dentro del formulario
                    tbody.append(`
                        <tr>
                            <td colspan="8">
                                <form id="nuevoLibroForm" action="guardar_libro_tema.php" method="post">
                                    <input type="hidden" name="profesor" value="<?php echo $_SESSION['id']; ?>">
                                    <select name="materia" id="materia" onchange="actualizarCarrera()">
                                        <option value="">Seleccione una materia</option>
                                        <?php
                                        $sql = "SELECT c.idCarrera, c.nombre_carrera, m.idMaterias, m.Nombre
                                                FROM materias m
                                                INNER JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
                                                INNER JOIN profesor p ON m.profesor_idProrfesor = p.idProrfesor
                                                WHERE p.idProrfesor = '{$_SESSION['id']}'";
                                        
                                        $query = mysqli_query($conexion, $sql);
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            echo "<option value=\"{$row['idMaterias']}\" data-carrera-id=\"{$row['idCarrera']}\">{$row['Nombre']} / {$row['nombre_carrera']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="carrera" id="carrera">
                                    <input type="text" name="capacidades" placeholder="Capacidades">
                                    <input type="text" name="contenidos" placeholder="Contenidos">
                                    <input type="text" name="evaluacion" placeholder="Evaluación">
                                    <input type="date" name="fecha">
                                    <input type="text" name="observacion" placeholder="Observación Diaria">
                                    <input type="submit" name="enviar" value="Enviar">
                                </form>
                            </td>
                        </tr>
                    `);

                    // Re-inicializar DataTables
                    var myTable = document.querySelector("#tablaLibros");
                    var dataTable = new DataTable(myTable);
                }
            });
        }

        // Llamar a la función para cargar los datos
        cargarDatos();
    });

    // Función para actualizar la carrera basada en la materia seleccionada
    function actualizarCarrera() {
        const selectMateria = document.getElementById('materia');
        const selectedOption = selectMateria.options[selectMateria.selectedIndex];
        const carreraId = selectedOption.getAttribute('data-carrera-id');
        document.getElementById('carrera').value = carreraId;
    }
</script>
    </div>


<!--   Core JS Files   -->
<script src="assets/js/core/jquery.3.2.1.min.js"></script>

<script src="assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>


<!-- jQuery Scrollbar -->
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Azzara JS -->
<script src="assets/js/ready.min.js"></script>



</body>
</html>

