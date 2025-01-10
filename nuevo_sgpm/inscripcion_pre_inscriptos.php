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
						<?php if ($rolUsuario == '1'): ?>
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
			<?php if ($rolUsuario == '4' || $rolUsuario == '1'): ?>
			<li class="nav-item">
                <a data-toggle="collapse" href="#newMenu">
                    <i class="fas fa-book"></i>
                    <p>Utilidades</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse" id="newMenu">
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
						<li>
                            <a href="./libro_de_tema.php">
                                <span class="sub-item">Libro de Temas</span>
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
            <li>
                <a href="./proximamente.php">
                    <span class="sub-item">Gestionar Preinscriptos</span>
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
<?php
// Obtener el DNI desde la URL
$dni = $_GET['DNI'];

// Realizar la consulta
$sql = "SELECT nombre, apellido, DNI, fecha_nacimiento, celular, correo FROM pre_inscripciones WHERE DNI = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $dni);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    // Obtener los datos
    $fila = $resultado->fetch_assoc();
} else {
    echo "No se encontraron datos para el DNI ingresado.";
}


?>
<div class="contenido">
    <h2 class="form-container__h2">Registro de Estudiante</h2>
    <form action="./Estudiantes/Tecnicatura/ABM_estudiante/guardar_estudiante.php" method="post" class="form-container">
        <div class="row">
            
            <div class="input-group">
            <label for="apellido_alu">Apellido:</label>
            <input type="text" class="form-container__input full" id="apellido_alu" name="apellido_alu" value="<?php echo $fila['apellido']; ?>" placeholder="Ingrese el apellido" autocomplete="off" required>
        </div>
        <div class="input-group">
            <label for="nombre_alu">Nombre:</label>
            <input type="text" class="form-container__input full" id="nombre_alu" name="nombre_alu" value="<?php echo $fila['nombre']; ?>" placeholder="Ingrese el nombre" autocomplete="off" required>
        </div>
        </div>
        <div class="row">
            <div class="input-group">
            <label for="dni_alu">DNI:</label>
            <input type="number" class="form-container__input full" id="dni_alu" name="dni_alu" value="<?php echo $fila['DNI']; ?>" placeholder="Ingrese el DNI" autocomplete="off" required>
        </div>
            <div class="input-group">
                <label for="cuil">CUIL:</label>
                <input type="number" class="form-container__input full" id="cuil" name="cuil" placeholder="Ingrese el cuil" autocomplete="off" required>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
            <label for="edad">Fecha de nacimiento:</label>
            <input type="date" class="form-container__input full" id="edad" name="edad" value="<?php echo $fila['fecha_nacimiento']; ?>" autocomplete="off">
        </div>
        </div>
        
        <?php
// Iniciar transacción para asegurar consistencia
$conexion->begin_transaction();

// Obtener el número máximo de legajo actual
$sql_legajo = "SELECT MAX(legajo) AS max_legajo FROM alumno";
$resultado_legajo = $conexion->query($sql_legajo);
$fila_legajo = $resultado_legajo->fetch_assoc();
$nuevo_legajo = $fila_legajo['max_legajo'] + 1;

// Verificar si el número de legajo ya existe
$legajo_existe = true;

while ($legajo_existe) {
    // Verificar si el legajo ya existe en la base de datos
    $sql_check_legajo = "SELECT COUNT(*) AS cantidad FROM alumno WHERE legajo = $nuevo_legajo";
    $resultado_check = $conexion->query($sql_check_legajo);
    $fila_check = $resultado_check->fetch_assoc();

    if ($fila_check['cantidad'] == 0) {
        // Si el legajo no existe, lo utilizamos
        $legajo_existe = false;
    } else {
        // Si el legajo ya existe, incrementar y verificar nuevamente
        $nuevo_legajo++;
    }
}

