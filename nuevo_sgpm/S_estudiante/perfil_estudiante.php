<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../../../login/login.php');
    exit;
}

$idPreceptor = $_SESSION['id'];
include '../../conexion/conexion.php';
// Set inactivity limit in seconds
$inactivity_limit = 1200;

// Check if the user has been inactive for too long
if (isset($_SESSION['time']) && (time() - $_SESSION['time'] > $inactivity_limit)) {
    session_unset();
    session_destroy();
    header("Location: ../../../login/login.php");
    exit;
} else {
    $_SESSION['time'] = time();
}

if ($_SESSION['contraseña'] === '0123456789') {header('Location: cambio_contrasena.php');}?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>SGPM</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="../assets/img/Logo ISPM 2 transparante.png" type="image/x-icon"/>

	<!-- Fonts and icons -->
	<script src="../assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Open+Sans:300,400,600,700"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"], urls: ['../assets/css/fonts.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="../assets/css/azzara.min.css">
	<link rel="stylesheet" href="../assets/css/estilos.css">

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="../assets/css/demo.css">
</head>
<body>
	<div class="wrapper">
		
		<div class="main-header" data-background-color="red">
			<div class="logo-header">
				
				<a href="./index_estudiante.php" class="logo">
					<img src="../assets/img/Logo ISPM 2 transparante.png" width="45px" alt="navbar brand" class="navbar-brand">
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
									<img src="../assets/img/1361728.png" alt="..." class="avatar-img rounded-circle" >
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
							<li>
								<div class="user-box">
									<div class="avatar-lg"><img src="../assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;"></div>
									<div class="u-text">
										<?php 
    // Consulta para obtener el nombre, apellido y correo del alumno
    $sql_alumno = "SELECT 
                    a.nombre_alumno, 
                    a.apellido_alumno, 
                    a.usu_alumno AS email 
                   FROM 
                    alumno a 
                   WHERE 
                    a.legajo = '{$_SESSION["id"]}'";
    $query_alumno = mysqli_query($conexion, $sql_alumno);

    // Comprobar si la consulta devolvió algún resultado
    if (mysqli_num_rows($query_alumno) > 0) {
        // Recorrer los resultados y hacer echo del nombre, apellido y correo del alumno
        while ($row = mysqli_fetch_assoc($query_alumno)) { ?>
            <h4><?php echo $row['nombre_alumno'] . " " . $row['apellido_alumno']; ?></h4>
        <?php 
        } 
    } else {
        echo "<h4>Estudiante no encontrado</h4>";
        echo "<p class='text-muted'>Correo no disponible</p>";
    }
?>
			
									</div>
								</div>
							</li>
								<li>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="perfil_estudiante.php">Perfil</a>
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
						<li class="nav-item active">
							<a href="./index_estudiante.php">
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
								<p>Mesas</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="base">
								<ul class="nav nav-collapse">
									<li>
										<a href="inscripcion_mesas.php">
											<span class="sub-item">Inscripción a mesas de Examen</span>
										</a>
									</li>
								</ul>
							</div>
						</li>
						<li class="nav-item">
                			<a data-toggle="collapse" href="#takeAttendance">
                    			<i class="fas fa-pen-square"></i>
                    			<p>Situación Académica</p>
                    			<span class="caret"></span>
                			</a>
                			<div class="collapse" id="takeAttendance">
								
                    <ul class="nav nav-collapse">
                        <li>
                            <a href="../ver_asistencia_alumnos.php">
                                <span class="sub-item">Mis Asistencias</span>
                            </a>
                        </li>
                        <li>
                            <a href="../ver_notas_alumnos.php">
                                <span class="sub-item">Mis Notas</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
						
		</div>
		<!-- End Sidebar -->
	</div>
	
</div>
        <div class="contenido">
<div class="perfil-container">
    <h2>Perfil del Estudiante</h2>

    <?php 
    $sql_alumno = "SELECT * FROM alumno WHERE legajo = '{$_SESSION["id"]}'";
    $query_alumno = mysqli_query($conexion, $sql_alumno);

    if (mysqli_num_rows($query_alumno) > 0) {
        $row = mysqli_fetch_assoc($query_alumno);
    ?>

    <form action="./perfil_estudiante/actualizar_perfil_estu.php" method="POST">
        <label for="nombre_alumno">Nombre:</label>
        <div class="input-group">
            <input type="text" name="nombre_alumno" value="<?php echo $row['nombre_alumno']; ?>" required>
        </div>

        <label for="apellido_alumno">Apellido:</label>
        <div class="input-group">
            <input type="text" name="apellido_alumno" value="<?php echo $row['apellido_alumno']; ?>" required>
        </div>

        <label for="dni_alumno">DNI:</label>
        <div class="input-group">
            <input type="number" name="dni_alumno" value="<?php echo $row['dni_alumno']; ?>" required>
        </div>

        <label for="celular">Celular:</label>
        <div class="input-group">
            <input type="text" name="celular" value="<?php echo $row['celular']; ?>" required>
        </div>

        <label for="email">Email:</label>
        <div class="input-group">
            <input type="email" name="email" value="<?php echo $row['correo']; ?>" required>
        </div>

        <label for="usuario">Usuario:</label>
        <div class="input-group">
            <input type="text" name="usuario" value="<?php echo $row['usu_alumno']; ?>" required>
        </div>

        <label for="password">Contraseña:</label>
        <div class="input-group">
            <input type="text" name="password" value="<?php echo $row['pass_alumno']; ?>" required>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Actualizar Datos</button>
        </div>
    </form>

    <?php
    } else {
        echo "No se encontraron datos del alumno.";
    }
    ?>
</div>
</div>

<!-- Estilos CSS (se mantienen similares) -->
<style>
    .perfil-container {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #ffffff;
    }

    .perfil-container h2 {
        color: #333;
        font-size: 24px;
        text-align: left;
        font-weight: 600;
        margin-bottom: 10px;
    }

    form label {
        font-size: 14px;
        color: #333;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
    }

    .input-group input {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        outline: none;
        transition: all 0.3s ease;
    }

    .btn-group {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .btn-primary {
        background-color: #f3545d !important;
        color: white !important;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #ff545d !important;
    }
</style>
    
	


<!--   Core JS Files   -->
<script src="../assets/js/core/jquery.3.2.1.min.js"></script>

<script src="../assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="../assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>


<!-- jQuery Scrollbar -->
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Azzara JS -->
<script src="../assets/js/ready.min.js"></script>



</body>
</html>

