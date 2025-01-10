<?php
session_start();
if (empty($_SESSION["id"])){header('Location: ../../../../login/login.php');}

// Asumimos que también almacenas el rol en la sesión.
$rolUsuario = $_SESSION["roles"];

// Definimos los roles permitidos para esta página.
$rolesPermitidos = ['1', '2', '3'];

// Verificar si el rol del usuario está en la lista de roles permitidos.
if (!in_array($rolUsuario, $rolesPermitidos)) {
    echo "<script>alert('Acceso restringido a esta página.');</script>";
    // Opcional: redirigir al usuario a otra página
    // header('Location: pagina_principal.php');
    exit; // Detener la ejecución del script.
}

// Set inactivity limit in seconds
$inactivity_limit = 1200;

// Check if the user has been inactive for too long
if (isset($_SESSION['time']) && (time() - $_SESSION['time'] > $inactivity_limit)) {
    // User has been inactive, so destroy the session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: ../../../../login/login.php");
    exit; // Terminar el script después de redireccionar
} else {
    // Update the session time to the current time
    $_SESSION['time'] = time();
}
?>
<?php include'../../../../conexion/conexion.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>SGPM</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="../../../assets/img/Logo ISPM 2 transparante.png" type="image/x-icon"/>
	<!-- DATA TABLES -->
    <link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
     <script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>
	<!-- Fonts and icons -->
	<script src="../../../assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Open+Sans:300,400,600,700"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"], urls: ['../../../assets/css/fonts.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../../assets/css/azzara.min.css">
	<link rel="stylesheet" href="../../../assets/css/estilos.css">

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="../../../assets/css/demo.css">
</head>
<body>
	<div class="wrapper">
		
		<div class="main-header" data-background-color="red">
			<div class="logo-header">
				
				<a href="../../../index.php" class="logo">
					<img src="../../../assets/img/Logo ISPM 2 transparante.png" width="45px" alt="navbar brand" class="navbar-brand">
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
        $imagenBase64 = '../../../assets/img/1361728.png';
    }
} else {
    echo "Error al cargar la imagen.";
}


?>

