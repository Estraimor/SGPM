<?php
session_start(); // Asegúrate de iniciar la sesión si no está ya iniciada.
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
				
				<a href="./S_estudiante/index_estudiante.php" class="logo">
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
									<img src="./assets/img/1361728.png" alt="..." class="avatar-img rounded-circle" >
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
							<li>
								<div class="user-box">
									<div class="avatar-lg"><img src="./assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;"></div>
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
									<a class="dropdown-item" href="./S_estudiante/perfil_estudiante.php">Perfil</a>
									<a class="dropdown-item" href="../../login/cerrar_sesion.php">Cerrar Sesión</a>
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
							<a href="./S_estudiante/index_estudiante.php">
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
										<a href="./S_estudiante/inscripcion_mesas.php">
											<span class="sub-item">Inscripción a mesas de Examen</span>
										</a>
									</li>
								</ul>
							</div>
						</li>
						<li class="nav-item active">
                			<a data-toggle="collapse" href="#takeAttendance">
                    			<i class="fas fa-pen-square"></i>
                    			<p>Situación Académica</p>
                    			<span class="caret"></span>
                			</a>
                			<div class="collapse show" id="takeAttendance">
								
                    <ul class="nav nav-collapse">
                        <li >
                            <a href="./ver_asistencia_alumnos.php">
                                <span class="sub-item">Mis Asistencias</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="./ver_notas_alumnos.php">
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
    <?php

    $legajo = $_SESSION["id"]; // Toma el legajo del alumno desde la sesión

    try {
        // Consulta para obtener todas las notas con valores y condición de la mesa final y el nombre de la materia
        $sql_notas = "SELECT 
                        n.nota_final,
                        n.condicion,
                        COALESCE(nf.nota, 'N/A') AS nota_examen_final,
                        COALESCE(m.Nombre, 'N/A') AS nombre_materia
                      FROM 
                        notas n
                      LEFT JOIN 
                        nota_examen_final nf ON n.alumno_legajo = nf.alumno_legajo AND n.materias_idMaterias = nf.materias_idMaterias
                      LEFT JOIN 
                        materias m ON n.materias_idMaterias = m.idMaterias
                      WHERE 
                        n.alumno_legajo = '$legajo'
                        AND n.nota_final IS NOT NULL
                        AND n.condicion IS NOT NULL
                      ORDER BY 
                        n.fecha DESC";

        $query_notas = mysqli_query($conexion, $sql_notas);

        if (!$query_notas) {
            throw new Exception("Error al obtener las notas: " . mysqli_error($conexion));
        }

        $html_notas = '<br><h2 class="section-title">Última Nota Registrada</h2><br>';
        $html_notas .= '<table class="styled-table">
                            <tr>
                                <th>Materia</th>
                                <th>Nota Final</th>
                                <th>Condición</th>
                                <th>Nota Examen Final</th>
                            </tr>';

        // Procesar todos los resultados
        $hay_registros = false;
        while ($row_notas = mysqli_fetch_assoc($query_notas)) {
            $hay_registros = true;
            $nombre_materia = $row_notas['nombre_materia'];
            $nota_final = isset($row_notas['nota_final']) ? number_format($row_notas['nota_final'], 2) : 'N/A';
            $condicion = $row_notas['condicion'] ?? 'N/A';
            $nota_examen_final = is_numeric($row_notas['nota_examen_final']) ? number_format($row_notas['nota_examen_final'], 2) : $row_notas['nota_examen_final'];

            $html_notas .= "<tr>
                                <td>$nombre_materia</td>
                                <td>$nota_final</td>
                                <td>$condicion</td>
                                <td>$nota_examen_final</td>
                            </tr>";
        }

        if (!$hay_registros) {
            $html_notas .= '<tr><td colspan="4">No hay registros de notas disponibles.</td></tr>';
        }

        $html_notas .= '</table>';
        echo $html_notas;

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
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