// Confirmar la transacción para asegurar que no haya colisiones
$conexion->commit();
?>
        <div class="row">
            
            <div class="input-group">
                <input type="hidden" id="legajo" name="legajo" value="<?php echo $nuevo_legajo; ?>" class="form-container__input full">
            </div>
        
        
            
            <div class="input-group">
                <label for="observaciones">Observaciones:</label>
                <input type="text" class="form-container__input full" id="observaciones" name="observaciones" placeholder="Observaciones" autocomplete="off" required>
            </div>
        </div>

        <div class="row">
            <div class="input-group">
                <label for="pais">País:</label>
                <select id="pais" name="pais" class="form-container__input full">
                    <option value="">Seleccione un país</option>
                </select>
            </div>
            <div class="input-group">
                <label for="provincia">Provincia:</label>
                <select id="provincia" name="provincia" class="form-container__input full" disabled>
                    <option value="">Seleccione una provincia</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="ciudad">Ciudad:</label>
                <select id="ciudad" name="ciudad" class="form-container__input full" disabled>
                    <option value="">Seleccione una ciudad</option>
                </select>
            </div>
        </div>

        <div class="row">
            <label>¿Tiene alguna discapacidad?</label>
            <div>
                <label>
                    <input type="radio" name="discapacidad" id="discapacidad-si"> Sí
                </label>
                <label>
                    <input type="radio" name="discapacidad" id="discapacidad-no"> No
                </label>
            </div>
        </div>

        <div class="row">
            <label for="discapacidad">Describa su discapacidad:</label>
            <input type="text" id="discapacidad-input" name="discapacidad" class="form-container__input full" disabled placeholder="Escriba su discapacidad aquí...">
        </div>

        <div class="row">
            <h3>Datos nivel secundario</h3>
            <label>¿Finalizó secundario?</label>
            <div>
                <label>
                    <input type="radio" name="finalizo_secundario" id="secundario-si"> Sí
                </label>
                <label>
                    <input type="radio" name="finalizo_secundario" id="secundario-no"> No
                </label>
            </div>
        </div>

        <div class="row" id="finalizo-si-section" style="display:none;">
            <div class="input-group">
                <label for="titulo-nivel-medio">Título nivel medio/superior:</label>
                <input type="text" id="titulo-nivel-medio" name="titulo_nivel_medio" class="form-container__input full" placeholder="Ingrese el título">
            </div>
            <div class="input-group">
                <label for="otorgado-por-escuela">Otorgado por escuela:</label>
                <input type="text" id="otorgado-por-escuela" name="otorgado_por_escuela" class="form-container__input full" placeholder="Ingrese el nombre de la escuela">
            </div>
        </div>

        <div class="row" id="finalizo-no-section" style="display:none;">
            <div class="input-group">
                <label for="materias-adeudadas">Cantidad de materias adeudadas:</label>
                <input type="number" id="materias-adeudadas" name="materias_adeudadas" class="form-container__input full" placeholder="Ingrese cantidad de materias">
            </div>
            <div class="input-group">
                <label for="fecha-rendicion">Estimativo de fecha que rendirá:</label>
                <input type="date" id="fecha_rendicion" name="fecha_rendicion" class="form-container__input full">
            </div>
        </div>

       <div class="row">
    <h3>Datos laborales</h3>
    <label>¿Trabaja?</label>
    <div>
        <label>
            <input type="radio" name="trabaja" id="trabaja-si"> Sí
        </label>
        <label>
            <input type="radio" name="trabaja" id="trabaja-no"> No
        </label>
    </div>
</div>

<div class="row" id="trabaja-si-section" style="display:none;">
    <div class="input-group">
        <label for="domicilio-laboral">Domicilio laboral:</label>
        <input type="text" id="domicilio-laboral" name="domicilio_laboral" class="form-container__input full" placeholder="Ingrese domicilio laboral" disabled>
    </div>
    <div class="input-group">
        <label for="ocupacion">Ocupación:</label>
        <input type="text" id="ocupacion" name="ocupacion" class="form-container__input full" placeholder="Ingrese su ocupación" disabled>
    </div>
    <div class="input-group">
        <label for="horario-laboral">Horario laboral:</label>
        <div>
            <input type="time" id="horario-laboral-desde" name="horario_laboral_desde" class="form-container__input full" placeholder="Desde" disabled>
            <input type="time" id="horario-laboral-hasta" name="horario_laboral_hasta" class="form-container__input full" placeholder="Hasta" disabled>
        </div>
    </div>
