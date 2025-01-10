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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- CSS Files -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/azzara.min.css">
	

	<!-- CSS Just for demo purpose, don't include it in your project -->
<!--	<link rel="stylesheet" href="assets/css/demo.css">-->
<!--	<link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">-->
<!--<script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>-->
	
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
			<li class="nav-item active">
                <a data-toggle="collapse" href="#alumnos">
                    <i class="fas fa-file-alt"></i>
                    <p>Utilidades<br> Administrativas</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse show" id="alumnos">
                    <ul class="nav nav-collapse">
					<?php if ($rolUsuario == '1'): ?>
					<li class="active">
						<a href="./gestionar_mesas_finales.php">
							<span class="sub-item">Gestión de Mesas</span>
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
    <h2>Agregar Fechas de Mesas Finales</h2>

    <form action="./mesa_finales/guardar_fechas_mesas_finales.php" method="POST">

        <!-- Contenedor dinámico para las combinaciones de carrera y materia -->
        <div id="mesa-container">
            <div class="mesa-item">
                <!-- Selección de carrera principal -->
                <label for="carrera">Selecciona la Carrera:</label><br>
                <select class="carrera" name="carrera[]">
                    <option value="">Selecciona una carrera</option>
                    <?php
                    // Obtener el ID del preceptor y el rol desde la sesión
            $idPreceptor = $_SESSION['id'];  // Asegúrate de tener el ID del preceptor guardado en la sesión
            $rolUsuario = $_SESSION["roles"]; // Asegúrate de tener el rol guardado en la sesión

            // Definimos la consulta dependiendo del rol
            if ($rolUsuario == 1) {
                // Si el rol es 1, mostrar todas las carreras que tienen materias con personas asociadas en inscripcion_asignatura
                $sql_mater = "
                    SELECT DISTINCT c.idCarrera, c.nombre_carrera
                    FROM carreras c
                    INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                    INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                ";
            } elseif ($rolUsuario == 5) {
                // Si el rol es 5, mostrar todas las carreras asignadas a las materias del preceptor y con personas asociadas en inscripcion_asignatura
                $sql_mater = "
                    SELECT DISTINCT c.idCarrera, c.nombre_carrera
                    FROM carreras c
                    INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                    INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                    WHERE c.profesor_idProrfesor = '{$idPreceptor}'
                ";
            } else {
                // Si el rol no es 1 ni 5, mostrar solo las carreras asignadas al preceptor y con personas asociadas en inscripcion_asignatura
                $sql_mater = "
                    SELECT DISTINCT c.idCarrera, c.nombre_carrera
                    FROM carreras c
                    INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                    INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                    WHERE m.profesor_idProrfesor = '{$idPreceptor}'
                ";
            }
                    $result = mysqli_query($conexion, $sql_mater);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
                    }
                    ?>
                </select><br><br>

                <!-- Selección de materia principal que depende de la carrera -->
                <label for="materia">Selecciona la Materia:</label><br>
                <select class="materia" name="materias[]">
                    <option value="">Selecciona una carrera primero</option>
                </select><br><br>
            </div>
        </div>

        <!-- Botón para agregar más combinaciones de carrera y materia -->
        <button type="button" id="agregar-mesa">Agregar Unidad Curricular</button><br><br>

        <!-- Contenedor para los campos de fecha, llamado, tanda, cupo -->
        <div class="contenedor-detalles">
            <div>
                <label for="fecha">Fecha:</label>
                <input type="datetime-local" id="fecha" name="fecha">
            </div>
            <div>
                <label for="llamado">Llamado:</label>
                <input type="number" id="llamado" name="llamado">
            </div>
            <div>
                <label for="tanda">Tanda:</label>
                <input type="number" id="tanda" name="tanda">
            </div>
            <div>
                <label for="cupo">Cupo:</label>
                <input type="number" id="cupo" name="cupo">
            </div>
        </div>

        <!-- Primer conjunto de selects para la mesa pedagógica asociada -->
        <div id="mesa-pedagogica-container">
            <h3>Agregar Mesa Pedagógica Asociada 1</h3>
            <div class="mesa-pedagogica-item">
                <label for="carrera-pedagogica-1">Selecciona la Carrera Pedagógica 1:</label><br>
                <select class="carrera" name="carrera_pedagogica_1">
                    <option value="">Selecciona una carrera</option>
                    <?php
                    $query = "SELECT * FROM carreras";
                    $result = mysqli_query($conexion, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
                    }
                    ?>
                </select><br><br>

                <label for="materia-pedagogica-1">Selecciona la Materia Pedagógica 1:</label><br>
                <select class="materia" name="materia_pedagogica_1">
                    <option value="">Selecciona una carrera primero</option>
                </select><br><br>
            </div>
        </div>

        <!-- Segundo conjunto de selects para la mesa pedagógica asociada -->
        <div id="mesa-pedagogica-container">
            <h3>Agregar Mesa Pedagógica Asociada 2</h3>
            <div class="mesa-pedagogica-item">
                <label for="carrera-pedagogica-2">Selecciona la Carrera Pedagógica 2:</label><br>
                <select class="carrera" name="carrera_pedagogica_2">
                    <option value="">Selecciona una carrera</option>
                    <?php
                    $query = "SELECT * FROM carreras";
                    $result = mysqli_query($conexion, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
                    }
                    ?>
                </select><br><br>

                <label for="materia-pedagogica-2">Selecciona la Materia Pedagógica 2:</label><br>
                <select class="materia" name="materia_pedagogica_2">
                    <option value="">Selecciona una carrera primero</option>
                </select><br><br>
            </div>
        </div>

        <input type="submit" value="Agregar Mesa">
    </form>







   <h2>Mesas Finales</h2>
