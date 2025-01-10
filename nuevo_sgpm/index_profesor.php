<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../login/login.php');
    exit;
}

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
									<img src="assets/img/1361728.png" alt="..." class="avatar-img rounded-circle">
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
								<li>
									<div class="user-box">
										<div class="avatar-lg"><img src="assets/img/1361728.png" alt="image profile" class="avatar-img rounded"></div>
										<div class="u-text">
											<h4>$usuario</h4>
											<p class="text-muted">#Correo Electronico</p><a href="profile.html" class="btn btn-rounded btn-danger btn-sm">View Profile</a>
										</div>
									</div>
								</li>
								<li>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="#">Mi Perfil</a>
									<a class="dropdown-item" href="#">Cambiar Contraseña</a>
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
							<a href="<?php
                                                    // Decidir el destino del enlace dependiendo del rol del usuario
                                                    if ($rolUsuario == '1') {
                                                        echo './Estudiantes/Tecnicatura/ABM_estudiante/nuevo_estudiante.php';
                                                    } else if ($rolUsuario == '2') {
                                                        echo './Estudiantes/Tecnicatura/ABM_estudiante/nuevo_estudiante_preceptor.php';
                                                    } else {
                                                        echo './Estudiantes/Tecnicatura/ABM_estudiante/nuevo_estudiante.php';
                                                    }
                                                ?>">
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
										<a href="#">
											<span class="sub-item">Libro de Tema</span>
										</a>
									</li>
									<li>
										<a href="#">
											<span class="sub-item">Parciales</span>
										</a>
									</li>
									<li>
										<a href="#">
											<span class="sub-item">Trabajos Practicos</span>
										</a>
									</li>
									<li>
										<a href="#">
											<span class="sub-item">Lista Estudiantes </span>
										</a>
									</li>
									<li>
										<a href="#">
											<span class="sub-item">Mesas</span>
										</a>
									</li>
								</ul>
							</div>
						</li>
						
						
		</div>
		<!-- End Sidebar -->

			
		
		
		<!-- End Custom template -->
	</div>
	
</div>
<div class="contenido">
	<br>
	<br>
<div class="welcome-box">
   <?php 
   $sql_profe = "SELECT p.idProrfesor, p.nombre_profe, p.apellido_profe FROM profesor p
WHERE p.idProrfesor = '{$_SESSION["id"]}'";

$query_nombre = mysqli_query($conexion, $sql_profe);

// Comprobar si la consulta devolvió algún resultado
if (mysqli_num_rows($query_nombre) > 0) {
    // Recorrer los resultados y hacer echo del nombre y apellido del profesor
    while ($row = mysqli_fetch_assoc($query_nombre)) { ?>
    <h2>Bienvenido/a al Sistema de Gestión del Politecnico Misiones N°1</h2>
    <p>¡Estamos encantados de verte de nuevo!</p>
	<p><?php echo "" . $row['nombre_profe'] . " " . $row['apellido_profe']; } ?></p>
<?php
} else {
    echo "No se encontraron datos del profesor.";
}

?>
    
</div>
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

