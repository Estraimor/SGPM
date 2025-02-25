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
$rolesPermitidos = ['1', '2', '3'];

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
<li class="nav-item active">
    <a data-toggle="collapse" href="#preceptorUtilities">
        <i class="fas fa-toolbox"></i>
        <p>Utilidades<br> del Preceptor</p>
        <span class="caret"></span>
    </a>
    <div class="collapse show" id="preceptorUtilities">
        <ul class="nav nav-collapse">
            <li>
                <a href="./Administracion/Profesores/pre_lista_promocionados.php">
                    <span class="sub-item">Actas Volantes Promocionados</span>
                </a>
            </li>
			<li class="active">
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
    <h1>Gestión de Actas Volantes Estudiantes Regulares </h1>
    <form method="POST" action="./config_actas_volantes/crear_acta_volante_regulares.php">
        <div class="cuadro" id="cuadroCarreras">
            
            <!-- Select de Carrera -->
            <label for="carrera">Seleccione una Carrera:</label>
            <select name="carrera" id="carrera" onchange="limpiarInfoEstudiantes(); cargarCursos();">
                <option value="">Seleccione una carrera</option>
                <?php
                $idPreceptor = $_SESSION['id'];
                $rolUsuario = $_SESSION["roles"];

                if ($rolUsuario == 1) {
                    $queryCarreras = "
                        SELECT DISTINCT c.idCarrera, c.nombre_carrera
                        FROM carreras c
                        INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                        INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                    ";
                } elseif ($rolUsuario == 5) {
                    $queryCarreras = "
                        SELECT DISTINCT c.idCarrera, c.nombre_carrera
                        FROM carreras c
                        INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                        INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                        WHERE c.profesor_idProrfesor = '{$idPreceptor}'
                    ";
                } else {
                    $queryCarreras = "
                        SELECT DISTINCT c.idCarrera, c.nombre_carrera
                        FROM carreras c
                        INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                        INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                        WHERE m.profesor_idProrfesor = '{$idPreceptor}'
                    ";
                }

                $resultCarreras = $conexion->query($queryCarreras);
                
                if ($resultCarreras->num_rows > 0) {
                    while ($row = $resultCarreras->fetch_assoc()) {
                        echo '<option value="' . $row['idCarrera'] . '">' . htmlspecialchars($row['nombre_carrera']) . '</option>';
                    }
                } else {
                    echo '<option value="">No hay carreras disponibles</option>';
                }
                ?>
            </select>

            <!-- Select de Curso -->
            <label for="curso">Seleccione un Curso:</label>
            <select name="curso" id="curso" onchange="cargarComisiones();limpiarInfoEstudiantes();">
                <option value="">Seleccione un curso</option>
            </select>

            <!-- Select de Comisión -->
            <label for="comision">Seleccione una Comisión:</label>
            <select name="comision" id="comision" onchange="habilitarMaterias();limpiarInfoEstudiantes();">
                <option value="">Seleccione una comisión</option>
            </select>

            <!-- Select de Llamado -->
            <label for="llamado">Seleccione un Llamado:</label>
            <select name="llamado" id="llamado" onchange="limpiarInfoEstudiantes(); habilitarMaterias();">
                <option hidden>Selecciona un llamado</option>
                <option value="1">Primer llamado</option>
                <option value="2">Segundo llamado</option>
            </select>

            <!-- Select de Tanda -->
            <label for="tanda">Seleccione una Tanda:</label>
            <select name="tanda" id="tanda" onchange="limpiarInfoEstudiantes(); habilitarMaterias();">
                <option hidden>Selecciona una tanda</option>
                <option value="1">Tanda 1</option>
                <option value="2">Tanda 2</option>
                <option value="3">Tanda 3</option>
                <option value="4">Tanda 4</option>
                <option value="5">Tanda 5</option>
                <option value="6">Tanda 6</option>
                
            </select>

            <!-- Select de Año -->
            <label for="anio">Seleccione un Año:</label>
            <select name="anio" id="anio" onchange="limpiarInfoEstudiantes(); habilitarMaterias();">
                <option hidden>Selecciona un año</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
            </select>
        </div>

        <!-- Select de Materias -->
        <div class="cuadro" id="cuadroMaterias">
            <label for="materias">Seleccione una Materia:</label>
            <div id="materias"></div>
        </div>

        <!-- Información de Alumnos -->
        <div class="cuadro" id="infoAlumnos" style="display:none;">
            <h2>Información de Mesa</h2>
            <p id="numAlumnos">Información de los alumnos aparecerá aquí.</p>
        </div>

        <div style="clear: both;"></div>
        <button class="boton" type="submit">Generar Acta Volante</button>
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
    function cargarCursos() {
        const carrera = document.getElementById('carrera').value;
        const cursoSelect = document.getElementById('curso');
        const comisionSelect = document.getElementById('comision');
        
        limpiarInfoEstudiantes();
        
        cursoSelect.innerHTML = '<option value="">Seleccione un curso</option>';
        comisionSelect.innerHTML = '<option value="">Seleccione una comisión</option>';
        
        if (carrera) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `./config_actas_volantes/obtener_cursos.php?idCarrera=${carrera}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        cursoSelect.innerHTML = xhr.responseText;
                    } else {
                        console.error('Error al cargar los cursos:', xhr.statusText);
                    }
                }
            };
            xhr.send();
        }
    }

    function cargarComisiones() {
        const curso = document.getElementById('curso').value;
        const comisionSelect = document.getElementById('comision');
        
        limpiarInfoEstudiantes();
        
        comisionSelect.innerHTML = '<option value="">Seleccione una comisión</option>';
        
        if (curso) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `./config_actas_volantes/obtener_comisiones.php?idCurso=${curso}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        comisionSelect.innerHTML = xhr.responseText;
                    } else {
                        console.error('Error al cargar las comisiones:', xhr.statusText);
                    }
                }
            };
            xhr.send();
        }
    }

    function limpiarInfoEstudiantes() {
        document.getElementById('numAlumnos').innerHTML = '';
        document.getElementById('infoAlumnos').style.display = 'none';
        document.getElementById('materias').innerHTML = '<p>Seleccione una carrera, curso, comisión, llamado, tanda y año para cargar materias.</p>';
    }

    function habilitarMaterias() {
        const carrera = document.getElementById('carrera').value;
        const curso = document.getElementById('curso').value;
        const comision = document.getElementById('comision').value;
        const llamado = document.getElementById('llamado').value;
        const tanda = document.getElementById('tanda').value;
        const anio = document.getElementById('anio').value;

        if (carrera && curso && comision && llamado && tanda && anio) {
            cargarMaterias(carrera, curso, comision, llamado, tanda, anio);
        } else {
            document.getElementById('materias').innerHTML = '<p>Por favor, selecciona todos los valores para cargar las materias.</p>';
        }
    }

    function cargarMaterias(carrera, curso, comision, llamado, tanda, anio) {
        const xhr = new XMLHttpRequest();
        xhr.open(
            'GET',
            `./config_actas_volantes/obtener_materias.php?idCarrera=${carrera}&idCurso=${curso}&idComision=${comision}&llamado=${llamado}&tanda=${tanda}&anio=${anio}`,
            true
        );
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    document.getElementById('materias').innerHTML = xhr.responseText;

                    // Añadir eventos a los checkboxes de materias
                    const checkboxes = document.querySelectorAll('#materias input[type="checkbox"]');
                    checkboxes.forEach(function (checkbox) {
                        checkbox.addEventListener('change', function () {
                            if (this.checked) {
                                // Desmarcar otros checkboxes
                                checkboxes.forEach(function (cb) {
                                    if (cb !== checkbox) cb.checked = false;
                                });

                                obtenerDetallesAlumnos(carrera, this.value, llamado, tanda, anio);
                            } else {
                                document.getElementById('infoAlumnos').style.display = 'none';
                            }
                        });
                    });
                } else {
                    console.error('Error al cargar las materias:', xhr.statusText);
                    document.getElementById('materias').innerHTML = '<p>Error al cargar las materias. Inténtelo de nuevo.</p>';
                }
            }
        };
        xhr.send();
    }

    function obtenerDetallesAlumnos(carrera, materia, llamado, tanda, anio) {
        const xhr = new XMLHttpRequest();
        xhr.open(
            'GET',
            `./config_actas_volantes/obtener_cantidad_alumnos_inscriptos.php?idCarrera=${carrera}&idMateria=${materia}&llamado=${llamado}&tanda=${tanda}&anio=${anio}`,
            true
        );
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    document.getElementById('numAlumnos').innerHTML = xhr.responseText;
                    document.getElementById('infoAlumnos').style.display = 'block';
                } else {
                    console.error('Error al obtener los detalles de los alumnos:', xhr.statusText);
                    document.getElementById('numAlumnos').innerHTML = 'Error al cargar la información de los alumnos.';
                }
            }
        };
        xhr.send();
    }
