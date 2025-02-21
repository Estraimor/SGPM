<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../login/login.php');
    exit;
}

if (isset($_SESSION["contraseña"]) && $_SESSION["contraseña"] === "0123456789") {
    header('Location: cambio_contrasena_profe.php');
    exit;
}

$rolUsuario = $_SESSION["roles"];
$rolesPermitidos = ['1', '2', '3', '4'];

if (!in_array($rolUsuario, $rolesPermitidos)) {
    header('Location: ../login/no_autorizado.php');
    exit;
}

// Conexión a la base de datos
require_once '../../conexion.php';

// Consultas a la base de datos
$carreras = mysqli_query($conn, "SELECT * FROM carreras");
$materias = mysqli_query($conn, "SELECT * FROM materias");
$comisiones = mysqli_query($conn, "SELECT * FROM comisiones");
$mesas_finales = mysqli_query($conn, "SELECT * FROM mesas_finales");
$materias_pedagogicas = mysqli_query($conn, "SELECT * FROM mesas_pedagogicas");



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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
	<!-- CSS Files -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/azzara.min.css">
	

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="assets/css/demo.css">
	<link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
<script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>



<style>
     /* Barra de control (buscador y selector) *//* Barra de control (buscador, selector y filtros) */
    .controls {
      margin-bottom: 10px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }
    .controls input[type="text"],
    .controls select,
    .controls input[type="date"],
    .controls input[type="number"] {
      padding: 5px;
    }

    /* Paginación */
    .pagination {
      display: flex;
      list-style: none;
      gap: 5px;
      margin-top: 10px;
    }
    .pagination li {
      cursor: pointer;
      padding: 5px 10px;
      border: 1px solid #888;
      background: #f9f9f9;
    }
    .pagination li.active {
      font-weight: bold;
      background: #cce5ff;
      border-color: #66afe9;
    }
    .pagination li[disabled] {
      opacity: 0.5;
      cursor: default;
    }

    /* Modal */
    #modal-background {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      z-index: 10;
    }
    #modal-editar {
      position: fixed;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 20px;
      border-radius: 10px;
      display: none;
      z-index: 20;
      width: 300px;
    }
</style>
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
        <div id="mesa-container">
            <div class="mesa-item">
                <label for="carrera">Selecciona la Carrera:</label><br>
                <select class="carrera" name="carrera[]" required>
                    <option value="">Selecciona una carrera</option>
                    <?php
                    include 'conexion.php';
                    $idPreceptor = $_SESSION['id'];  
                    $rolUsuario = $_SESSION["roles"]; 

                    $sql_mater = "SELECT DISTINCT c.idCarrera, c.nombre_carrera 
                                  FROM carreras c
                                  INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                                  INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera";
                    
                    if ($rolUsuario == 5) {
                        $sql_mater .= " WHERE c.profesor_idProrfesor = '{$idPreceptor}'";
                    } elseif ($rolUsuario != 1) {
                        $sql_mater .= " WHERE m.profesor_idProrfesor = '{$idPreceptor}'";
                    }

                    $result = mysqli_query($conexion, $sql_mater);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
                    }
                    ?>
                </select><br><br>

                <label for="curso">Selecciona el Curso:</label><br>
                <select class="curso" name="curso[]" required>
                    <option value="">Selecciona una carrera primero</option>
                </select><br><br>

                <label for="comision">Selecciona la Comisión:</label><br>
                <select class="comision" name="comision[]" required>
                    <option value="">Selecciona un curso primero</option>
                </select><br><br>

                <label for="materia">Selecciona la Materia:</label><br>
                <select class="materia" name="materias[]" required>
                    <option value="">Selecciona una comisión primero</option>
                </select><br><br>
            </div>
        </div>

        <button type="button" id="agregar-mesa" class="btn">Agregar Unidad Curricular</button><br><br>


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


    <!-- Mesa Pedagógica Asociada 1 -->