<input type="text" id="searchInput" placeholder="Buscar en la tabla..." style="margin-bottom: 10px; padding: 5px; width: 100%; border: 1px solid #ddd;">

<table border="1" id="tabla_mesas">
    <thead>
        <tr>
            <th>Unidad Curricular</th>
            <th>Fecha</th>
            <th>Llamado</th>
            <th>Tanda</th>
            <th>Cupo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT fm.idfechas_mesas_finales, m.Nombre AS nombre_materia, t.fecha, t.llamado, t.tanda, t.cupo, c.nombre_carrera
                  FROM fechas_mesas_finales fm
                  JOIN materias m ON fm.materias_idMaterias = m.idMaterias
                  JOIN tandas t ON fm.tandas_idtandas = t.idtandas
                  JOIN carreras c ON m.carreras_idCarrera  = c.idCarrera";
        $result = mysqli_query($conexion, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $carrera_prefix = substr($row['nombre_carrera'], 0, 4);
                $carrera_suffix = substr($row['nombre_carrera'], -5);
                $nombre_materia_completo = $carrera_prefix . " " . $row['nombre_materia'] . " " . $carrera_suffix;

                echo "<tr>";
                echo "<td>" . htmlspecialchars($nombre_materia_completo) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
                echo "<td>" . ($row['llamado'] == 1 ? 'Primer Llamado' : 'Segundo Llamado') . "</td>";
                echo "<td>" . htmlspecialchars($row['tanda']) . "</td>";
                echo "<td>" . htmlspecialchars($row['cupo']) . "</td>";
                echo "<td>
                        <button class='editar' data-id='" . htmlspecialchars($row['idfechas_mesas_finales']) . "'>Modificar</button>
                        <button class='eliminar' data-id='" . htmlspecialchars($row['idfechas_mesas_finales']) . "'>Eliminar</button>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No se encontraron mesas finales.</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Fondo oscuro del modal -->
<div id="modal-background" style="display:none;"></div>

<!-- Modal para editar mesa final -->
<div id="modal-editar" style="display:none;">
    <form id="form-editar">
        <input type="hidden" id="modal_id_mesa" name="id_mesa">

        <label for="modal_fecha">Fecha:</label>
        <input type="datetime-local" id="modal_fecha" name="fecha"><br><br>

        <label for="modal_llamado">Llamado:</label>
        <input type="number" id="modal_llamado" name="llamado"><br><br>

        <label for="modal_tanda">Tanda:</label>
        <input type="number" id="modal_tanda" name="tanda"><br><br>

        <label for="modal_cupo">Cupo:</label>
        <input type="number" id="modal_cupo" name="cupo"><br><br>

        <button type="submit">Guardar cambios</button>
        <button type="button" id="cerrar-modal">Cancelar</button>
    </form>
</div>


<script>
 $(document).ready(function() {
    // Cargar las materias para la carrera principal
    $(document).on('change', '.carrera', function() {
        var idCarrera = $(this).val();
        var materiaSelect = $(this).closest('.mesa-item').find('.materia');

        if (idCarrera) {
            $.ajax({
                type: 'POST',
                url: './mesa_finales/obtener_materias_mesas_finales.php',
                data: { idCarrera: idCarrera },
                success: function(html) {
                    materiaSelect.html(html);
                },
                error: function() {
                    alert('Error al cargar las materias.');
                }
            });
        } else {
            materiaSelect.html('<option value="">Selecciona una carrera primero</option>');
        }
    });

    // Cargar las materias para la primera carrera pedagógica
    $(document).on('change', 'select[name="carrera_pedagogica_1"]', function() {
        var idCarreraPedagogica = $(this).val();
        var materiaSelectPedagogica = $('select[name="materia_pedagogica_1"]');

        if (idCarreraPedagogica) {
            $.ajax({
                type: 'POST',
                url: './mesa_finales/obtener_materias_mesas_finales.php',
                data: { idCarrera: idCarreraPedagogica },
                success: function(html) {
                    materiaSelectPedagogica.html(html);
                },
                error: function() {
                    alert('Error al cargar las materias pedagógicas.');
                }
            });
        } else {
            materiaSelectPedagogica.html('<option value="">Selecciona una carrera primero</option>');
        }
    });

    // Cargar las materias para la segunda carrera pedagógica
    $(document).on('change', 'select[name="carrera_pedagogica_2"]', function() {
        var idCarreraPedagogica = $(this).val();
        var materiaSelectPedagogica = $('select[name="materia_pedagogica_2"]');

        if (idCarreraPedagogica) {
            $.ajax({
                type: 'POST',
                url: './mesa_finales/obtener_materias_mesas_finales.php',
                data: { idCarrera: idCarreraPedagogica },
                success: function(html) {
                    materiaSelectPedagogica.html(html);
                },
                error: function() {
                    alert('Error al cargar las materias pedagógicas.');
                }
            });
        } else {
            materiaSelectPedagogica.html('<option value="">Selecciona una carrera primero</option>');
        }
    });


    // Evento para agregar una nueva combinación de carrera y materia
    $('#agregar-mesa').on('click', function() {
        var nuevaMesa = `
            <div class="mesa-item">
                <label for="carrera">Selecciona la Carrera:</label><br>
                <select class="carrera" name="carrera[]">
                    <option value="">Selecciona una carrera</option>
                    <?php
                    $query = "SELECT * FROM carreras";
                    $result = mysqli_query($conexion, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
                    }
                    ?>
                </select><br><br>

                <label for="materia">Selecciona la Materia:</label><br>
                <select class="materia" name="materias[]">
                    <option value="">Selecciona una carrera primero</option>
                </select><br><br>

                <button type="button" class="eliminar-mesa">Eliminar Unidad Curricular</button><br><br>
            </div>
        `;
        $('#mesa-container').append(nuevaMesa);
    });

    // Evento para eliminar una mesa recién agregada
    $(document).on('click', '.eliminar-mesa', function() {
        $(this).closest('.mesa-item').remove();
    });
  });
</script>

<script>
  $(document).ready(function() {
    // Filtro personalizado para buscar en la tabla
    $('#searchInput').on('keyup', function() {
        let filter = $(this).val().toUpperCase();
        let rows = $('#tabla_mesas tbody tr');

        rows.each(function() {
            let row = $(this);
            let match = false;

            row.find('td').each(function(index) {
                if (index < 5 && $(this).text().toUpperCase().includes(filter)) { // Excluye la columna Acciones
                    match = true;
                }
            });

            row.toggle(match);
        });
    });

    // Evento para abrir el modal con datos desde AJAX
    $('.editar').on('click', function() {
        let idMesa = $(this).data('id');

        $.ajax({
            type: 'POST',
            url: './obtener_mesa_final.php',
            data: { idMesa: idMesa },
            dataType: 'json',
            success: function(data) {
                if (data) {
                    $('#modal_id_mesa').val(data.idfechas_mesas_finales);
                    $('#modal_fecha').val(data.fecha);
                    $('#modal_llamado').val(data.llamado);
                    $('#modal_tanda').val(data.tanda);
                    $('#modal_cupo').val(data.cupo);

                    $('#modal-background').show();
                    $('#modal-editar').show();
                } else {
                    alert('No se pudieron cargar los datos.');
                }
            },
            error: function() {
                alert('Error al obtener los datos.');
            }
        });
    });

    // Cerrar el modal al hacer clic en "Cancelar" o en el fondo oscuro
    $('#cerrar-modal, #modal-background').on('click', function() {
        $('#modal-background').hide();
        $('#modal-editar').hide();
    });

    // Guardar cambios usando AJAX
    $('#form-editar').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: './mesa_finales/modificar_mesa_final.php',
            data: $(this).serialize(),
            success: function(response) {
                alert('Mesa actualizada correctamente');
                $('#modal-background').hide();
                $('#modal-editar').hide();
                location.reload(); // Recargar la página para reflejar los cambios
            },
            error: function() {
                alert('Error al guardar los cambios.');
            }
        });
    });

    // Evento para eliminar mesa
    $('.eliminar').on('click', function() {
        let idMesa = $(this).data('id');
        if (confirm('¿Estás seguro de que deseas eliminar esta mesa?')) {
            $.ajax({
                type: 'POST',
                url: './mesa_finales/eliminar_mesa_final.php',
                data: { idMesa: idMesa },
                success: function(response) {
                    alert('Mesa eliminada correctamente');
                    location.reload(); // Recargar la página para reflejar los cambios
                },
                error: function() {
                    alert('Error al eliminar la mesa.');
                }
            });
        }
    });
});
</script>



