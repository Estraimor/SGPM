<?php
session_start();
if (empty($_SESSION["id"])){header('Location: ../../../login/login.php');}

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
        $imagenBase64 = '../../assets/img/1361728.png';
    }
} else {
    echo "Error al cargar la imagen.";
}


?>

<!-- HTML para mostrar la imagen -->
<?php if ($imagenBase64 === '../../../assets/img/1361728.png') : ?>
    <!-- Mostrar imagen por defecto si no hay avatar -->
    <img src="../../../assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;">
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
        $imagenBase64 = '../../assets/img/1361728.png';
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
									<a class="dropdown-item" href="../../Perfil.php">Mi Perfil</a>
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
									<li>
										<a href="../Tecnicatura/ABM_estudiante/nuevo_estudiante.php">
											<span class="sub-item">Nuevo Estudiante</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li >
										<a href="../FP/nuevo_estudianteFP.php">
											<span class="sub-item">Nuevo Estudiante FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li class="active">
										<a href="../Tecnicatura/lista_estudiantes.php">
											<span class="sub-item">Lista Estudiantes</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../Tecnicatura/lista_estudiantes_2025.php">
											<span class="sub-item">Lista Estudiantes 2025</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../FP/lista_estudianteFP.php">
											<span class="sub-item">Lista Estudiantes FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="../Tecnicatura/Informes/informe_asistencia_tecnicaturas.php">
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
										<a href="../Tecnicatura/Informes/informe_lista_estudiantes.php">
											<span class="sub-item">Imprimir Lista de Estudiantes Técnicaturas</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../FP/informesFP/informe_lista_estudiantesFP.php">
											<span class="sub-item">Imprimir Lista de Estudiantes FP</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
									<li>
										<a href="../Tecnicatura/Falta_justificada/falta_justificada.php">
											<span class="sub-item">Justificar Falta</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="../Tecnicatura/Retirados/estudiantes_retirados.php">
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
                            <a href="../../index_asistencia.php">
                                <span class="sub-item">Estudiantes Técnicaturas</span>
                            </a>
                        </li>
						<?php endif; ?>
					<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                        <li>
                            <a href="../FP/ver_FPS.php">
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
                            <a href="../../ver_carreras.php">
                                <span class="sub-item">Estudiantes Técnicaturas</span>
                            </a>
                        </li>
						<?php endif; ?>
						<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                        <li>
                            <a href="../FP/ver_asistenciaFPS.php">
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
										<a href="../../Administracion/Profesores/alta_docente.php">
											<span class="sub-item">Alta Docentes</span>
										</a>
									</li>
									<li>
										<a href="../../Administracion/Profesores/lista_profesores.php">
											<span class="sub-item">Lista de Docentes</span>
										</a>
									</li>
									<li>
										<a href="../../Administracion/Profesores/materia_profesor.php">
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
                            <a href="../../proximamente.php">
                                <span class="sub-item">Nuevo Preceptor</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../proximamente.php">
                                <span class="sub-item">Lista de Preceptores</span>
                            </a>
                        </li>
                       
						<li>
							<a href="../../proximamente.php">
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
						<a href="../../Administracion/Profesores/acta_volante_materias.php">
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
                            <a href="../../Administracion/Profesores/pre_parciales.php">
                                <span class="sub-item">Gestión de Notas</span>
                            </a>
                        </li>
						<li>
                            <a href="../../pre_libro.php">
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
                <a href="../../lista_pre_inscriptos.php">
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
$legajo = $_GET['legajo'];
?>
<a href="imprimir_alumno.php?legajo=<?php echo $legajo; ?>" class="accion-button">Imprimir Datos <i class="fas fa-print"></i></a>
<br><br>