</div>


        <div class="row">
            <h3>Datos de domicilio</h3>
            <div class="input-group">
                <label for="calle">Calle:</label>
                <input type="text" id="calle" name="calle" class="form-container__input full" placeholder="Calle">
            </div>
            <div class="input-group">
                <label for="barrio">Barrio:</label>
                <input type="text" id="barrio" name="barrio" class="form-container__input full" placeholder="Barrio">
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="numeracion">Numeración:</label>
                <input type="text" id="numeracion" name="numeracion" class="form-container__input full" placeholder="Numeración">
            </div>
            <div class="input-group">
                <label for="pais-domicilio">País:</label>
                <select id="pais-domicilio" class="form-container__input full">
                    <option value="">País</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="provincia-domicilio">Provincia:</label>
                <select id="provincia-domicilio" name="provincia_domicilio" class="form-container__input full" disabled>
                    <option value="">Provincia</option>
                </select>
            </div>
            <div class="input-group">
                <label for="ciudad-domicilio">Ciudad:</label>
                <select id="ciudad-domicilio" name="ciudad_domicilio" class="form-container__input full" disabled>
                    <option value="">Ciudad</option>
                </select>
            </div>
        </div>

        <div class="row">
            <h3>Datos de contacto</h3>
            <div class="input-group">
        <label for="telefono-celular">Tel. Celular:</label>
        <input type="text" id="telefono-celular" name="celular" class="form-container__input full" value="<?php echo $fila['celular']; ?>" placeholder="Tel. Celular">
    </div>
            <div class="input-group">
                <label for="telefono-urgencias">Tel. Urgencias:</label>
                <input type="text" id="telefono-urgencias" name="telefono_urgencias" class="form-container__input full" placeholder="Tel. Urgencias">
            </div>
        </div>
        <div class="row">
            <div class="input-group">
        <label for="correo-electronico">Correo Electrónico:</label>
        <input type="email" id="correo-electronico" name="correo_electronico" class="form-container__input full" value="<?php echo $fila['correo']; ?>" placeholder="Correo Electrónico">
    </div>
        </div>

        <div class="row">
            <h3>Matriculación</h3>
            <label>Carrera:</label>
            <div>
                <label>
                    <input type="checkbox" name="carrera" value="Técnico Superior en Enfermería"> Técnico Superior en Enfermería
                </label>
                <label>
                    <input type="checkbox" name="carrera" value="Técnico Superior en Acompañamiento Terapéutico"> Técnico Superior en Acompañamiento Terapéutico
                </label>
                <label>
                    <input type="checkbox" name="carrera" value="Técnico Superior en Comercialización y Marketing"> Técnico Superior en Comercialización y Marketing
                </label>
                <label>
                    <input type="checkbox" name="carrera" value="Técnico Superior en Automatización y Robótica"> Técnico Superior en Automatización y Robótica
                </label>
            </div>
        </div>
        <div class="row">
    <h3>Requisitos presentados</h3>
</div>
<div class="row requisitos">
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito1" value="titulo-secundario"> Original y copia del Título Secundario
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito2" value="fotos"> 2 fotos 4x4
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito3" value="folio"> Folio A4
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito4" value="dni"> Fotocopia del DNI
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito5" value="partida-nacimiento"> Fotocopia de la Partida de Nacimiento
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito6" value="Cuil"> CUIL
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito7" value="ayuda-economica"> $15.000 Ayuda económica voluntaria para gastos de limpieza (lavandina, cera, trapos de piso, etc.) y gastos administrativos (hojas, carpetas, tóner, etc.).
        </label>
    </div>
    