<div class="mesa-pedagogica-container">
    <h3>Agregar Mesa Pedagógica Asociada 1</h3>
    <div class="mesa-pedagogica-item">
        <!-- Seleccionar Carrera -->
        <label for="carrera-1">Selecciona la Carrera:</label><br>
        <select class="carrera" name="carrera_1">
            <option value="">Selecciona una carrera</option>
            <?php
            include("conexion.php");
            $query = "SELECT * FROM carreras";
            $result = mysqli_query($conexion, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
            }
            ?>
        </select><br><br>

        <!-- Seleccionar Curso -->
        <label for="curso-1">Selecciona el Curso:</label><br>
        <select class="curso" name="curso_1">
            <option value="">Selecciona una carrera primero</option>
        </select><br><br>

        <!-- Seleccionar Comisión -->
        <label for="comision-1">Selecciona la Comisión:</label><br>
        <select class="comision" name="comision_1">
            <option value="">Selecciona un curso primero</option>
        </select><br><br>

        <!-- Seleccionar Materia Principal -->
        <label for="materia-principal-1">Selecciona la Materia Principal:</label><br>
        <select class="materia-principal" name="materia_principal_1">
            <option value="">Selecciona Carrera, Curso y Comisión primero</option>
        </select><br><br>

        
    </div>
</div>

<!-- Mesa Pedagógica Asociada 2 -->
<div class="mesa-pedagogica-container">
    <h3>Agregar Mesa Pedagógica Asociada 2</h3>
    <div class="mesa-pedagogica-item">
        <label for="carrera-2">Selecciona la Carrera:</label><br>
        <select class="carrera" name="carrera_2">
            <option value="">Selecciona una carrera</option>
            <?php
            $query = "SELECT * FROM carreras";
            $result = mysqli_query($conexion, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
            }
            ?>
        </select><br><br>

        <label for="curso-2">Selecciona el Curso:</label><br>
        <select class="curso" name="curso_2">
            <option value="">Selecciona una carrera primero</option>
        </select><br><br>

        <label for="comision-2">Selecciona la Comisión:</label><br>
        <select class="comision" name="comision_2">
            <option value="">Selecciona un curso primero</option>
        </select><br><br>

        

        <label for="materia-pedagogica-2">Selecciona la Materia Pedagógica Asociada:</label><br>
        <select class="materia-pedagogica" name="materia_pedagogica_2">
            <option value="">Selecciona Carrera, Curso y Comisión primero</option>
        </select><br><br>
    </div>
</div>


        <input type="submit" value="Agregar Mesa">
    </form>







    <h2>Mesas Finales</h2>

<!-- Controles de búsqueda y tamaño de página -->
<div class="controls">
  <!-- Búsqueda general -->
  <input type="text" id="searchInput" placeholder="Buscar texto en la tabla...">

  <!-- Selector de cuántas filas se muestran por página -->
  <select id="pageSizeSelect">
    <option value="5">5 filas</option>
    <option value="10" selected>10 filas</option>
    <option value="25">25 filas</option>
    <option value="50">50 filas</option>
  </select>

  <!-- Filtros adicionales -->
  <label for="filterFecha">Fecha:</label>
  <input type="date" id="filterFecha">
  <!-- Botón para limpiar la fecha -->
  <button type="button" id="clearFecha" title="Limpiar fecha">🗑️</button>

  <label for="filterLlamado">Llamado:</label>
  <input type="number" id="filterLlamado" placeholder="1 o 2...">

  <label for="filterTanda">Tanda:</label>
  <input type="number" id="filterTanda" placeholder="Ej: 1, 2...">

  <label for="filterCupo">Cupo:</label>
  <input type="number" id="filterCupo" placeholder="Ej: 30">
</div>



