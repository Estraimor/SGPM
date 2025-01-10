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
	<link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
     <script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/azzara.min.css">
	

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
			<li class="nav-item active">
    <a data-toggle="collapse" href="#preinscriptos">
        <i class="fas fa-user-plus"></i>
        <p>Preinscriptos</p>
        <span class="caret"></span>
    </a>
    <div class="collapse show" id="preinscriptos">
        <ul class="nav nav-collapse">
            <li class="active">
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
<?php
// Consultas para contar el número de registros por carrera en la tabla pre_inscripciones
$sql_enfermeria = "SELECT COUNT(*) AS total FROM pre_inscripciones WHERE carrera = 'Enfermería'";
$sql_comercializacion = "SELECT COUNT(*) AS total FROM pre_inscripciones WHERE carrera = 'Comercialización y Marketing'";
$sql_acompanante = "SELECT COUNT(*) AS total FROM pre_inscripciones WHERE carrera = 'Acompañante Terapéutico'";
$sql_automatizacion = "SELECT COUNT(*) AS total FROM pre_inscripciones WHERE carrera = 'Automatización y Robótica'";
$sql_total = "SELECT COUNT(*) AS total FROM pre_inscripciones";

// Ejecutar las consultas
$result_enfermeria = $conexion->query($sql_enfermeria)->fetch_assoc()['total'];
$result_comercializacion = $conexion->query($sql_comercializacion)->fetch_assoc()['total'];
$result_acompanante = $conexion->query($sql_acompanante)->fetch_assoc()['total'];
$result_automatizacion = $conexion->query($sql_automatizacion)->fetch_assoc()['total'];
$result_total = $conexion->query($sql_total)->fetch_assoc()['total'];

// Consulta para obtener los estudiantes y contar por carrera basada en inscripcion_asignatura
$profesor_id = $_SESSION["id"];
$sql1 = "SELECT a.*, c.nombre_carrera
         FROM inscripcion_asignatura ia
         INNER JOIN alumno a ON ia.alumno_legajo = a.legajo
         INNER JOIN preceptores p ON p.carreras_idCarrera = ia.carreras_idCarrera
         INNER JOIN carreras c ON ia.carreras_idCarrera = c.idCarrera
         WHERE a.estado = '3'
         GROUP BY ia.alumno_legajo";

$query1 = mysqli_query($conexion, $sql1);

// Inicializamos los contadores
$contadorAcompañanteTerapeutico = 0;
$contadorEnfermeria = 0;
$contadorComercializacionMarketing = 0;
$contadorAutomatizacionRobotica = 0;
$totalEstudiantes = 0;

// Recorremos los resultados y calculamos los contadores
while ($datos = mysqli_fetch_assoc($query1)) {
    $nombre_carrera = trim($datos['nombre_carrera']); // Eliminar posibles espacios en blanco

    // Condicionales para incrementar los contadores según la carrera
    if (preg_match('/acomp/i', $nombre_carrera)) {
        $contadorAcompañanteTerapeutico++;
    } elseif (preg_match('/enfer/i', $nombre_carrera)) {
        $contadorEnfermeria++;
    } elseif (preg_match('/comercial/i', $nombre_carrera) || preg_match('/marketing/i', $nombre_carrera)) {
        $contadorComercializacionMarketing++;
    } elseif (preg_match('/auto/i', $nombre_carrera) || preg_match('/robot/i', $nombre_carrera)) {
        $contadorAutomatizacionRobotica++;
    }

    // Incrementamos el contador total de estudiantes
    $totalEstudiantes++;
}
?>

<!-- Contadores de pre_inscripciones -->
<h2>Pre-Inscriptos</h2>
<div class="contadores">
	
    <p><strong>Enfermería:</strong> <?php echo $result_enfermeria; ?></p>
	<p><strong>Acompañante Terapéutico:</strong> <?php echo $result_acompanante; ?></p>
    <p><strong>Comercialización y Marketing:</strong> <?php echo $result_comercializacion; ?></p>
    
    <p><strong>Automatización y Robótica:</strong> <?php echo $result_automatizacion; ?></p>
    <p><strong>Total:</strong> <?php echo $result_total; ?></p>
</div>

<!-- Contadores de inscripcion_asignatura -->
<h2>Inscriptos 2025</h2>
<div class="contadores">
<p>Enfermería: <?php echo $contadorEnfermeria; ?></p>
    <p>Acompañante Terapéutico: <?php echo $contadorAcompañanteTerapeutico; ?></p>
   
    <p>Comercialización Marketing: <?php echo $contadorComercializacionMarketing; ?></p>
    <p>Automatización Robótica: <?php echo $contadorAutomatizacionRobotica; ?></p>
    <p>Total: <?php echo $totalEstudiantes; ?></p>
</div>

    <?php
    $sql = "SELECT * FROM pre_inscripciones";
    $result = $conexion->query($sql);
    ?>