</div>


        <?php
           $sql_mater = "SELECT * FROM carreras";
           $peticion = mysqli_query($conexion, $sql_mater);
        ?>
        <select name="inscripcion_carrera" class="form-container__input full">
            <option hidden>Selecciona una carrera</option>
            <?php while ($informacion = mysqli_fetch_assoc($peticion)) { ?>
                <option value="<?php echo $informacion['idCarrera'] ?>"><?php echo $informacion['nombre_carrera'] ?></option>
            <?php } ?>
        </select>

        <input type="submit" class="form-container__input" name="enviar" value="Enviar" onclick="mostrarAlertaExitosa(); closeSuccessMessage();">
    </form>
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
    const paisSelect = document.getElementById('pais');
    const provinciaSelect = document.getElementById('provincia');
    const ciudadSelect = document.getElementById('ciudad');
    const discapacidadSi = document.getElementById('discapacidad-si');
    const discapacidadNo = document.getElementById('discapacidad-no');
    const discapacidadInput = document.getElementById('discapacidad-input');
    const secundarioSi = document.getElementById('secundario-si');
    const secundarioNo = document.getElementById('secundario-no');
    const finalizoSiSection = document.getElementById('finalizo-si-section');
    const finalizoNoSection = document.getElementById('finalizo-no-section');
    const trabajaSi = document.getElementById('trabaja-si');
    const trabajaNo = document.getElementById('trabaja-no');
    const trabajaSiSection = document.getElementById('trabaja-si-section');
    const domicilioLaboral = document.getElementById('domicilio-laboral');
    const ocupacion = document.getElementById('ocupacion');
    const horarioLaboralDesde = document.getElementById('horario-laboral-desde');
    const horarioLaboralHasta = document.getElementById('horario-laboral-hasta');   
    // Lista de países de Latinoamérica
    const latinAmericanCountries = [
        'Argentina', 'Bolivia', 'Brazil', 'Chile', 'Colombia', 'Costa Rica',
        'Cuba', 'Dominican Republic', 'Ecuador', 'El Salvador', 'Guatemala',
        'Honduras', 'Mexico', 'Nicaragua', 'Panama', 'Paraguay', 'Peru',
        'Uruguay', 'Venezuela'
    ];

    // Fetch countries and filter to show only Latin American countries
    fetch('https://countriesnow.space/api/v0.1/countries')
    .then(response => response.json())
    .then(data => {
        data.data.forEach(country => {
            if (latinAmericanCountries.includes(country.country)) {
                const option = document.createElement('option');
                option.value = country.country;
                option.textContent = country.country;
                paisSelect.appendChild(option);
            }
        });
    });

    // Fetch states (provinces)
    paisSelect.addEventListener('change', function() {
        const countryName = this.value;
        provinciaSelect.disabled = true;
        ciudadSelect.disabled = true;
        ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
        provinciaSelect.innerHTML = '<option value="">Seleccione una provincia</option>';

        if (countryName) {
            fetch('https://countriesnow.space/api/v0.1/countries/states', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ country: countryName })
            })
            .then(response => response.json())
            .then(data => {
                data.data.states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.name;
                    option.textContent = state.name;
                    provinciaSelect.appendChild(option);
                });
                provinciaSelect.disabled = false;
            });
        }
    });

    // Fetch cities based on selected province
    provinciaSelect.addEventListener('change', function() {
        const countryName = paisSelect.value;
        const stateName = this.value;
        ciudadSelect.disabled = true;
        ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';

        if (stateName) {
            fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ country: countryName, state: stateName })
            })
            .then(response => response.json())
            .then(data => {
                data.data.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    ciudadSelect.appendChild(option);
                });
                ciudadSelect.disabled = false;
            });
        }
    });

 // Manejar la selección de discapacidad
discapacidadSi.addEventListener('change', function() {
    if (this.checked) {
        discapacidadInput.disabled = false;
        discapacidadInput.readOnly = false;
        discapacidadInput.value = '';  // Limpiar el valor
        discapacidadInput.placeholder = "Escriba su discapacidad aquí...";
    }
});

