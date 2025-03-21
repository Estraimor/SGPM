<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../login/login.php');
    exit;
}

// Verificación de la contraseña específica "0123456789"
if (isset($_SESSION["contraseña"]) && $_SESSION["contraseña"] === "0123456789") {
    header('Location: cambio_contrasena_profe.php');
    exit;
}

// Asumimos que también almacenas el rol en la sesión.
$rolUsuario = $_SESSION["roles"];

// Definimos los roles permitidos para esta página.
$rolesPermitidos = ['1', '2', '3', '4','5'];

// Verificar si el rol del usuario está en la lista de roles permitidos.
if (!in_array($rolUsuario, $rolesPermitidos)) {
    echo "<script>alert('Acceso restringido a esta página.');</script>";
    // Opcional: redirigir al usuario a otra página
    // header('Location: pagina_principal.php');
    exit; // Detener la ejecución del script.
}

$idPreceptor = $_SESSION['id'];
include '../conexion/conexion.php';



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
	<link rel="manifest" href="../manifest.json">
    
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
									<?php $idProfesor = $_SESSION["id"];
$sql = "SELECT avatar FROM profesor WHERE idProrfesor = '$idProfesor'";
$result = mysqli_query($conexion, $sql);

$imagenBase64 = ""; // Inicializar la variable para la imagen en caso de que no haya una

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $avatar = $row['avatar'];

    if ($avatar) {
        // Convertir la imagen binaria a base64
        $imagenBase64 = 'data:image/jpeg;base64,' . base64_encode($avatar);
    } else {
        // Usar la imagen por defecto si no hay avatar en la base de datos
        $imagenBase64 = 'assets/img/1361728.png';
    }
} else {
    echo "Error al cargar la imagen.";
}


?>

<!-- HTML para mostrar la imagen -->
<?php if ($imagenBase64 === 'assets/img/1361728.png') : ?>
    <!-- Mostrar imagen por defecto si no hay avatar -->
    <img src="assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;">
<?php else : ?>
    <!-- Mostrar imagen del avatar en base64 -->
    <img src="<?php echo $imagenBase64; ?>" alt="Avatar" class="avatar-img rounded-circle">
<?php endif; ?>
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
							<li>
								<div class="user-box">
									<div class="avatar-lg">
									    <?php $idProfesor = $_SESSION["id"];
$sql = "SELECT avatar FROM profesor WHERE idProrfesor = '$idProfesor'";
$result = mysqli_query($conexion, $sql);

$imagenBase64 = ""; // Inicializar la variable para la imagen en caso de que no haya una

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $avatar = $row['avatar'];

    if ($avatar) {
        // Convertir la imagen binaria a base64
        $imagenBase64 = 'data:image/jpeg;base64,' . base64_encode($avatar);
    } else {
        // Usar la imagen por defecto si no hay avatar en la base de datos
        $imagenBase64 = 'assets/img/1361728.png';
    }
} else {
    echo "Error al cargar la imagen.";
}


?>

<!-- HTML para mostrar la imagen -->
<?php if ($imagenBase64 === 'assets/img/1361728.png') : ?>
    <!-- Mostrar imagen por defecto si no hay avatar -->
    <img src="assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;">
<?php else : ?>
    <!-- Mostrar imagen del avatar en base64 -->
    <img src="<?php echo $imagenBase64; ?>" alt="Avatar" class="avatar-img rounded-circle">