<!--   Core JS Files   -->
<script src="assets/js/core/jquery.3.2.1.min.js"></script>

<script src="assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>


<!-- jQuery Scrollbar -->
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Azzara JS -->
<script src="assets/js/ready.min.js"></script>


<style>
    .contenido {
    position: absolute;
    top: 55px;
    left: 270px;
    width: calc(100% - 270px);
    background-color: #ffffff;
    background-image: url(./assets/img/fondo.png);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: local;
    padding: 20px;
    min-height: 100%;
    background-attachment: local; /* Hace que el fondo se mueva con el contenido */
}
@media (max-width: 768px) {
    .contenido {
        width: 100%;
        left: 0;
    }
 }

 h2 {
    color: #f3545d;
    margin-bottom: 15px;
    font-size: 24px;
    text-align: center;
  }

  form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 20px;
    align-items: center;
  }

  label {
    font-weight: bold;
    margin-bottom: 5px;
  }

  select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  input[type="datetime-local"], 
  input[type="number"] {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  input[type="submit"], 
  button {
    background-color: #f3545d;
    color: #ffffff;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
    margin-top: 10px;
  }

  input[type="submit"]:hover, 
  button:hover {
    background-color: #c13d4a;
  }

  /* Ajuste para el contenedor de los selects */
  #mesa-container {
    max-width: 600px; /* Ancho máximo para reducir el tamaño */
    margin: 0 auto; /* Centramos el contenedor */
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  .mesa-item {
    background-color: #f9f9f9;
    padding: 15px;
    border: 1px solid #eaeaea;
    border-radius: 8px;
  }

  /* Ajuste para el contenedor de los 4 inputs */
  .contenedor-detalles {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
  }

  .contenedor-detalles div {
    flex: 1 1 calc(50% - 10px); /* Dos columnas con espacio entre ellas */
  }

  .contenedor-detalles input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
  }

  /* Ajuste para la tabla */
  #tabla_mesas {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  #tabla_mesas thead {
    background-color: #f3545d;
    color: #ffffff;
    font-weight: bold;
  }

  #tabla_mesas th, #tabla_mesas td {
    padding: 10px;
    text-align: left;
    border: 1px solid #eaeaea;
  }

  #tabla_mesas tbody tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  #tabla_mesas tbody tr:hover {
    background-color: #ffe4e8;
  }

  .editar, .eliminar {
    background-color: #f3545d;
    color: #ffffff;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 5px;
    font-size: 14px;
  }

  .eliminar {
    background-color: #c13d4a;
  }

  .editar:hover, .eliminar:hover {
    background-color: #b0303a;
  }

  /* Modal */
  #modal-background {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10;
  }

  #modal-editar {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    width: 80%;
    max-width: 400px;
    z-index: 11;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  #modal-editar form {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  #modal-editar button {
    margin-top: 10px;
  }

  /* Ajuste para pantallas pequeñas */
  @media (max-width: 768px) {
    .contenedor-detalles {
      flex-direction: column;
    }

    .contenedor-detalles div {
      flex: 1 1 100%;
    }
  }

  @media (max-width: 480px) {
    #modal-editar {
      width: 90%;
    }
  }
    </style>



</body>
</html>