discapacidadNo.addEventListener('change', function() {
    if (this.checked) {
        discapacidadInput.disabled = false;
        discapacidadInput.readOnly = true;  // Hacer el campo solo de lectura
        discapacidadInput.value = 'No posee';  // Asignar el valor "No posee"
        discapacidadInput.placeholder = "No posee discapacidad";
    }
});




    // Manejar la selección de si finalizó secundario
    secundarioSi.addEventListener('change', function() {
        if (this.checked) {
            finalizoSiSection.style.display = 'block';
            finalizoNoSection.style.display = 'none';
        }
    });

    secundarioNo.addEventListener('change', function() {
        if (this.checked) {
            finalizoSiSection.style.display = 'none';
            finalizoNoSection.style.display = 'block';
        }
    });
    
   // Manejar la selección de si trabaja
trabajaSi.addEventListener('change', function() {
    if (this.checked) {
        trabajaSiSection.style.display = 'block'; // Mostrar la sección
        domicilioLaboral.disabled = false;
        ocupacion.disabled = false;
        horarioLaboralDesde.disabled = false;
        horarioLaboralHasta.disabled = false;

        // Limpiar los valores y placeholders
        domicilioLaboral.value = '';
        ocupacion.value = '';
        horarioLaboralDesde.value = '';
        horarioLaboralHasta.value = '';
        domicilioLaboral.placeholder = 'Ingrese domicilio laboral';
        ocupacion.placeholder = 'Ingrese su ocupación';
    }
});

trabajaNo.addEventListener('change', function() {
    if (this.checked) {
        trabajaSiSection.style.display = 'block'; // Puedes mantenerlo visible o no
        domicilioLaboral.value = 'no trabaja';  // Mostrar "no trabaja"
        ocupacion.value = 'no trabaja';  // Mostrar "no trabaja"
        horarioLaboralDesde.value = '00:00';  // Establecer hora en 00:00
        horarioLaboralHasta.value = '00:00';  // Establecer hora en 00:00

        // Cambiar a solo lectura en lugar de deshabilitar
        domicilioLaboral.readOnly = true;
        ocupacion.readOnly = true;
        horarioLaboralDesde.readOnly = true;
        horarioLaboralHasta.readOnly = true;
    }
});

    
     const paisDomicilioSelect = document.getElementById('pais-domicilio');
    const provinciaDomicilioSelect = document.getElementById('provincia-domicilio');
    const ciudadDomicilioSelect = document.getElementById('ciudad-domicilio');

    // Fetch countries
    fetch('https://countriesnow.space/api/v0.1/countries')
    .then(response => response.json())
    .then(data => {
        data.data.forEach(country => {
            const option = document.createElement('option');
            option.value = country.country;
            option.textContent = country.country;
            paisDomicilioSelect.appendChild(option);
        });
    });

    // Fetch provinces based on selected country
    paisDomicilioSelect.addEventListener('change', function() {
        const countryName = this.value;
        provinciaDomicilioSelect.disabled = true;
        ciudadDomicilioSelect.disabled = true;
        ciudadDomicilioSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
        provinciaDomicilioSelect.innerHTML = '<option value="">Seleccione una provincia</option>';

        if (countryName) {
            fetch('https://countriesnow.space/api/v0.1/countries/states', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ country: countryName })
            })
            .then(response => response.json())
            .then(data => {
                data.data.states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.name;
                    option.textContent = state.name;
                    provinciaDomicilioSelect.appendChild(option);
                });
                provinciaDomicilioSelect.disabled = false;
            });
        }
    });

    // Fetch cities based on selected province
    provinciaDomicilioSelect.addEventListener('change', function() {
        const countryName = paisDomicilioSelect.value;
        const stateName = this.value;
        ciudadDomicilioSelect.disabled = true;
        ciudadDomicilioSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';

        if (stateName) {
            fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ country: countryName, state: stateName })
            })
            .then(response => response.json())
            .then(data => {
                data.data.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    ciudadDomicilioSelect.appendChild(option);
                });
                ciudadDomicilioSelect.disabled = false;
            });
        }
    });
    
    
    
    const telefonoCelularInput = document.getElementById('telefono-celular');
    const telefonoUrgenciasInput = document.getElementById('telefono-urgencias');
    const correoElectronicoInput = document.getElementById('correo-electronico');
    
    // Validar inputs cuando se pierda el foco
    telefonoCelularInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.value = '0';
        }
    });

    telefonoUrgenciasInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.value = '0';
        }
    });

    correoElectronicoInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.value = 'no se relleno';
        }
    });
    
    
    document.querySelector('form').addEventListener('submit', function(event) {
    const requisitos = [
        document.querySelector('input[name="requisito1"]'),
        document.querySelector('input[name="requisito2"]'),
        document.querySelector('input[name="requisito3"]'),
        document.querySelector('input[name="requisito4"]'),
        document.querySelector('input[name="requisito5"]'),
        document.querySelector('input[name="requisito6"]'),
        document.querySelector('input[name="requisito7"]')
    ];

    let presentoRequisitos = false;

    requisitos.forEach(function(requisito) {
        if (requisito && requisito.checked) {
            presentoRequisitos = true;
        }
    });

    if (presentoRequisitos) {
        alert("Presentó todos los requisitos.");
    } else {
        alert("No presentó ningún requisito.");
    }
});