<!-- TABLA con los datos de Mesas Finales -->
<table border="1" id="tabla_mesas">
  <thead>
    <tr>
      <th>ID tanta</th>
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
    // EJEMPLO DE CONSULTA (ajusta según tu configuración/archivo PHP).
    // Asegúrate de haber incluido la conexión a la BD antes (conexion.php).
    // include("conexion.php");

    $query = "SELECT fm.idfechas_mesas_finales, 
                     m.Nombre AS nombre_materia,
                     t.idtandas, 
                     t.fecha, 
                     t.llamado, 
                     t.tanda, 
                     t.cupo, 
                     c.nombre_carrera
              FROM fechas_mesas_finales fm
              JOIN materias m ON fm.materias_idMaterias = m.idMaterias
              JOIN tandas t ON fm.tandas_idtandas = t.idtandas
              JOIN carreras c ON m.carreras_idCarrera  = c.idCarrera";

    $result = mysqli_query($conexion, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Construir nombre_materia_completo
            $carrera_prefix = substr($row['nombre_carrera'], 0, 4);
            $carrera_suffix = substr($row['nombre_carrera'], -5);
            $nombre_materia_completo = $carrera_prefix . " " . $row['nombre_materia'] . " " . $carrera_suffix;

            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['idtandas']) . "</td>"; // Columna 0
            echo "<td>" . htmlspecialchars($nombre_materia_completo) . "</td>"; // Columna 1
            echo "<td>" . htmlspecialchars($row['fecha']) . "</td>"; // Columna 2
            // Convertir llamado 1 o 2 a texto
            $llamadoText = ($row['llamado'] == 1) ? 'Primer Llamado' : 'Segundo Llamado';
            echo "<td>" . $llamadoText . "</td>"; // Columna 3
            echo "<td>" . htmlspecialchars($row['tanda']) . "</td>"; // Columna 4
            echo "<td>" . htmlspecialchars($row['cupo']) . "</td>";  // Columna 5
            echo "<td>
                    <button class='editar' data-id='" . htmlspecialchars($row['idfechas_mesas_finales']) . "'>Modificar</button>
                    <button class='eliminar' data-id='" . htmlspecialchars($row['idfechas_mesas_finales']) . "'>Eliminar</button>
                  </td>"; // Columna 6
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No se encontraron mesas finales.</td></tr>";
    }
    ?>
  </tbody>
</table>

<!-- Contenedor para la paginación -->
<ul id="pagination" class="pagination"></ul>

<!-- Fondo oscuro del modal -->
<div id="modal-background"></div>

<!-- Modal para editar mesa final -->
<div id="modal-editar">
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
    // Cargar los cursos cuando se seleccione una carrera
    $(document).on('change', '.carrera', function() {
        var idCarrera = $(this).val();
        var cursoSelect = $(this).closest('.mesa-item').find('.curso');
        var comisionSelect = $(this).closest('.mesa-item').find('.comision');
        var materiaSelect = $(this).closest('.mesa-item').find('.materia');

        if (idCarrera) {
            $.ajax({
                type: 'POST',
                url: './mesa_finales/obtener_cursos.php',
                data: { idCarrera: idCarrera },
                success: function(html) {
                    cursoSelect.html(html);
                    comisionSelect.html('<option value="">Selecciona un curso primero</option>');
                    materiaSelect.html('<option value="">Selecciona una comisión primero</option>');
                },
                error: function() {
                    alert('Error al cargar los cursos.');
                }
            });
        }
    });

    // Cargar las comisiones cuando se seleccione un curso
    $(document).on('change', '.curso', function() {
        var idCurso = $(this).val();
        var comisionSelect = $(this).closest('.mesa-item').find('.comision');
        var materiaSelect = $(this).closest('.mesa-item').find('.materia');

        if (idCurso) {
            $.ajax({
                type: 'POST',
                url: './mesa_finales/obtener_comisiones.php',
                data: { idCurso: idCurso },
                success: function(html) {
                    comisionSelect.html(html);
                    materiaSelect.html('<option value="">Selecciona una comisión primero</option>');
                },
                error: function() {
                    alert('Error al cargar las comisiones.');
                }
            });
        }
    });

    // Cargar las materias cuando se seleccione una comisión
    $(document).on('change', '.comision', function() {
        var idCarrera = $(this).closest('.mesa-item').find('.carrera').val();
        var idCurso = $(this).closest('.mesa-item').find('.curso').val();
        var idComision = $(this).val();
        var materiaSelect = $(this).closest('.mesa-item').find('.materia');

        if (idCarrera && idCurso && idComision) {
            $.ajax({
                type: 'POST',
                url: './mesa_finales/obtener_materias_mesas_finales.php',
                data: { idCarrera: idCarrera, idCurso: idCurso, idComision: idComision },
                success: function(html) {
                    materiaSelect.html(html);
                },
                error: function() {
                    alert('Error al cargar las materias.');
                }
            });
        }
    });


    // Agregar nueva combinación de carrera, curso, comisión y materia
    $('#agregar-mesa').on('click', function() {
        var nuevaMesa = `
            <div class="mesa-item">
                <label for="carrera">Selecciona la Carrera:</label><br>
                <select class="carrera" name="carrera[]">
                    <option value="">Selecciona una carrera</option>
                    <?php
                    include 'conexion.php';

                    $idPreceptor = $_SESSION['id'];
                    $rolUsuario = $_SESSION["roles"];

                    if ($rolUsuario == 1) {
                        $sql_mater = "
                            SELECT DISTINCT c.idCarrera, c.nombre_carrera
                            FROM carreras c
                            INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                            INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                        ";
                    } elseif ($rolUsuario == 5) {
                        $sql_mater = "
                            SELECT DISTINCT c.idCarrera, c.nombre_carrera
                            FROM carreras c
                            INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                            INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                            WHERE c.profesor_idProrfesor = '{$idPreceptor}'
                        ";
                    } else {
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

                <label for="curso">Selecciona el Curso:</label><br>
                <select class="curso" name="curso[]">
                    <option value="">Selecciona una carrera primero</option>
                </select><br><br>

                <label for="comision">Selecciona la Comisión:</label><br>
                <select class="comision" name="comision[]">
                    <option value="">Selecciona un curso primero</option>
                </select><br><br>

                <label for="materia">Selecciona la Materia:</label><br>
                <select class="materia" name="materias[]">
                    <option value="">Selecciona una comisión primero</option>
                </select><br><br>

                <button type="button" class="eliminar-mesa">Eliminar Unidad Curricular</button><br><br>
            </div>
        `;
        $('#mesa-container').append(nuevaMesa);
    });

    // Eliminar una mesa agregada
    $(document).on('click', '.eliminar-mesa', function() {
        $(this).closest('.mesa-item').remove();
    });
});