<!-- HTML para mostrar la imagen -->
<?php if ($imagenBase64 === '../../../../assets/img/1361728.png') : ?>
    <!-- Mostrar imagen por defecto si no hay avatar -->
    <img src="../../../../assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;">
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
        $imagenBase64 = '../../../assets/img/1361728.png';
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
									<a class="dropdown-item" href="../../../Perfil.php">Mi Perfil</a>
									<a class="dropdown-item" href="../../../../login/cerrar_sesion.php">Cerrar Sesión</a>
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
							<a href="../../../index.php">
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
						<li class="nav-item active submenu">
							<a data-toggle="collapse" href="#base">
								<i class="fas fa-child"></i>
								<p>Estudiantes</p>
								<span class="caret"></span>
							</a>
							<div class="collapse show" id="base">
								<ul class="nav nav-collapse">
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li >
										<a href="./nuevo_estudiante.php">
											<span class="sub-item">Nuevo Estudiante</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../../FP/nuevo_estudianteFP.php">
											<span class="sub-item">Nuevo Estudiante FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li class="active">
										<a href="../lista_estudiantes.php">
											<span class="sub-item">Lista Estudiantes</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../lista_estudiantes_2025.php">
											<span class="sub-item">Lista Estudiantes 2025</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../../FP/lista_estudianteFP.php">
											<span class="sub-item">Lista Estudiantes FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="../Informes/informe_asistencia_tecnicaturas.php">
											<span class="sub-item">Informe de Asistencias Técnicaturas</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../../proximamente.php">
											<span class="sub-item">Informe de Asistencias FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="../Informes/informe_lista_estudiantes.php">
											<span class="sub-item">Imprimir Lista de Estudiantes Técnicaturas</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../../FP/informesFP/informe_lista_estudiantesFP.php">
											<span class="sub-item">Imprimir Lista de Estudiantes FP</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
									<li>
										<a href="../Falta_justificada/falta_justificada.php">
											<span class="sub-item">Justificar Falta</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="../Retirados/estudiantes_retirados.php">
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
                            <a href="../../../index_asistencia.php">
                                <span class="sub-item">Estudiantes Técnicaturas</span>
                            </a>
                        </li>
						<?php endif; ?>
					<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                        <li>
                            <a href="../../FP/ver_FPS.php">
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
                            <a href="../../../ver_carreras.php">
                                <span class="sub-item">Estudiantes Técnicaturas</span>
                            </a>
                        </li>
						<?php endif; ?>
						<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                        <li>
                            <a href="../../FP/ver_asistenciaFPS.php">
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
										<a href="../../../Administracion/Profesores/alta_docente.php">
											<span class="sub-item">Alta Docentes</span>
										</a>
									</li>
									<li>
										<a href="../../../Administracion/Profesores/lista_profesores.php">
											<span class="sub-item">Lista de Docentes</span>
										</a>
									</li>
									<li>
										<a href="../../../Administracion/Profesores/materia_profesor.php">
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
                            <a href="../../../proximamente.php">
                                <span class="sub-item">Nuevo Preceptor</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../proximamente.php">
                                <span class="sub-item">Lista de Preceptores</span>
                            </a>
                        </li>
                       
						<li>
							<a href="../../../proximamente.php">
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
						<a href="../../../Administracion/Profesores/acta_volante_materias.php">
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
                         <a href="../../../Administracion/Profesores/pre_parciales.php">
                            <span class="sub-item">Gestión de Notas</span>
                            </a>
                        </li>
						<li>
                            <a href="../../../pre_libro.php">
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
                <a href="../../../lista_pre_inscriptos.php">
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
    <?php
    if (isset($_GET['legajo'])) {
        $legajo = $_POST['legajo'] ?? $_GET['legajo'] ?? null;
        $sql = "SELECT * FROM alumno WHERE legajo = '$legajo'";
        $query = mysqli_query($conexion, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            $datos = mysqli_fetch_assoc($query);
    ?>
            <h2 class="form-container__h2">Editar Estudiante</h2>
            <form action="guardar_modificacion_alumno.php" method="post" class="form-container">
                
                <!-- Información personal -->
                <div class="row">
                    <div class="column half">
                        <label for="nombre_alumno">Nombre</label>
                        <input type="text" id="nombre_alumno" name="nombre_alumno" placeholder="Nombre" value="<?php echo htmlspecialchars($datos['nombre_alumno']); ?>" class="form-container__input">
                    </div>
                    <div class="column half">
                        <label for="apellido_alumno">Apellido</label>
                        <input type="text" id="apellido_alumno" name="apellido_alumno" placeholder="Apellido" value="<?php echo htmlspecialchars($datos['apellido_alumno']); ?>" class="form-container__input">
                    </div>
                </div>

                <div class="row">
                    <div class="column half">
                        <label for="dni_alumno">DNI (sin puntos)</label>
                        <input type="number" id="dni_alumno" name="dni_alumno" placeholder="DNI (sin puntos)" value="<?php echo htmlspecialchars($datos['dni_alumno']); ?>" class="form-container__input">
                    </div>
                    <div class="column half">
                        <label for="cuil">CUIL</label>
                        <input type="text" id="cuil" name="cuil" placeholder="CUIL" value="<?php echo htmlspecialchars($datos['cuil']); ?>" class="form-container__input">
                    </div>
                </div>

                <div class="row">
                    <div class="column half">
                        <label for="legajo">N° Legajo</label>
                        <input type="text" id="legajo" name="legajo" placeholder="N° Legajo" value="<?php echo htmlspecialchars($datos['legajo']); ?>" class="form-container__input" readonly>
                    </div>
					<div class="column half">
        				<label for="fecha_nacimiento">Fecha de Nacimiento</label>
        				<input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($datos['fecha_nacimiento']); ?>" class="form-container__input">
    				</div>
                </div>
				<div class="row">
				<div class="column half">
                        <label for="pais_nacimiento">País de Nacimiento</label>
                        <input type="text" id="pais_nacimiento" name="pais_nacimiento" placeholder="País de Nacimiento" value="<?php echo htmlspecialchars($datos['pais_nacimiento']); ?>" class="form-container__input">
                    </div>
				</div>
                <!-- Información de nacimiento -->
                <div class="row">
                    <div class="column half">
                        <label for="ciudad_nacimiento">Ciudad de Nacimiento</label>
                        <input type="text" id="ciudad_nacimiento" name="ciudad_nacimiento" placeholder="Ciudad de Nacimiento" value="<?php echo htmlspecialchars($datos['ciudad_nacimiento']); ?>" class="form-container__input">
                    </div>
                    <div class="column half">
                        <label for="provincia_nacimiento">Provincia de Nacimiento</label>
                        <input type="text" id="provincia_nacimiento" name="provincia_nacimiento" placeholder="Provincia de Nacimiento" value="<?php echo htmlspecialchars($datos['provincia_nacimiento']); ?>" class="form-container__input">
                    </div>
                    
                </div>

                <!-- Información de contacto -->
                <div class="row">
                    <div class="column half">
                        <label for="celular">Celular</label>
                        <input type="text" id="celular" name="celular" placeholder="Celular" value="<?php echo htmlspecialchars($datos['celular']); ?>" class="form-container__input">
                    </div>
                    <div class="column half">
                        <label for="telefono_urgencias">Teléfono de Urgencias</label>
                        <input type="text" id="telefono_urgencias" name="telefono_urgencias" placeholder="Teléfono de Urgencias" value="<?php echo htmlspecialchars($datos['telefono_urgencias']); ?>" class="form-container__input">
                    </div>
                </div>

                <!-- Información de domicilio -->
                <div class="row">
                    <div class="column half">
                        <label for="calle_domicilio">Calle</label>
                        <input type="text" id="calle_domicilio" name="calle_domicilio" placeholder="Calle" value="<?php echo htmlspecialchars($datos['calle_domicilio']); ?>" class="form-container__input">
                    </div>
                    <div class="column half">
                        <label for="barrio_domicilio">Barrio</label>
                        <input type="text" id="barrio_domicilio" name="barrio_domicilio" placeholder="Barrio" value="<?php echo htmlspecialchars($datos['barrio_domicilio']); ?>" class="form-container__input">
                    </div>
                </div>

                <!-- Información académica -->
                <div class="row">
                    <div class="column half">
                        <label for="titulo_secundario">Título Secundario</label>
                        <input type="text" id="titulo_secundario" name="titulo_secundario" placeholder="Título Secundario" value="<?php echo htmlspecialchars($datos['Titulo_secundario']); ?>" class="form-container__input">
                    </div>
                    <div class="column half">
                        <label for="escuela_secundaria">Escuela Secundaria</label>
                        <input type="text" id="escuela_secundaria" name="escuela_secundaria" placeholder="Escuela Secundaria" value="<?php echo htmlspecialchars($datos['escuela_secundaria']); ?>" class="form-container__input">
                    </div>
                </div>

                <!-- Información laboral -->
                <div class="row">
                    <div class="column half">
                        <label for="ocupacion">Ocupación</label>
                        <input type="text" id="ocupacion" name="ocupacion" placeholder="Ocupación" value="<?php echo htmlspecialchars($datos['ocupacion']); ?>" class="form-container__input">
                    </div>
                    <div class="column half">
                        <label for="domicilio_laboral">Domicilio Laboral</label>
                        <input type="text" id="domicilio_laboral" name="domicilio_laboral" placeholder="Domicilio Laboral" value="<?php echo htmlspecialchars($datos['domicilio_laboral']); ?>" class="form-container__input">
                    </div>
                </div>

                <!-- Horario laboral -->
                <div class="row">
                    <div class="column half">
                        <label for="horario_laboral_desde">Horario Laboral Desde</label>
                        <input type="time" id="horario_laboral_desde" name="horario_laboral_desde" value="<?php echo htmlspecialchars($datos['horario_laboral_desde']); ?>" class="form-container__input">
                    </div>
                    <div class="column half">
                        <label for="horario_laboral_hasta">Horario Laboral Hasta</label>
                        <input type="time" id="horario_laboral_hasta" name="horario_laboral_hasta" value="<?php echo htmlspecialchars($datos['horario_laboral_hasta']); ?>" class="form-container__input">
                    </div>
                </div>

                <!-- Campo de Pago y Selección de Año -->
                <div class="row">
                    <div class="column half">
                        <label for="pago">¿Pago Realizado?</label>
                        <select id="pago" name="pago" class="form-container__input">
                            <option value="1" <?php echo ($datos['Pago'] == 1) ? 'selected' : ''; ?>>Sí</option>
                            <option value="0" <?php echo ($datos['Pago'] == 0) ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                    
                    <div class="column half">
                        <label for="anio">Año de Inscripción</label>
                        <select id="anio" name="anio" class="form-container__input" onchange="actualizarEstado()" required>
                            <option value="2024" <?php echo ($datos['estado'] == 1) ? 'selected' : ''; ?>>2024</option>
                            <option value="2025" <?php echo ($datos['estado'] == 3) ? 'selected' : ''; ?>>2025</option>
                        </select>
                    </div>
                </div>

                <input type="hidden" id="estado" name="estado" value="<?php echo ($datos['estado'] == 1) ? '1' : '3'; ?>">

                <script>
                function actualizarEstado() {
                    var anio = document.getElementById("anio").value;
                    var estado = document.getElementById("estado");
                    estado.value = (anio === "2024") ? "1" : "3";
                }
                </script>

                <!-- Otros campos -->
                <div class="row">
                    <label for="observaciones">Observaciones</label>
                    <input type="text" id="observaciones" name="observaciones" placeholder="Observaciones" value="<?php echo htmlspecialchars($datos['observaciones']); ?>" class="form-container__input full">
                </div>

                <div class="row">
                    <label for="discapacidad">Discapacidad</label>
                    <input type="text" id="discapacidad" name="discapacidad" placeholder="Discapacidad" value="<?php echo htmlspecialchars($datos['discapacidad']); ?>" class="form-container__input full">
                </div>
                
                <div class="row">
   <h3>Matriculación</h3>
<label>Carrera:</label>
<div>
    <label>
        <input type="radio" name="carrera" value="Técnico Superior en Enfermería" 
        <?php echo (isset($datos['carrera']) && $datos['carrera'] == 'Técnico Superior en Enfermería') ? 'checked' : ''; ?>>
        Técnico Superior en Enfermería
    </label>
    <label>
        <input type="radio" name="carrera" value="Técnico Superior en Acompañamiento Terapéutico" 
        <?php echo (isset($datos['carrera']) && $datos['carrera'] == 'Técnico Superior en Acompañamiento Terapéutico') ? 'checked' : ''; ?>>
        Técnico Superior en Acompañamiento Terapéutico
    </label>
    <label>
        <input type="radio" name="carrera" value="Técnico Superior en Comercialización y Marketing" 
        <?php echo (isset($datos['carrera']) && $datos['carrera'] == 'Técnico Superior en Comercialización y Marketing') ? 'checked' : ''; ?>>
        Técnico Superior en Comercialización y Marketing
    </label>
    <label>
        <input type="radio" name="carrera" value="Técnico Superior en Automatización y Robótica" 
        <?php echo (isset($datos['carrera']) && $datos['carrera'] == 'Técnico Superior en Automatización y Robótica') ? 'checked' : ''; ?>>
        Técnico Superior en Automatización y Robótica
    </label>
</div>
</div>

                
                <div class="row">
    <label for="correo">Correo</label>
    <input type="text" id="correo" name="correo" value="<?php echo htmlspecialchars($datos['correo']); ?>" class="form-container__input full" placeholder="Correo">
</div>
                <div class="row">
    <label for="original_titulo">Original del Título</label>
    <select id="original_titulo" name="original_titulo" class="form-container__input full">
        <option value="1" <?php echo ($datos['original_titulo'] == '1') ? 'selected' : ''; ?>>Presentó requisito</option>
        <option value="0" <?php echo ($datos['original_titulo'] == '0' || is_null($datos['original_titulo'])) ? 'selected' : ''; ?>>No presentó</option>
    </select>
</div>
                <div class="row">
    <label for="foto">Fotografía</label>
    <select id="foto" name="foto" class="form-container__input full">
        <option value="1" <?php echo ($datos['foto'] == '1') ? 'selected' : ''; ?>>Sí presentó</option>
        <option value="0" <?php echo ($datos['foto'] == '0' || is_null($datos['foto'])) ? 'selected' : ''; ?>>No presentó requisito</option>
    </select>
</div>
<div class="row">
    <label for="folio">Folio</label>
    <select id="folio" name="folio" class="form-container__input full">
        <option value="Presentó requisito" <?php echo ($datos['folio'] == 'Presentó requisito') ? 'selected' : ''; ?>>Presentó requisito</option>
        <option value="0" <?php echo ($datos['folio'] == '0' || is_null($datos['folio'])) ? 'selected' : ''; ?>>No presentó requisito</option>
    </select>
</div>
<div class="row">
    <label for="fotocopia_dni">Fotocopia DNI</label>
    <select id="fotocopia_dni" name="fotocopia_dni" class="form-container__input full">
        <option value="Presentó requisito" <?php echo ($datos['fotocopia_dni'] == 'Presentó requisito') ? 'selected' : ''; ?>>Presentó requisito</option>
        <option value="0" <?php echo ($datos['fotocopia_dni'] == '0' || is_null($datos['fotocopia_dni'])) ? 'selected' : ''; ?>>No presentó requisito</option>
    </select>
</div>
<div class="row">
    <label for="fotocopia_partida_nacimiento">Fotocopia Partida de Nacimiento</label>
    <select id="fotocopia_partida_nacimiento" name="fotocopia_partida_nacimiento" class="form-container__input full">
        <option value="Presentó requisito" <?php echo ($datos['fotocopia_partida_nacimiento'] == 'Presentó requisito') ? 'selected' : ''; ?>>Presentó requisito</option>
        <option value="0" <?php echo ($datos['fotocopia_partida_nacimiento'] == '0' || is_null($datos['fotocopia_partida_nacimiento'])) ? 'selected' : ''; ?>>No presentó requisito</option>
    </select>
</div>
<div class="row">
    <label for="constancia_cuil">Constancia CUIL</label>
    <select id="constancia_cuil" name="constancia_cuil" class="form-container__input full">
        <option value="Presentó requisito" <?php echo ($datos['constancia_cuil'] == 'Presentó requisito') ? 'selected' : ''; ?>>Presentó requisito</option>
        <option value="0" <?php echo ($datos['constancia_cuil'] == '0' || is_null($datos['constancia_cuil'])) ? 'selected' : ''; ?>>No presentó requisito</option>
    </select>
</div>



                
                <div class="row">
                    <label for="carreras">Carrera:</label>
<select id="carreras" name="carreras" class="input-text" required>
    <?php
    // Consulta para obtener todas las carreras
    $sql_carreras = "SELECT idCarrera, nombre_carrera FROM carreras";
    $result_carreras = $conexion->query($sql_carreras);

    // Verificar si hay resultados
    if ($result_carreras->num_rows > 0) {
        while ($row_carrera = $result_carreras->fetch_assoc()) {
            // Comprobar si el estudiante está inscrito en esta carrera
            $sql_inscripcion = "SELECT 1 
                                FROM inscripcion_asignatura ia
                                WHERE ia.carreras_idCarrera = ? AND ia.alumno_legajo = ?";
            $stmt_inscripcion = $conexion->prepare($sql_inscripcion);
            $stmt_inscripcion->bind_param("ss", $row_carrera["idCarrera"], $legajo);
            $stmt_inscripcion->execute();
            $result_inscripcion = $stmt_inscripcion->get_result();

            // Marcar como seleccionada si hay inscripción
            $selected = $result_inscripcion->num_rows > 0 ? "selected" : "";

            // Generar opción del select
            echo "<option value='" . htmlspecialchars($row_carrera["idCarrera"]) . "' $selected>" . htmlspecialchars($row_carrera["nombre_carrera"]) . "</option>";
        }
    } else {
        echo "<option value=''>No se encontraron carreras</option>";
    }
    ?>
</select>
<br><br>

                </div>
                

                <br><input type="submit" value="Confirmar" name="Enviar" class="form-container__input">
            </form>
    <?php
        } else {
            echo "Error: No se encontró el estudiante con el legajo especificado.";
        }
    } else {
        echo "Error: ID de alumno no proporcionado.";
    }
    ?>
</div>


<style>
	.row {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.column.half {
    flex: 0 0 48%; /* Ajusta el ancho de cada columna a 48% para dos columnas con espacio entre ellas */
    margin-right: 2%; /* Espacio entre columnas */
}

.column.half:last-child {
    margin-right: 0; /* Remueve el espacio de la última columna en cada fila */
}

.form-container__input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
}

</style>


<!--   Core JS Files   -->
<script src="../../../assets/js/core/jquery.3.2.1.min.js"></script>

<script src="../../../assets/js/core/bootstrap.min.js"></script>


<!-- jQuery UI -->
<script src="../../../assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>


<!-- jQuery Scrollbar -->
<script src="../../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Azzara JS -->
<script src="../../../assets/js/ready.min.js"></script>

<script>
 function mostrarAlertaExitosa() {
    alert("Registro completado con éxito!");
}

function closeSuccessMessage() {
    // Aquí puedes agregar código para cerrar cualquier mensaje de éxito o realizar acciones después del envío
    console.log("El formulario se ha enviado correctamente.");
}
</script>

</body>
</html>