</script>


<style>
     .contenido {
        position: absolute;
        top: 55px;
        left: 270px;
        width: calc(100% - 270px);
        background-color: #ffffff;
        background-image: url(assets/img/fondo.png);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        padding: 20px;
        min-height: 100%;
        background-attachment: local;
    }

    .form-container {
        max-width: 75%;
        margin: 0 auto;
        padding: 50px;
        background-color: #0036ff25;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-container__h2 {
        text-align: center;
        color: #b71c1c;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .form-container__input {
        width: 100%;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .input-group {
        width: 48%;
    }

    .row label {
        font-weight: bold;
        color: #b71c1c;
        margin-bottom: 5px;
        display: block;
    }

    .row h3 {
        width: 100%;
        color: #b71c1c;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .row div {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    input[type="submit"] {
        background-color: #b71c1c;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 10px 20px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
        width: 100%;
    }

    input[type="submit"]:hover {
        background-color: #8c1b1b;
    }
    /* Estilos para los radios personalizados */
input[type="radio"] {
    appearance: none;
    -webkit-appearance: none;
    background-color: #fff;
    border: 2px solid #b71c1c;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    cursor: pointer;
    outline: none;
    transition: background 0.3s ease;
}

input[type="radio"]:checked {
    background-color: #b71c1c;
    border-color: #b71c1c;
}

input[type="radio"]::after {
    content: '';
    display: block;
    width: 10px;
    height: 10px;
    margin: 5px;
    border-radius: 50%;
    background-color: #fff;
    transition: background 0.3s ease;
}

input[type="radio"]:checked::after {
    background-color: #fff;
}
input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    background-color: #fff;
    border: 2px solid #b71c1c;
    border-radius: 4px;
    width: 20px;
    height: 20px;
    cursor: pointer;
    outline: none;
    transition: background-color 0.3s ease, border-color 0.3s ease;
    position: relative;
}

input[type="checkbox"]:checked {
    background-color: #b71c1c;
    border-color: #b71c1c;
}

input[type="checkbox"]::after {
    content: '';
    position: absolute;
    top: 4px;
    left: 4px;
    width: 10px;
    height: 10px;
    background-color: white;
    display: none;
}

input[type="checkbox"]:checked::after {
    display: block;
}
.requisitos {
    margin-top: 20px;
}

.requisitos .input-group {
    width: 100%;
    margin-bottom: 10px;
}

.requisitos label {
    font-weight: normal;
    color: #333;
}

.requisitos input[type="checkbox"] {
    margin-right: 10px;
}

</style>



</body>
</html>