<?php endif; ?>
									    </div>
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
									<a class="dropdown-item" href="Perfil.php">Mi Perfil</a>
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
						<li class="nav-item active">
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
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
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
										<a href="./Estudiantes/Tecnicatura/lista_estudiantes_2025.php">
											<span class="sub-item">Lista Estudiantes 2025</span>
										</a>
									</li>
									<?php endif; ?>
									
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
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
									<li>
										<a href="./Estudiantes/Tecnicatura/Falta_justificada/falta_justificada.php">
											<span class="sub-item">Justificar Falta</span>
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
                <a data-toggle="collapse" href="#alumnos">
                    <i class="fas fa-file-alt"></i>
                    <p>Utilidades<br> Administrativas</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse" id="alumnos">
                    <ul class="nav nav-collapse">
					<?php if ($rolUsuario == '1'): ?>
					<li>
						<a href="./gestionar_mesas_finales.php">
							<span class="sub-item">Gestión de Mesas</span>
						</a>
					</li>
					<li>
						<a href="./ver_inscriptos_mesas.php">
							<span class="sub-item">Contador de Inscriptos a Mesas</span>
						</a>
					</li>
					
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
						<?php endif; ?>
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
									
									<?php if ($rolUsuario == '1'): ?>
									<li>
										<a href="./Estudiantes/Tecnicatura/pagos.php">
											<span class="sub-item">Estado de Pagos de Estudiantes</span>
										</a>
									</li>
									<?php endif; ?>	
                       
                    </ul>
                </div>
            </li>
			<?php endif; ?>
			<?php if ($rolUsuario == '4' || $rolUsuario == '1'|| $rolUsuario == '5'): ?>
			<li class="nav-item">
                <a data-toggle="collapse" href="#newMenu">
                    <i class="fas fa-book"></i>
                    <p>Utilidades<br> del Docentes</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse" id="newMenu">
                    <ul class="nav nav-collapse">
                        <li>
                            <a href="./Administracion/Profesores/pre_parciales.php">
                                <span class="sub-item">Gestión de Notas</span>
                            </a>
                        </li>
						<li>
                            <a href="./pre_libro.php">
                                <span class="sub-item">Libro de Temas</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
			<?php endif; ?>
			<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
<li class="nav-item">
    <a data-toggle="collapse" href="#preceptorUtilities">
        <i class="fas fa-toolbox"></i>
        <p>Utilidades<br> del Preceptor</p>
        <span class="caret"></span>
    </a>
    <div class="collapse" id="preceptorUtilities">
        <ul class="nav nav-collapse">
            <li>
                <a href="./Administracion/Profesores/pre_lista_promocionados.php">
                    <span class="sub-item">Actas Volantes Promocionados</span>
                </a>
            </li>
			<li>
                <a href="./actas_volante_estudiantes_regulares.php">
                    <span class="sub-item">Actas Volantes Regulares</span>
                </a>
            </li>
			<li>
                <a href="./actas_volante_estudiantes_libres.php">
                    <span class="sub-item">Actas Volantes Libres</span>
                </a>
            </li>
            <li>
                <a href="./proximamente.php">
                    <span class="sub-item">Gestión de Comunicados</span>
                </a>
            </li>
			<li>
                <a href="./pre_nota_final.php">
                    <span class="sub-item">Cargar Notas de Mesas</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="sub-item">Notas Pendientes</span>
                </a>
            </li>
        </ul>
    </div>
</li>
<?php endif; ?>
			<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
			<li class="nav-item">
    <a data-toggle="collapse" href="#preinscriptos">
        <i class="fas fa-user-plus"></i>
        <p>Preinscriptos</p>
        <span class="caret"></span>
    </a>
    <div class="collapse" id="preinscriptos">
        <ul class="nav nav-collapse">
            <li>
                <a href="./lista_pre_inscriptos.php">
                    <span class="sub-item">Lista de Preinscriptos</span>
                </a>
            </li>
            
        </ul>
    </div>
	<?php endif; ?>
</li>
		</div>
		<!-- End Sidebar -->
	</div>
	