</script>



<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('../sw.js')
      .then(() => console.log('Service Worker registrado'))
      .catch((error) => console.error('Error al registrar el Service Worker', error));
  }
</script>
<style>
/* Títulos */
h1 {
    font-size: 24px;
    color: #c0392b; /* Color rojo */
    border-bottom: 2px solid #e74c3c;
    padding-bottom: 10px;
    margin-bottom: 20px;
    text-align: center;
    margin-top: 5px;
}

/* Contenedores de los cuadros */
.cuadro {
    float: left;
    width: 30%;
    padding: 10px;
    margin: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.cuadro label, .cuadro h2 {
    font-weight: bold;
    margin-bottom: 5px;
    color: #c0392b;
}

.cuadro p, .cuadro select, .cuadro div {
    margin-bottom: 10px;
}

/* Estilos para el cuadro de materias */
#cuadroMaterias {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin: 10px;
}

/* Estilos para la lista de materias */
#materias {
    margin: 10px 0;
}

/* Estilos para cada materia en la lista */
#materias label {
    display: block;
    padding: 8px 0;
    border-bottom: 1px solid #e0e0e0; /* Línea separadora fina y sutil */
    font-size: 16px;
    color: #333;
}

/* Elimina la línea del último elemento */
#materias label:last-child {
    border-bottom: none;
}

/* Estilos para los checkboxes */
#materias input[type="checkbox"] {
    width: 20px; /* Aumenta el tamaño del checkbox */
    height: 20px; /* Aumenta el tamaño del checkbox */
    margin-right: 10px; /* Espacio entre el checkbox y el texto */
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); /* Sombra diagonal */
    accent-color: #c0392b; /* Color rojo */
}

/* Formulario */
form {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    margin: 15px;
}

label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #555;
}

select, .boton {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
    font-size: 16px;
}

select {
    background-color: #fdfdfd;
}

.boton {
    background-color: #c0392b; /* Color rojo */
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
    width: auto;
    margin: auto;
}

.boton:hover {
    background-color: #a93226;
}

/* Ajustes de respuesta */
@media (max-width: 900px) {
    .cuadro {
        width: 100%;
        margin-bottom: 20px;
    }

    .boton {
        width: 100%;
    }
}


</style>



</body>
</html>