<?php
try {
    // **Datos del Estudiante**
    $html_datos_estudiante = '<h2 class="section-title">Datos del Estudiante</h2><br>';
    $html_datos_estudiante .= '<table class="styled-table" id="datos-estudiante">
        <tr><th>Apellido</th><th>Nombre</th><th>Carrera</th><th>DNI</th><th>CUIL</th><th>Legajo</th><th>Pagó</th></tr>';

    $sql_datos_alumno = "SELECT * FROM alumno a 
                         INNER JOIN inscripcion_asignatura ia ON ia.alumno_legajo = a.legajo 
                         INNER JOIN carreras c ON c.idCarrera = ia.carreras_idCarrera 
                         INNER JOIN comisiones co on ia.Comisiones_idComisiones = co.idComisiones
                         WHERE legajo = '$legajo'";
    $query_datos_alumno = mysqli_query($conexion, $sql_datos_alumno);

    if (!$query_datos_alumno) throw new Exception("Error al obtener los datos del alumno: " . mysqli_error($conexion));

    $row_datos_alumno = mysqli_fetch_assoc($query_datos_alumno);
    
    // Evaluar el valor de 'Pago' y asignar texto adecuado
    $pago_texto = "Se desconoce";
    if ($row_datos_alumno['Pago'] === '1') {
        $pago_texto = "SI";
    } elseif ($row_datos_alumno['Pago'] === '0') {
        $pago_texto = "NO";
    }

    $html_datos_estudiante .= "<tr>
        <td>{$row_datos_alumno['apellido_alumno']}</td>
        <td>{$row_datos_alumno['nombre_alumno']}</td>
        <td>{$row_datos_alumno['nombre_carrera']}</td>
        <td>{$row_datos_alumno['dni_alumno']}</td>
        <td>{$row_datos_alumno['cuil']}</td>
        <td>$legajo</td>
        <td>$pago_texto</td>
    </tr>";
    $html_datos_estudiante .= '</table>';
    echo $html_datos_estudiante;

    // **Lugar de Nacimiento**
    $html_lugar_nacimiento = '<h2 class="section-title">Lugar de Nacimiento</h2><br>';
    $html_lugar_nacimiento .= '<table class="styled-table" id="lugar-nacimiento">
        <tr><th>Ciudad de Nacimiento</th><th>Provincia de Nacimiento</th><th>País de Nacimiento</th><th>Fecha de Nacimiento</th><th>Edad</th></tr>';
    $html_lugar_nacimiento .= "<tr>
        <td>{$row_datos_alumno['ciudad_nacimiento']}</td>
        <td>{$row_datos_alumno['provincia_nacimiento']}</td>
        <td>{$row_datos_alumno['pais_nacimiento']}</td>
        <td>{$row_datos_alumno['fecha_nacimiento']}</td>
        <td>{$row_datos_alumno['edad']}</td>
    </tr>";
    $html_lugar_nacimiento .= '</table>';
    echo $html_lugar_nacimiento;

    // **Lugar de Domicilio**
    $html_lugar_domicilio = '<h2 class="section-title">Lugar de Domicilio</h2><br>';
    $html_lugar_domicilio .= '<table class="styled-table" id="lugar-domicilio">
        <tr><th>Calle</th><th>Provincia</th><th>Altura</th><th>Celular</th><th>Teléfono Urgencia</th></tr>';
    $html_lugar_domicilio .= "<tr>
        <td>{$row_datos_alumno['calle_domicilio']}</td>
        <td>{$row_datos_alumno['provincia_domicilio']}</td>
        <td>{$row_datos_alumno['numeracion_domicilio']}</td>
        <td>{$row_datos_alumno['celular']}</td>
        <td>{$row_datos_alumno['telefono_urgencias']}</td>
    </tr>";
    $html_lugar_domicilio .= '</table>';
    echo $html_lugar_domicilio;

    // **Datos Laborales**
    $html_datos_laborales = '<h2 class="section-title">Datos Laborales</h2><br>';
    $html_datos_laborales .= '<table class="styled-table" id="datos-laborales">
        <tr><th>Ocupación</th><th>Horario Laboral Desde</th><th>Horario Laboral Hasta</th><th>Observaciones</th></tr>';
    $html_datos_laborales .= "<tr>
        <td>{$row_datos_alumno['ocupacion']}</td>
        <td>Desde: {$row_datos_alumno['horario_laboral_desde']}</td>
        <td>Hasta: {$row_datos_alumno['horario_laboral_hasta']}</td>
        <td>{$row_datos_alumno['observaciones']}</td>
    </tr>";
    $html_datos_laborales .= '</table>';
    echo $html_datos_laborales;
    




    // **Usuario**
$html_datos_laborales = '<h2 class="section-title">Perfil</h2><br>';
$html_datos_laborales .= '<table class="styled-table" id="datos-laborales">
    <tr><th>Usuario</th><th>Password</th><th>Acción</th></tr>';
$html_datos_laborales .= "<tr>
    <td>{$row_datos_alumno['usu_alumno']}</td>
    <td>{$row_datos_alumno['pass_alumno']}</td>
    <td>
        <form method='post' action='restablecer_password.php' onsubmit='return confirmarRestablecimiento()'>
            <input type='hidden' name='legajo' value='$legajo'>
            <button type='submit' class='btn-restablecer'>Restablecer Contraseña</button>
        </form>
    </td>
</tr>";
$html_datos_laborales .= '</table>';
echo $html_datos_laborales;



    // **Usuario**
$html_datos_laborales = '<h2 class="section-title">Comision</h2><br>';
$html_datos_laborales .= '<table class="styled-table" id="datos-laborales">
    <tr><th>Comision</th></tr>';
$html_datos_laborales .= "<tr>
    <td>{$row_datos_alumno['comision']}</td>
    
</tr>";
$html_datos_laborales .= '</table>';
echo $html_datos_laborales;


    // // **Asistencias**
    // $html_asistencias = '<br><h2 class="section-title">Asistencias</h2><br>';
    // $html_asistencias .= '<table class="styled-table">
    //                     <tr>
    //                         <th>Materia</th>
    //                         <th>Porcentaje Presente</th>
    //                         <th>Porcentaje Ausente</th>
    //                         <th>Cantidad de Faltas Justificadas</th>
    //                     </tr>';

    // $sql_asistencias = "SELECT 
    //                         m.Nombre,
    //                         SUM(CASE WHEN a.1_Horario = 'Presente' OR a.2_Horario = 'Presente' THEN 1 ELSE 0 END) AS asistencias,
    //                         SUM(CASE WHEN a.1_Horario = 'Ausente' OR a.2_Horario = 'Ausente' THEN 1 ELSE 0 END) AS ausencias,
    //                         COUNT(*) AS total_clases,
    //                         (SELECT COUNT(*) FROM alumnos_justificados aj 
    //                          WHERE (aj.materias_idMaterias = m.idMaterias OR aj.materias_idMaterias1 = m.idMaterias)
    //                          AND aj.inscripcion_asignatura_alumno_legajo = '$legajo') AS justificaciones
    //                     FROM 
    //                         asistencia a
    //                     INNER JOIN 
    //                         materias m ON a.materias_idMaterias = m.idMaterias
    //                     WHERE 
    //                         a.inscripcion_asignatura_alumno_legajo = '$legajo'
    //                     GROUP BY 
    //                         m.Nombre";

    // $query_asistencias = mysqli_query($conexion, $sql_asistencias);

    // if (!$query_asistencias) {
    //     throw new Exception("Error al obtener las asistencias: " . mysqli_error($conexion));
    // }

    // while ($row_asistencias = mysqli_fetch_assoc($query_asistencias)) {
    //     $total_clases = $row_asistencias['total_clases'];
    //     $justificaciones = $row_asistencias['justificaciones'];
    //     $ajuste_ausencias = floor($justificaciones / 2); 
    //     $ausencias_ajustadas = $row_asistencias['ausencias'] + $ajuste_ausencias;
    //     $asistencias_ajustadas = $row_asistencias['asistencias'] - $ajuste_ausencias;
    //     $porcentaje_asistencia = ($asistencias_ajustadas / $total_clases) * 100;
    //     $porcentaje_ausencia = ($ausencias_ajustadas / $total_clases) * 100;

    //     $html_asistencias .= "<tr>
    //                             <td>{$row_asistencias['Nombre']}</td>
    //                             <td>" . number_format($porcentaje_asistencia, 0) . "%</td>
    //                             <td>" . number_format($porcentaje_ausencia, 0) . "%</td>
    //                             <td>{$row_asistencias['justificaciones']}</td>
    //                         </tr>";
    // }
    // $html_asistencias .= '</table>';
    // echo $html_asistencias;

    // // **Justificaciones**
    // $html_justificaciones = '<br><h2 class="section-title">Justificaciones</h2><br>';
    // $html_justificaciones .= '<table class="styled-table">
    //                             <tr>
    //                                 <th>Materia</th>
    //                                 <th>Materia2</th>
    //                                 <th>Motivo</th>
    //                                 <th>Fecha</th>
    //                             </tr>';

    // $sql_justificaciones = "SELECT 
    //                             c.nombre_carrera,
    //                             a2.nombre_alumno,
    //                             a2.apellido_alumno,
    //                             m.Nombre AS materia,
    //                             m.Nombre AS materia2,
    //                             a.Motivo,
    //                             a.fecha 
    //                         FROM 
    //                             alumnos_justificados a
    //                         INNER JOIN 
    //                             carreras c ON c.idCarrera = a.inscripcion_asignatura_carreras_idCarrera
    //                         INNER JOIN 
    //                             alumno a2 ON a.inscripcion_asignatura_alumno_legajo = a2.legajo
    //                         INNER JOIN 
    //                             materias m ON m.idMaterias = a.materias_idMaterias
    //                         WHERE 
    //                             a.inscripcion_asignatura_alumno_legajo = '$legajo'";

    // $query_justificaciones = mysqli_query($conexion, $sql_justificaciones);

    // if (!$query_justificaciones) {
    //     throw new Exception("Error al obtener las justificaciones: " . mysqli_error($conexion));
    // }

    // while ($row_justificacion = mysqli_fetch_assoc($query_justificaciones)) {
    //     $html_justificaciones .= "<tr>
    //                                 <td>{$row_justificacion['materia']}</td>
    //                                 <td>{$row_justificacion['materia2']}</td>
    //                                 <td>{$row_justificacion['Motivo']}</td>
    //                                 <td>{$row_justificacion['fecha']}</td>
    //                             </tr>";
    // }

    // $html_justificaciones .= '</table>';
    // echo $html_justificaciones;

    // // **Ratificaciones**
    // $html_ratificaciones = '<br><h2 class="section-title">Días que se retiró antes de tiempo</h2><br>';
    // $html_ratificaciones .= '<table class="styled-table">
    //                             <tr>
    //                                 <th>Materia</th>
    //                                 <th>Motivo</th>
    //                                 <th>Fecha</th>
    //                             </tr>';

    // $sql_ratificaciones = "SELECT 
    //                             a2.legajo, 
    //                             a2.apellido_alumno,
    //                             a2.nombre_alumno,
    //                             c.nombre_carrera,
    //                             m.Nombre AS materia,
    //                             p.nombre_profe,
    //                             a.motivo,
    //                             a.fecha 
    //                         FROM 
    //                             alumnos_rat a
    //                         INNER JOIN 
    //                             alumno a2 ON a.alumno_legajo = a2.legajo
    //                         INNER JOIN 
    //                             carreras c ON a.carreras_idCarrera = c.idCarrera
    //                         INNER JOIN 
    //                             materias m ON a.materias_idMaterias = m.idMaterias
    //                         INNER JOIN 
    //                             profesor p ON a.profesor_idProrfesor = p.idProrfesor
    //                         WHERE 
    //                             a.alumno_legajo = '$legajo'";

    // $query_ratificaciones = mysqli_query($conexion, $sql_ratificaciones);

    // if (!$query_ratificaciones) {
    //     throw new Exception("Error al obtener las ratificaciones: " . mysqli_error($conexion));
    // }

    // while ($row_ratificacion = mysqli_fetch_assoc($query_ratificaciones)) {
    //     $html_ratificaciones .= "<tr>
    //                                 <td>{$row_ratificacion['materia']}</td>
    //                                 <td>{$row_ratificacion['motivo']}</td>
    //                                 <td>{$row_ratificacion['fecha']}</td>
    //                             </tr>";
    // }

    // $html_ratificaciones .= '</table>';
    // echo $html_ratificaciones;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

 </div>


<style>
  /* Botón de acción */
.accion-button {
    background-color: white;
    color: black;
    padding: 10px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
    margin: 20px;
}
.accion-button:hover {
    background-color: darkred;
	text-decoration: none;
}

/* Títulos de sección */
.section-title {
    color: white;
    text-shadow: 2px 1px 2px black;
    font-family: 'Arial', sans-serif;
    margin: 20px 0;
}

/* Estilos para las tablas */
.styled-table {
    width: 90%;
    margin: 20px auto;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 16px;
    text-align: left;
    font-family: 'Arial', sans-serif;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.styled-table th, .styled-table td {
    padding: 8px 12px;
  
}
.styled-table th {
    background-color: red;
    color: white;
    text-align: center;
}
.styled-table tr {
    border-bottom: 1px solid #ddd;
}
.styled-table tr:nth-of-type(even) {
    background-color: #f9f9f9;
}
.styled-table tr:last-of-type {
    border-bottom: 2px solid red;
}
.styled-table tr:hover {
    background-color: #f1f1f1;
}
.styled-table th:first-child {
    border-top-left-radius: 8px;
}
.styled-table th:last-child {
    border-top-right-radius: 8px;
}
.styled-table tr:last-child td:first-child {
    border-bottom-left-radius: 8px;
}
.styled-table tr:last-child td:last-child {
    border-bottom-right-radius: 8px;
}

/* Estilos específicos para las tablas de datos del alumno */
#datos-alumno th {
    background-color: red;
    color: white;
}
#datos-alumno td {
    background-color: #fff;
    color: black;
}

/* Estilos específicos para las tablas de asistencias */
#asistencias th {
    background-color: red;
    color: white;
}
#asistencias td {
    background-color: #fff;
    color: black;
}

/* Estilos específicos para las tablas de justificaciones */
#justificaciones th {
    background-color: red;
    color: white;
}
#justificaciones td {
    background-color: #fff;
    color: black;
}

/* Estilos específicos para las tablas de ratificaciones */
#ratificaciones th {
    background-color: red;
    color: white;
}
#ratificaciones td {
    background-color: #fff;
    color: black;
}
</style>

   

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
    
// dataTables de Alumnos //
var myTable = document.querySelector("#tabla");
var dataTable = new DataTable(tabla);
</script>

<script>
function confirmarRestablecimiento() {
    return confirm('¿Estás seguro de que deseas restablecer la contraseña a 0123456789?');
}
</script>

</body>
</html>