</script>

<!-- JS materia pedagogica   -->
<script>
$(document).ready(function() {

    // 🔹 Cuando cambia la Carrera, cargar los Cursos correspondientes
    $('.carrera').on('change', function() {
        var idCarrera = $(this).val();
        var $cursoSelect = $(this).closest('.mesa-pedagogica-item').find('.curso');
        var $comisionSelect = $(this).closest('.mesa-pedagogica-item').find('.comision');
        var $materiaSelects = $(this).closest('.mesa-pedagogica-item').find('.materia-principal, .materia-pedagogica');

        // Restablecer selects dependientes
        $cursoSelect.html('<option value="">Selecciona una carrera primero</option>');
        $comisionSelect.html('<option value="">Selecciona un curso primero</option>');
        $materiaSelects.html('<option value="">Selecciona Carrera, Curso y Comisión primero</option>');

        if (!idCarrera) return;

        $.ajax({
            url: './mesa_finales/obtener_cursos.php',
            type: 'POST',
            data: { idCarrera: idCarrera },
            success: function(options) {
                $cursoSelect.html('<option value="">Selecciona un curso</option>' + options);
            },
            error: function() {
                alert("Error al cargar los cursos.");
            }
        });
    });

    // 🔹 Cuando cambia el Curso, cargar las Comisiones correspondientes
    $('.curso').on('change', function() {
        var idCurso = $(this).val();
        var $comisionSelect = $(this).closest('.mesa-pedagogica-item').find('.comision');
        var $materiaSelects = $(this).closest('.mesa-pedagogica-item').find('.materia-principal, .materia-pedagogica');

        // Restablecer selects dependientes
        $comisionSelect.html('<option value="">Selecciona un curso primero</option>');
        $materiaSelects.html('<option value="">Selecciona Carrera, Curso y Comisión primero</option>');

        if (!idCurso) return;

        $.ajax({
            url: './mesa_finales/obtener_comisiones.php',
            type: 'POST',
            data: { idCurso: idCurso },
            success: function(options) {
                $comisionSelect.html('<option value="">Selecciona una comisión</option>' + options);
            },
            error: function() {
                alert("Error al cargar las comisiones.");
            }
        });
    });

    // 🔹 Cuando cambia la Comisión, cargar las Materias Principal y Pedagógica desde obtener_materia_pedagogica.php
    $('.comision').on('change', function() {
        var idCarrera = $(this).closest('.mesa-pedagogica-item').find('.carrera').val();
        var idCurso = $(this).closest('.mesa-pedagogica-item').find('.curso').val();
        var idComision = $(this).val();
        var $materiaPrincipalSelect = $(this).closest('.mesa-pedagogica-item').find('.materia-principal');
        var $materiaPedagogicaSelect = $(this).closest('.mesa-pedagogica-item').find('.materia-pedagogica');

        if (!idCarrera || !idCurso || !idComision) {
            $materiaPrincipalSelect.html('<option value="">Selecciona Carrera, Curso y Comisión primero</option>');
            $materiaPedagogicaSelect.html('<option value="">Selecciona Carrera, Curso y Comisión primero</option>');
            return;
        }

        // Obtener Materias (Ambas: Principal y Pedagógica) desde obtener_materia_pedagogica.php
        $.ajax({
            url: './mesa_finales/obtener_materia_pedagogica.php',
            type: 'POST',
            data: { idCarrera: idCarrera, idCurso: idCurso, idComision: idComision },
            success: function(options) {
                $materiaPrincipalSelect.html('<option value="">Selecciona una materia</option>' + options);
                $materiaPedagogicaSelect.html('<option value="">Selecciona una materia pedagógica</option>' + options);
            },
            error: function() {
                alert("Error al cargar las materias.");
            }
        });
    });

});
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {

// ==========================================
// 1) CONFIGURACIÓN DE BÚSQUEDA, PAGINACIÓN Y FILTROS
// ==========================================

const table = document.getElementById('tabla_mesas');
const tBody = table.querySelector('tbody');
const allRows = Array.from(tBody.querySelectorAll('tr')); // Guardar todas las filas originales

// Controles de búsqueda y filtros
const searchInput    = document.getElementById('searchInput');
const pageSizeSelect = document.getElementById('pageSizeSelect');
const filterFecha    = document.getElementById('filterFecha');
const filterLlamado  = document.getElementById('filterLlamado');
const filterTanda    = document.getElementById('filterTanda');
const filterCupo     = document.getElementById('filterCupo');
const paginationUL   = document.getElementById('pagination');

// Variables de estado para la paginación y filtrado
let filteredRows = [...allRows];
let currentPage = 1;
let pageSize = parseInt(pageSizeSelect.value);

/**
 * FUNCION: Aplica todos los filtros a la tabla
 * Incluye búsqueda global, fecha, llamado, tanda y cupo.
 * @returns {Array} Filas que cumplen con todos los filtros aplicados
 */
function applyAllFilters() {
  const textSearch   = searchInput.value.toLowerCase().trim();
  const dateFilter   = filterFecha.value.trim();   
  const llamadoFilter= filterLlamado.value.trim(); 
  const tandaFilter  = filterTanda.value.trim();   
  const cupoFilter   = filterCupo.value.trim();    

  return allRows.filter(row => {
    // Extraer el contenido de cada columna relevante
    const c2_fecha    = row.cells[2].innerText; 
    const c3_llamado  = row.cells[3].innerText.toLowerCase();
    const c4_tanda    = row.cells[4].innerText.toLowerCase();
    const c5_cupo     = row.cells[5].innerText.toLowerCase();
    const rowText     = row.innerText.toLowerCase();

    // Validar cada filtro individualmente
    const passSearch  = rowText.includes(textSearch);
    const passFecha   = !dateFilter || c2_fecha.includes(dateFilter);
    const passLlamado = !llamadoFilter || c3_llamado.includes(llamadoFilter.toLowerCase());
    const passTanda   = !tandaFilter || c4_tanda.includes(tandaFilter);
    const passCupo    = !cupoFilter || c5_cupo.includes(cupoFilter);

    return passSearch && passFecha && passLlamado && passTanda && passCupo;
  });
}

/**
 * FUNCION: Renderiza la tabla aplicando los filtros y la paginación
 */
function renderTable() {
  filteredRows = applyAllFilters();
  tBody.innerHTML = ''; // Limpiar la tabla antes de renderizar

  const totalPages = Math.ceil(filteredRows.length / pageSize) || 1;

  currentPage = Math.max(1, Math.min(currentPage, totalPages));

  const startIndex = (currentPage - 1) * pageSize;
  const endIndex   = startIndex + pageSize;
  const rowsToShow = filteredRows.slice(startIndex, endIndex);

  rowsToShow.forEach(row => tBody.appendChild(row));
  renderPagination(totalPages);
}

/**
 * FUNCION: Renderiza los controles de paginación
 */
function renderPagination(totalPages) {
  paginationUL.innerHTML = ''; // Limpiar la paginación

  if (totalPages < 1) return;

  // Flecha "anterior"
  const prevLi = document.createElement('li');
  prevLi.textContent = '<';
  prevLi.addEventListener('click', () => { 
    if (currentPage > 1) { 
      currentPage--; 
      renderTable(); 
    } 
  });
  paginationUL.appendChild(prevLi);

  // Rango visible de páginas (5 botones)
  const visiblePages = 5;
  let startPage = Math.max(1, currentPage - Math.floor(visiblePages / 2));
  let endPage   = Math.min(totalPages, startPage + visiblePages - 1);

  for (let p = startPage; p <= endPage; p++) {
    const li = document.createElement('li');
    li.textContent = p;
    li.classList.toggle('active', p === currentPage);
    li.addEventListener('click', () => { 
      currentPage = p; 
      renderTable(); 
    });
    paginationUL.appendChild(li);
  }

  // Flecha "siguiente"
  const nextLi = document.createElement('li');
  nextLi.textContent = '>';
  nextLi.addEventListener('click', () => { 
    if (currentPage < totalPages) { 
      currentPage++; 
      renderTable(); 
    } 
  });
  paginationUL.appendChild(nextLi);
}

// Eventos que recalculan la tabla
searchInput.addEventListener('input', () => { currentPage = 1; renderTable(); });
pageSizeSelect.addEventListener('change', () => { 
  pageSize = parseInt(pageSizeSelect.value); 
  currentPage = 1; 
  renderTable(); 
});

// Filtros adicionales
[filterFecha, filterLlamado, filterTanda, filterCupo].forEach(filter => {
  filter.addEventListener('input', () => { 
    currentPage = 1; 
    renderTable(); 
  });
});

renderTable();

// ==========================================
// 2) MANEJO DEL MODAL (Editar / Eliminar)
// ==========================================

table.addEventListener('click', function(e) {
  // Modal para Modificar
  if (e.target.classList.contains('editar')) {
    const idMesa = e.target.getAttribute('data-id');
    
    fetch('./obtener_mesa_final.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({ idMesa: idMesa })
    })
    .then(response => response.json())
    .then(data => {
      document.getElementById('modal_id_mesa').value = data.idfechas_mesas_finales;
      document.getElementById('modal_fecha').value = data.fecha;
      document.getElementById('modal_llamado').value = data.llamado;
      document.getElementById('modal_tanda').value = data.tanda;
      document.getElementById('modal_cupo').value = data.cupo;

      document.getElementById('modal-background').style.display = 'block';
      document.getElementById('modal-editar').style.display = 'block';
    });
  }

  // Eliminar
  if (e.target.classList.contains('eliminar')) {
    const idMesa = e.target.getAttribute('data-id');
    
    if (confirm('¿Eliminar esta mesa?')) {
      fetch('./mesa_finales/eliminar_mesa_final.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ idMesa: idMesa })
      })
      .then(() => {
        alert('Eliminado correctamente');
        renderTable();
      });
    }
  }
});

// Cerrar Modal
document.getElementById('cerrar-modal').addEventListener('click', () => {
  document.getElementById('modal-background').style.display = 'none';
  document.getElementById('modal-editar').style.display = 'none';
});

// Guardar cambios en el modal
document.getElementById('form-editar').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch('./mesa_finales/modificar_mesa_final.php', {
    method: 'POST',
    body: formData
  })
  .then(() => {
    alert('Mesa actualizada correctamente');
    document.getElementById('modal-background').style.display = 'none';
    document.getElementById('modal-editar').style.display = 'none';
    renderTable();
  });
});
// Botón para limpiar la fecha y recargar la tabla sin filtros activos
document.getElementById('clearFecha').addEventListener('click', function() {
    document.getElementById('filterFecha').value = ''; // Vaciar el campo de fecha
    searchInput.value = ''; // Limpiar la búsqueda general
    filterLlamado.value = ''; // Limpiar el filtro de llamado
    filterTanda.value = ''; // Limpiar el filtro de tanda
    filterCupo.value = ''; // Limpiar el filtro de cupo

    currentPage = 1; // Volver a la primera página
    renderTable(); // Recargar la tabla con todos los registros (estado default)
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