</div>
<div class="contenido">
    <h2>Registro de Notas Finales</h2>

    <label>Carrera:</label>
    <select id="carrera">
        <option value="">Seleccione una carrera</option>
    </select><br>

    <label>Curso:</label>
    <select id="curso" disabled>
        <option value="">Seleccione un curso</option>
    </select><br>

    <label>Comisión:</label>
    <select id="comision" disabled>
        <option value="">Seleccione una comisión</option>
    </select><br>

    <label>Materia:</label>
    <select id="materiaSeleccionada" disabled>
        <option value="">Seleccione una materia</option>
    </select><br>

    <label>Turno:</label>
    <select id="turno" disabled>
        <option value="">Seleccione un turno</option>
    </select><br>

    <h3>Lista de Estudiantes</h3>
    <div id="tablaNotas" class="tabla-container"></div>

    <button id="guardarNotas" style="margin-top: 20px;">Guardar Notas</button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Lista de materias cuatrimestrales (IDs) para actualizar turnos
    const materiasCuatrimestrales = ['146', '415', '426', '193', '444', '453'];

    // Función para actualizar las opciones del turno basado en si la materia es cuatrimestral
    function actualizarTurnos(isCuatrimestral) {
        const opcionesTurno = isCuatrimestral
            ? [
                { value: "1", text: "Turno 1 (Julio - Agosto)" },
                { value: "2", text: "Turno 2 (Noviembre - Diciembre)" },
                { value: "3", text: "Turno 3 (Febrero - Marzo)" },
                { value: "4", text: "Turno 4 (Julio - Agosto)" },
                { value: "5", text: "Turno 5 (Noviembre - Diciembre)" },
                { value: "6", text: "Turno 6 (Febrero - Marzo)" },
                { value: "7", text: "Turno 7 (Julio - Agosto)" }
              ]
            : [
                { value: "1", text: "Turno 1 (Noviembre - Diciembre)" },
                { value: "2", text: "Turno 2 (Febrero - Marzo)" },
                { value: "3", text: "Turno 3 (Julio - Agosto)" },
                { value: "4", text: "Turno 4 (Noviembre - Diciembre)" },
                { value: "5", text: "Turno 5 (Febrero - Marzo)" },
                { value: "6", text: "Turno 6 (Julio - Agosto)" },
                { value: "7", text: "Turno 7 (Noviembre - Diciembre)" }
              ];

        $('#turno').html('<option value="">Seleccione un turno</option>');
        opcionesTurno.forEach(opc => {
            $('#turno').append(`<option value="${opc.value}">${opc.text}</option>`);
        });
    }

    // Cargar carreras al iniciar
    $.post('./config_notas_pendientes/obtener_carreras.php', function(data) {
        $('#carrera').html('<option value="">Seleccione una carrera</option>' + data);
    });

    // Cargar cursos al seleccionar una carrera
    $('#carrera').on('change', function() {
        $('#tablaNotas1, #tablaNotas2').html('');
        $('#curso').prop('disabled', false);
        let carrera = $(this).val();
        $.post('./config_notas_pendientes/obtener_cursos.php', { carrera: carrera }, function(data) {
            $('#curso').html('<option value="">Seleccione un curso</option>' + data);
        });
    });

    // Cargar comisiones al seleccionar un curso
    $('#curso').on('change', function() {
        $('#tablaNotas1, #tablaNotas2').html('');
        let idCarrera = $('#carrera').val();
        let idCurso = $(this).val();
        $.post('./config_notas_pendientes/obtener_comisiones.php', { idCarrera, idCurso }, function(data) {
            $('#comision').html('<option value="">Seleccione una comisión</option>' + data);
            $('#comision').prop('disabled', false);
        });
    });

    // Cargar materias al seleccionar una comisión
    $('#comision').on('change', function() {
        $('#tablaNotas1, #tablaNotas2').html('');
        $('#materiaSeleccionada').prop('disabled', false);
        let carrera = $('#carrera').val();
        let curso = $('#curso').val();
        let comision = $('#comision').val();
        $.post('./config_notas_pendientes/obtener_materias.php', { carrera, curso, comision }, function(data) {
            $('#materiaSeleccionada').html('<option value="">Seleccione una materia</option>' + data);
        });
    });

    $('#materiaSeleccionada').on('change', function() {
        let idMateria = $(this).val();
        let isCuatrimestral = materiasCuatrimestrales.includes(idMateria);
        actualizarTurnos(isCuatrimestral);
        $('#turno').prop('disabled', false);
    });

    $('#turno').on('change', function() {
        let idMateria = $('#materiaSeleccionada').val();
        let comision = $('#comision').val();
        let curso = $('#curso').val();
        let carrera = $('#carrera').val();
        let turno = $('#turno').val();

        if (!idMateria || !comision || !curso || !carrera || !turno) {
            $('#tablaNotas').html('<p style="color:red;">Debe seleccionar todos los campos antes de continuar.</p>');
            return;
        }

        $.post('./config_notas_pendientes/obtener_estudiantes_materia.php', { idMateria, comision, curso, carrera, turno }, function(data) {
            if (data.error) {
                $('#tablaNotas').html(`<p style="color:red;">Error al cargar los datos: ${data.error}</p>`);
                return;
            }

            if (Array.isArray(data)) {
                let tableHtml = `<table border="1">
                    <thead>
                        <tr>
                            <th>Legajo</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th colspan="3">Primer Llamado</th>
                            <th>Bloquear</th>
                            <th colspan="3">Segundo Llamado</th>
                            <th>Bloquear</th>
                        </tr>
                        <tr>
                            <th></th><th></th><th></th>
                            <th>Nota</th><th>Tomo</th><th>Folio</th>
                            <th></th>
                            <th>Nota</th><th>Tomo</th><th>Folio</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>`;

                data.forEach(est => {
                    tableHtml += `<tr>
                        <td>${est.legajo}</td>
                        <td>${est.nombre}</td>
                        <td>${est.apellido}</td>
                        <td><input type="number" class="notaFinal1" data-legajo="${est.legajo}" step="0.1"></td>
                        <td><input type="text" class="tomoFinal1" data-legajo="${est.legajo}"></td>
                        <td><input type="text" class="folioFinal1" data-legajo="${est.legajo}"></td>
                        <td><input type="checkbox" class="bloquear1" data-legajo="${est.legajo}"></td>
                        <td><input type="number" class="notaFinal2" data-legajo="${est.legajo}" step="0.1"></td>
                        <td><input type="text" class="tomoFinal2" data-legajo="${est.legajo}"></td>
                        <td><input type="text" class="folioFinal2" data-legajo="${est.legajo}"></td>
                        <td><input type="checkbox" class="bloquear2" data-legajo="${est.legajo}"></td>
                    </tr>`;
                });

                tableHtml += '</tbody></table>';
                $('#tablaNotas').html(tableHtml);
            }
        }, 'json');
    });

    // Bloquear Tomo y Folio
    $(document).on('change', '.bloquear1', function() {
        let legajo = $(this).data('legajo');
        $(`.tomoFinal1[data-legajo="${legajo}"], .folioFinal1[data-legajo="${legajo}"]`).prop('disabled', this.checked);
    });

    $(document).on('change', '.bloquear2', function() {
        let legajo = $(this).data('legajo');
        $(`.tomoFinal2[data-legajo="${legajo}"], .folioFinal2[data-legajo="${legajo}"]`).prop('disabled', this.checked);
    });

    $('#guardarNotas').on('click', function() {
    let comision = $('#comision').val();
    let curso = $('#curso').val();
    let carrera = $('#carrera').val();
    let materia = $('#materiaSeleccionada').val();
    let turno = $('#turno').val();
    let estudiantes = [];

    if (!comision || !curso || !carrera || !materia || !turno) {
        alert("⚠️ Debe seleccionar todos los campos antes de enviar.");
        return;
    }

    $('.notaFinal1, .notaFinal2').each(function() {
        let legajo = $(this).data('legajo');
        let nota1 = $(`.notaFinal1[data-legajo="${legajo}"]`).val().trim();
        let tomo1 = $(`.tomoFinal1[data-legajo="${legajo}"]`).val().trim();
        let folio1 = $(`.folioFinal1[data-legajo="${legajo}"]`).val().trim();
        let bloquear1 = $(`.bloquear1[data-legajo="${legajo}"]`).is(':checked');

        let nota2 = $(`.notaFinal2[data-legajo="${legajo}"]`).val().trim();
        let tomo2 = $(`.tomoFinal2[data-legajo="${legajo}"]`).val().trim();
        let folio2 = $(`.folioFinal2[data-legajo="${legajo}"]`).val().trim();
        let bloquear2 = $(`.bloquear2[data-legajo="${legajo}"]`).is(':checked');

        let llamado = bloquear2 ? null : 2; // Si el checkbox está marcado, se guarda NULL

        let estudiante = {
            legajo,
            nota1: (nota1 !== '' && !isNaN(nota1) && nota1 >= 0 && nota1 <= 10) ? nota1 : null,
            tomo1: bloquear1 ? null : tomo1 || null,
            folio1: bloquear1 ? null : folio1 || null,
            nota2: (nota2 !== '' && !isNaN(nota2) && nota2 >= 0 && nota2 <= 10) ? nota2 : null,
            tomo2: bloquear2 ? null : tomo2 || null,
            folio2: bloquear2 ? null : folio2 || null,
            llamado
        };

        estudiantes.push(estudiante);
    });

    $.post('./config_notas_pendientes/guardar_nota_mesas_pendientes.php', {
        comision, curso, carrera, materia, turno, estudiantes
    }, function(response) {
        alert(response.success ? "✅ Notas guardadas correctamente." : "❌ Error al guardar notas.");
    }, 'json');
});
});
</script>

<!--   Core JS Files   -->


<script src="assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>


<!-- jQuery Scrollbar -->
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Azzara JS -->
<script src="assets/js/ready.min.js"></script>



</body>
</html>