<table id="tabla">
    <thead>
        <tr>
		<th width="105px">Apellido</th>
            <th>Nombre</th>
           
            <th width="95px">DNI</th>
            <th width="100px">Fecha de Nac.</th>
            <th width="45px">Edad</th>
            <th>Celular</th>
            <th>Correo</th>
            <th>Carrera</th>
            <th width="120px">Acciones</th>
        </tr>
    </thead>
	<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["apellido"] . "</td>";  
        echo "<td>" . $row["nombre"] . "</td>";
        echo "<td>" . $row["DNI"] . "</td>";

        // Extraer la fecha de nacimiento de la base de datos
        $fecha_nacimiento = $row["fecha_nacimiento"];

        // Convertir la fecha de nacimiento en un objeto DateTime
        $fecha_nacimiento_dt = new DateTime($fecha_nacimiento);

        // Obtener la fecha actual
        $fecha_actual = new DateTime();

        // Calcular la diferencia entre la fecha de nacimiento y la fecha actual
        $edad = $fecha_actual->diff($fecha_nacimiento_dt)->y;

        // Formatear la fecha de nacimiento como día/mes/año
        $fecha_nacimiento_formateada = $fecha_nacimiento_dt->format('d/m/Y');

        // Mostrar la fecha de nacimiento formateada y la edad
        echo "<td>" . $fecha_nacimiento_formateada . "</td>";
        echo "<td>" . $edad . "</td>";
        echo "<td>" . $row["celular"] . "</td>";
        echo "<td>" . $row["correo"] . "</td>";
        echo "<td>" . $row["carrera"] . "</td>";
        echo "<td>
                <button class='delete' onclick='confirmDelete(" . $row["DNI"] . ")'><i class='fa fa-trash'></i></button>
                <button class='print' onclick='imprimir(" . $row["DNI"] . ")'><i class='fa fa-check'></i></button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No se encontraron registros</td></tr>";
}
?>
    </table>
    
    <form action="exel_alumnos_pre_inscriptos.php" method="POST">
            <button type="submit">Descargar Excel Alumnos Pre-inscriptos</button>
        </form>
        <form action="exel_alumnos_2025.php" method="POST">
            <button type="submit">Descargar Excel inscriptos 2025</button>
        </form>
</div>


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
	.contadores p{
		font-size: 15px !important;
		
	}
	.contadores {
    display: block !important;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 10px;
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 5px;
}
 }
.contadores {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 10px;
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.contadores p {
    margin: 0 15px;
    padding: 5px;
    font-size: 16px;
    color: #f3545d; /* Color rojo */
    font-weight: bold;
}

button {
    border: none;
    cursor: pointer;
    padding: 10px;
    border-radius: 5px;
    margin-right: 5px;
}

button.delete {
    background-color: #ff0000; /* Fondo rojo */
    color: #ffffff; /* Icono blanco */
}

button.delete i {
    font-size: 18px;
}

button.print {
    background-color: #28a745; /* Fondo verde */
    color: #ffffff; /* Icono blanco */
}

button.print i {
    font-size: 18px;
}
/* Estilo general de la tabla */
#tabla {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 14px; /* Tamaño de fuente reducido */
    text-align: left;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    table-layout: fixed; /* Fija el ancho de las celdas */
}

/* Encabezado de la tabla */
#tabla th {
    background-color: #f3545d; /* Fondo rojo */
    color: #ffffff; /* Texto blanco */
    padding: 8px; /* Padding reducido */
    text-align: left;
    font-weight: 600; /* Ligero énfasis en el texto */
    white-space: nowrap; /* Evita que el texto se desborde */
    overflow: hidden;
    text-overflow: ellipsis; /* Añade "..." si el texto es muy largo */
}


/* Celdas de la tabla */
#tabla td {
    padding: 8px; /* Padding reducido */
    border-bottom: 1px solid #dddddd;
    white-space: nowrap; /* Evita que el texto se desborde */
    overflow: hidden;
    text-overflow: ellipsis; /* Añade "..." si el texto es muy largo */
}

/* Fila impar de la tabla */
#tabla tr:nth-child(odd) {
    background-color: #fafafa; /* Color muy suave */
}

/* Fila par de la tabla */
#tabla tr:nth-child(even) {
    background-color: #ffffff;
}

/* Efecto hover en las filas */
#tabla tr:hover {
    background-color: #f2f2f2;
}

/* Estilo para los botones dentro de la tabla */
#tabla td button {
    padding: 6px 10px; /* Padding reducido */
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px; /* Fuente más pequeña */
}

/* Iconos de los botones */
#tabla td button i {
    font-size: 14px; /* Icono más pequeño */
}

/* Encabezado de la tabla fijo al hacer scroll */
#tabla th {
    position: sticky;
    top: 0;
}

/* Bordes redondeados para la tabla */
#tabla {
    border-radius: 6px;
    overflow: hidden;
}
</style>


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
// dataTables de Alumnos //
var myTable = document.querySelector("#tabla");
var dataTable = new DataTable(tabla,{ perPage: 10,
        perPageSelect: [25, 50, 100, 500],});


function confirmDelete(id) {
    if (confirm("¿Estás seguro de que deseas borrar este registro?")) {
        window.location.href = "eliminar_registro_pre_inscriptos.php?DNI=" + id;
    }
}

function imprimir(dni) {
    window.location.href = "inscripcion_pre_inscriptos.php?DNI=" + dni;
}
</script>


</body>
</html>

