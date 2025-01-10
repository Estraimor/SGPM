<?php
session_start();
if (empty($_SESSION["id"])){header('Location: ../../../login/login.php');}

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
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
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
	
	
	
	
	<style>
/* Modal styles */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 150px;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 28%;
  z-index: 999999;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

/* Estilo para el título */
.tituloh2a {
    font-size: 24px;
    font-weight: bold;
    color: #c0392b; /* Rojo elegante */
    text-align: center;
    margin-bottom: 20px;
}

/* Estilo para el formulario */
#finalizarForm {
    background-color: #ffffff; /* Fondo blanco */
    padding: 20px;
	width: 100%;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra suave */
    max-width: 500px;
    margin: 0 auto;
}

/* Estilo para las etiquetas */
.labelfecha {
    font-size: 16px;
    color: #333333; /* Texto gris oscuro */
    display: block;
    margin-bottom: 10px;
}

/* Estilo para el input de fecha */
.fechainput {
    width: 50%; /* Ajusta el ancho a un 50% del contenedor */
    max-width: 300px; /* Limita el tamaño máximo para dispositivos más grandes */
    padding: 8px; /* Ajusta el padding para hacerlo más compacto */
    border: 1px solid #cccccc;
    border-radius: 5px;
    font-size: 16px;
    display: block;
    margin: 0 auto 20px auto; /* Centra horizontalmente y agrega margen inferior */
    box-sizing: border-box; /* Asegura que el padding no afecte el tamaño total */
}

/* Estilo para los checkboxes */
.checkcarreras {
    margin-right: 10px;
}

/* Estilo para el botón */
.botonguardar {
    background-color: #c0392b; /* Rojo elegante */
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: block;
    width: 100%;
}

.botonguardar:hover {
    background-color: #a93226; /* Rojo más oscuro al hacer hover */
}

/* Estilo para el contenido del modal */
#carrerasModalContent label {
    font-size: 16px;
    color: #333333; /* Texto gris oscuro */
    margin-bottom: 10px;
    display: block;
}
/* Estilo para los checkboxes */
.checkcarreras {
    appearance: none; /* Elimina el estilo por defecto del checkbox */
    width: 20px;
    height: 20px;
    border: 2px solid #c0392b; /* Borde rojo */
    border-radius: 4px; /* Bordes redondeados */
    cursor: pointer;
    position: relative;
    margin-right: 10px;
    vertical-align: middle;
}

/* Estilo para el checkbox cuando está seleccionado */
.checkcarreras:checked {
    background-color: #c0392b; /* Fondo rojo cuando está seleccionado */
    border-color: #c0392b; /* Mantiene el borde rojo */
}

/* Estilo para el check (marca de verificación) */
.checkcarreras:checked::before {
    content: '\2714'; /* Símbolo de check */
    font-size: 16px;
    color: #ffffff; /* Color del check blanco */
    position: absolute;
    top: -2px;
    left: 2px;
}

/* Estilo para el checkbox al enfocarse */
.checkcarreras:focus {
    outline: none; /* Elimina el contorno por defecto */
    box-shadow: 0 0 5px rgba(192, 57, 43, 0.5); /* Sombra roja suave */
}
#finalizarForm {
    text-align: center; /* Centra todos los elementos del formulario */
}
</style>









<style>
/* Estilo para el botón de "Sacar Finalización" */
.botonsacar {
    background-color: #c0392b; /* Rojo elegante */
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: block;
    width: 100%;
    margin-top: 10px; /* Margen superior para separarlo del contenido */
}

.botonsacar:hover {
    background-color: #a93226; /* Rojo más oscuro al hacer hover */
}

/* Estilos para el nuevo modal */
#removeFinalizacionModal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 150px;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

#removeFinalizacionModal .modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 28%;
  z-index: 999999;
}

.closeRemove {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.closeRemove:hover,
.closeRemove:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style> 
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
						<li class="nav-item active">
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
										<a href="./nuevo_estudianteFP.php">
											<span class="sub-item">Nuevo Estudiante FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
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
									<li class="active">
										<a href="./lista_estudianteFP.php">
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
										<a href="./informesFP/informe_lista_estudiantesFP.php">
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
                            <a href="./ver_FPS.php">
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
                            <a href="./ver_asistenciaFPS.php">
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
						<a href="../../gestionar_mesas_finales.php">
							<span class="sub-item">Gestión de Mesas</span>
						</a>
					</li>
					
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
						<?php endif; ?>
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
									
									<?php if ($rolUsuario == '1'): ?>
									<li>
										<a href="../Tecnicatura/pagos.php">
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
                <a href="../../Administracion/Profesores/pre_lista_promocionados.php">
                    <span class="sub-item">Actas Volantes Promocionados</span>
                </a>
            </li>
			<li>
                <a href="../../actas_volante_estudiantes_regulares.php">
                    <span class="sub-item">Actas Volantes Regulares</span>
                </a>
            </li>
			<li>
                <a href="../../actas_volante_estudiantes_libres.php">
                    <span class="sub-item">Actas Volantes Libres</span>
                </a>
            </li>
            <li>
                <a href="../../proximamente.php">
                    <span class="sub-item">Gestión de Comunicados</span>
                </a>
            </li>
			<li>
                <a href="../../pre_nota_final.php">
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
<?php
// Obtener los legajos de los alumnos que han finalizado
$sqlFinalizados = "SELECT alumnos_fp_legajo_afp FROM finalizo_FP";
$queryFinalizados = mysqli_query($conexion, $sqlFinalizados);

$alumnosFinalizados = array();
while ($row = mysqli_fetch_assoc($queryFinalizados)) {
    $alumnosFinalizados[] = $row['alumnos_fp_legajo_afp']; // Cambiado para asegurar el acceso correcto al campo
}

// Convertir el array de legajos a JSON para pasarlo a JavaScript
$alumnosFinalizadosJson = json_encode($alumnosFinalizados);
?>

<script>
// Convertir el JSON de PHP a un array de JavaScript
var alumnosFinalizados = <?php echo $alumnosFinalizadosJson; ?>;
</script>

<?php
// Obtener los legajos de los alumnos que han finalizado
$sqlFinalizados = "SELECT alumnos_fp_legajo_afp FROM finalizo_FP";
$queryFinalizados = mysqli_query($conexion, $sqlFinalizados);

$alumnosFinalizados = array();
while ($row = mysqli_fetch_assoc($queryFinalizados)) {
    $alumnosFinalizados[] = $row['alumnos_fp_legajo_afp']; // Cambiado para asegurar el acceso correcto al campo
}

// Convertir el array de legajos a JSON para pasarlo a JavaScript
$alumnosFinalizadosJson = json_encode($alumnosFinalizados);
?>

<script>
// Convertir el JSON de PHP a un array de JavaScript
var alumnosFinalizados = <?php echo $alumnosFinalizadosJson; ?>;
</script>

<div class="contenido">
  <div id="tablaContainerEstudiantesFP">
    <table id="tablaFP">
      <thead>
        <tr>
          <th class="legajoFP">Legajo</th>
          <th class="apellidoFP">Apellido</th>
          <th class="nombreFP">Nombre</th>
          <th class="dniFP">DNI</th>
          <th class="celularFP">Celular</th>
          <th class="carrera1FP">Carrera1</th>
          <th class="carrera2FP">Carrera2</th>
          <th class="carrera3FP">Carrera3</th>
          <th class="carrera4FP">Carrera4</th>
          <th class="carrera4FP">Estado</th>
          <th class="accionesFP">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Función para obtener las carreras finalizadas del alumno
        function obtenerCarrerasFinalizadas($conexion, $legajo_afp) {
            $carreras_finalizadas = [];
            $sql = "SELECT carreras_idCarrera FROM finalizo_FP WHERE alumnos_fp_legajo_afp = '$legajo_afp'";
            $query = mysqli_query($conexion, $sql);
            while ($row = mysqli_fetch_assoc($query)) {
                $carreras_finalizadas[] = $row['carreras_idCarrera'];
            }
            return $carreras_finalizadas;
        }

        // Consulta para obtener todos los alumnos
        $sql1 = "SELECT af.idalumnos_fp, af.legajo_afp, af.nombre_afp, af.apellido_afp, af.dni_afp, af.celular_afp,af.estado , 
                         c1.idCarrera AS carrera1_id, c1.nombre_carrera AS nombre_carrera1, 
                         c2.idCarrera AS carrera2_id, c2.nombre_carrera AS nombre_carrera2, 
                         c3.idCarrera AS carrera3_id, c3.nombre_carrera AS nombre_carrera3, 
                         c4.idCarrera AS carrera4_id, c4.nombre_carrera AS nombre_carrera4
                  FROM alumnos_fp af
                  LEFT JOIN carreras c1 ON af.carreras_idCarrera = c1.idCarrera
                  LEFT JOIN carreras c2 ON af.carreras_idCarrera1 = c2.idCarrera
                  LEFT JOIN carreras c3 ON af.carreras_idCarrera2 = c3.idCarrera
                  LEFT JOIN carreras c4 ON af.carreras_idCarrera3 = c4.idCarrera;";
        $query1 = mysqli_query($conexion, $sql1);
        while ($datos = mysqli_fetch_assoc($query1)) {
            // Obtener las carreras finalizadas del alumno
            $carreras_finalizadas = obtenerCarrerasFinalizadas($conexion, $datos['legajo_afp']);
        ?>
          <tr>
            <td><?php echo $datos['legajo_afp']; ?></td>
            <td><?php echo $datos['apellido_afp']; ?></td>
            <td><?php echo $datos['nombre_afp']; ?></td>
            <td><?php echo $datos['dni_afp']; ?></td>
            <td><?php echo $datos['celular_afp']; ?></td>
            <td style="color: <?php echo in_array($datos['carrera1_id'], $carreras_finalizadas) ? 'green' : 'inherit'; ?>">
                <?php echo $datos['nombre_carrera1']; ?>
            </td>
            <td style="color: <?php echo in_array($datos['carrera2_id'], $carreras_finalizadas) ? 'green' : 'inherit'; ?>">
                <?php echo $datos['nombre_carrera2']; ?>
            </td>
            <td style="color: <?php echo in_array($datos['carrera3_id'], $carreras_finalizadas) ? 'green' : 'inherit'; ?>">
                <?php echo $datos['nombre_carrera3']; ?>
            </td>
            <td style="color: <?php echo in_array($datos['carrera4_id'], $carreras_finalizadas) ? 'green' : 'inherit'; ?>">
                <?php echo $datos['nombre_carrera4']; ?>
            </td>
            <td>
                <?php
    if ($datos['estado'] == 1) {
        echo "Activo";
    } elseif ($datos['estado'] == 2) {
        echo "Inactivo";
    }
    ?>
            </td>
            <td>
              <a href="modificar_alumnoFP.php?legajo=<?php echo $datos['legajo_afp']; ?>" class="modificar-button"><i class="fas fa-pencil-alt"></i></a>
                <a href="#" onclick="showCarrerasModal('<?php echo $datos['legajo_afp']; ?>'); return false;" class="borrar-button">
                <i class="fas fa-trash-alt"></i>
                </a>             
                <a href="infoFP.php?legajo=<?php echo $datos['legajo_afp']; ?>" class="accion-button"><i class="fas fa-exclamation"></i></a>
              <a href="#" onclick="showModal('<?php echo $datos['idalumnos_fp']; ?>', '<?php echo $datos['legajo_afp']; ?>', '<?php echo $datos['carrera1_id']; ?>', '<?php echo $datos['nombre_carrera1']; ?>', '<?php echo $datos['carrera2_id']; ?>', '<?php echo $datos['nombre_carrera2']; ?>', '<?php echo $datos['carrera3_id']; ?>', '<?php echo $datos['nombre_carrera3']; ?>', '<?php echo $datos['carrera4_id']; ?>', '<?php echo $datos['nombre_carrera4']; ?>')" class="finalizar-button" data-finalizadas="<?php echo implode(',', $carreras_finalizadas); ?>"><i class="far fa-check-circle"></i></a>
            </td>
          </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
  </div>
</div>


<!-- Modal -->
<div id="finalizoModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 class="tituloh2a">Seleccionar Cursos Finalizados</h2>
    <form id="finalizarForm" method="POST" action="finalizar_alumnoFP.php">
      <input type="hidden" name="idalumnos_fp" id="idalumnos_fp" value="">
      <input type="hidden" name="legajo_afp" id="legajo_afp" value="">
      <div id="carrerasModalContent"></div>
      <label for="fecha_finalizacion" class="labelfecha">Fecha de Finalización:</label>
      <input type="date" class="fechainput" name="fecha_finalizacion" id="fecha_finalizacion" required>
      <br>
      <button class="botonguardar" type="submit">Guardar</button>
    </form>

    <!-- Botón para sacar la finalización -->
    <button class="botonsacar" onclick="openRemoveModal()">Sacar Finalización</button>
  </div>
</div>

<div id="removeFinalizacionModal" class="modal">
  <div class="modal-content">
    <span class="closeRemove">&times;</span>
    <h2 class="tituloh2a">Remover Finalización de Cursos</h2>
    <form id="removeFinalizarForm" method="POST" action="remover_finalizacion_alumnoFP.php">
      <input type="hidden" name="idalumnos_fp_remove" id="idalumnos_fp_remove" value="">
      <input type="hidden" name="legajo_afp_remove" id="legajo_afp_remove" value="">
      <div id="carrerasRemoveModalContent"></div>
      <br>
      <button class="botonguardar" type="submit">Guardar Cambios</button>
    </form>
  </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Carreras Inscriptas</h2>
        <div id="carreras-list"></div>
        <button onclick="confirmDelete('<?php echo $datos['legajo_afp']; ?>')">Confirmar</button>
    </div>
</div>


  
<!-- Core JS Files -->
<script src="../../assets/js/core/bootstrap.min.js"></script>
<!-- jQuery UI -->
<script src="../../assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<!-- jQuery Scrollbar -->
<script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<!-- Azzara JS -->
<script src="../../assets/js/ready.min.js"></script>

<script>
    
// dataTables de FP //
var myTable = document.querySelector("#tablaFP");
var dataTable = new DataTable(tablaFP);
</script>

<script>
// Obtener referencias a los elementos del DOM
var finalizoModal = document.getElementById("finalizoModal");
var carrerasModalContent = document.getElementById("carrerasModalContent");
var removeModal = document.getElementById("removeFinalizacionModal"); 
var spanFinalizo = document.getElementsByClassName("close")[0];
var spanRemove = document.getElementsByClassName("closeRemove")[0];

// Función para mostrar el modal de finalización
function showModal(idalumnos_fp, legajo_afp, carrera1_id, carrera1_name, carrera2_id, carrera2_name, carrera3_id, carrera3_name, carrera4_id, carrera4_name) {
  // Asignar valores al formulario dentro del modal
  document.getElementById("idalumnos_fp").value = idalumnos_fp;
  document.getElementById("legajo_afp").value = legajo_afp;

  // Realizar la solicitud AJAX para obtener las carreras finalizadas
  $.ajax({
    url: 'get_carreras_finalizadas.php', // Archivo PHP que maneja la solicitud
    type: 'POST',
    data: { legajo_afp: legajo_afp },
    success: function(response) {
      var finalizadasArray = JSON.parse(response); // Convertir la respuesta en array
      var carrerasContent = "";

      // Comprobamos si cada carrera está finalizada y si es así, marcamos el checkbox
      if (carrera1_id != 65) {
        carrerasContent += '<label><input type="checkbox" class="checkcarreras" name="carreras_finalizadas[]" value="' + carrera1_id + '"' + (finalizadasArray.some(carrera => carrera.id == carrera1_id) ? ' checked' : '') + '> ' + carrera1_name + '</label><br>';
      }
      if (carrera2_id != 65) {
        carrerasContent += '<label><input type="checkbox" class="checkcarreras" name="carreras_finalizadas[]" value="' + carrera2_id + '"' + (finalizadasArray.some(carrera => carrera.id == carrera2_id) ? ' checked' : '') + '> ' + carrera2_name + '</label><br>';
      }
      if (carrera3_id != 65) {
        carrerasContent += '<label><input type="checkbox" class="checkcarreras" name="carreras_finalizadas[]" value="' + carrera3_id + '"' + (finalizadasArray.some(carrera => carrera.id == carrera3_id) ? ' checked' : '') + '> ' + carrera3_name + '</label><br>';
      }
      if (carrera4_id != 65) {
        carrerasContent += '<label><input type="checkbox" class="checkcarreras" name="carreras_finalizadas[]" value="' + carrera4_id + '"' + (finalizadasArray.some(carrera => carrera.id == carrera4_id) ? ' checked' : '') + '> ' + carrera4_name + '</label><br>';
      }

      carrerasModalContent.innerHTML = carrerasContent;

      // Mostrar el modal
      finalizoModal.style.display = "block";
    },
    error: function() {
      alert('Error al obtener las carreras finalizadas.');
    }
  });
}

// Cerrar el modal de finalización cuando el usuario haga clic en <span> (x)
spanFinalizo.onclick = function() {
  finalizoModal.style.display = "none";
}

// Función para abrir el modal de remover finalización
function openRemoveModal() {
  var legajo_afp = document.getElementById("legajo_afp").value; // Obtener el legajo del modal principal
  document.getElementById("idalumnos_fp_remove").value = document.getElementById("idalumnos_fp").value; // Asignar el ID del alumno
  document.getElementById("legajo_afp_remove").value = legajo_afp; // Asignar el legajo al modal de remover

  // Realizar la solicitud AJAX para obtener las carreras finalizadas
  $.ajax({
    url: 'get_carreras_finalizadas.php', // Archivo PHP que maneja la solicitud
    type: 'POST',
    data: { legajo_afp: legajo_afp },
    success: function(response) {
      var finalizadasArray = JSON.parse(response); // Convertir la respuesta en array
      var carrerasContent = "";

      // Comprobamos si cada carrera está finalizada y mostramos el nombre de la carrera
      if (finalizadasArray.length > 0) {
        finalizadasArray.forEach(function(carrera) {
          carrerasContent += '<label><input type="checkbox" class="checkcarreras" name="carreras_finalizadas_remove[]" value="' + carrera.id + '" checked> ' + carrera.nombre + '</label><br>';
        });
      } else {
        carrerasContent += '<p>No hay carreras finalizadas para este alumno.</p>';
      }

      document.getElementById("carrerasRemoveModalContent").innerHTML = carrerasContent;

      removeModal.style.display = "block";
    },
    error: function() {
      alert('Error al obtener las carreras finalizadas.');
    }
  });
}

// Cerrar el modal de remover finalización cuando el usuario haga clic en <span> (x)
spanRemove.onclick = function() {
  removeModal.style.display = "none";
}

// Cuando el usuario hace clic fuera de cualquier modal, cerrar el modal correspondiente
window.onclick = function(event) {
  if (event.target == finalizoModal) {
    finalizoModal.style.display = "none";
  } else if (event.target == removeModal) {
    removeModal.style.display = "none";
  }
}

// Función para mostrar el modal de carreras inscriptas
function showCarrerasModal(legajo_afp) {
    var modal = document.getElementById("myModal");
    var carrerasList = document.getElementById("carreras-list");

    // Guardar el legajo_afp en un atributo data del modal para usarlo después
    modal.setAttribute("data-legajo", legajo_afp);

    // Limpiar la lista anterior (si existía)
    carrerasList.innerHTML = "";

    // Hacer la solicitud AJAX para obtener las carreras y las carreras no aprobadas
    $.ajax({
        url: 'obtener_carreras.php', // Archivo PHP que maneja la solicitud
        type: 'POST',
        data: { legajo_afp: legajo_afp },
        success: function(response) {
            console.log(response); // Agregar esto para verificar la respuesta en la consola del navegador
            var responseData = JSON.parse(response); // Parsear la respuesta JSON
            var carreras = responseData.carreras; // Lista de carreras en las que el alumno está inscrito
            var carrerasNoAprobadas = responseData.carreras_no_aprobadas; // Lista de carreras no aprobadas

            // Agregar las carreras al modal con checkboxes
            carreras.forEach(function(carrera) {
                var carreraItem = document.createElement("div");

                // Crear el checkbox
                var checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.name = "carreras";
                checkbox.value = carrera.idCarrera;  // Usa el idCarrera como valor

                // Verifica si la carrera ya está marcada como no aprobada
                if (carrerasNoAprobadas.includes(carrera.idCarrera)) {
                    checkbox.checked = true;  // Mantener el checkbox marcado si no está aprobada
                }

                // Crear el label para la carrera
                var label = document.createElement("label");
                label.textContent = carrera.nombre_carrera;  // Muestra el nombre de la carrera

                // Añadir el checkbox y el label al contenedor
                carreraItem.appendChild(checkbox);
                carreraItem.appendChild(label);

                // Añadir el contenedor de la carrera a la lista
                carrerasList.appendChild(carreraItem);
            });

            // Mostrar el modal
            modal.style.display = "block";
        },
        error: function() {
            alert('Error al obtener las carreras.');
        }
    });
}

// Cerrar el modal de carreras inscriptas cuando el usuario haga clic en <span> (x)
function closeModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
}

// Cerrar el modal cuando el usuario haga clic fuera del modal
window.onclick = function(event) {
    var modal = document.getElementById("myModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Función para confirmar la eliminación de carreras no aprobadas
function confirmDelete() {
    // Obtener el legajo_afp desde el atributo data del modal
    var modal = document.getElementById("myModal");
    var legajo_afp = modal.getAttribute("data-legajo");

    // Obtener todos los checkboxes y clasificarlos en seleccionados y desmarcados
    var checkboxes = document.querySelectorAll('input[name="carreras"]');
    var carrerasSeleccionadas = [];
    var carrerasDesmarcadas = [];

    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
            carrerasSeleccionadas.push(checkbox.value);  // Carreras seleccionadas para insertar
        } else {
            carrerasDesmarcadas.push(checkbox.value);  // Carreras desmarcadas para eliminar
        }
    });

    // Crear la solicitud AJAX para insertar las carreras seleccionadas
    if (carrerasSeleccionadas.length > 0) {
        var xhrInsert = new XMLHttpRequest();
        xhrInsert.open("POST", "insertar_no_aprobados.php", true);
        xhrInsert.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        var dataInsert = "legajo_afp=" + encodeURIComponent(legajo_afp) +
                         "&carreras=" + encodeURIComponent(carrerasSeleccionadas.join(","));

        xhrInsert.onreadystatechange = function() {
            if (xhrInsert.readyState === 4 && xhrInsert.status === 200) {
                console.log("Carreras insertadas con éxito.");
            }
        };

        xhrInsert.send(dataInsert);
    }

    // Crear la solicitud AJAX para eliminar las carreras desmarcadas
    if (carrerasDesmarcadas.length > 0) {
        var xhrDelete = new XMLHttpRequest();
        xhrDelete.open("POST", "eliminar_noaprobados.php", true);
        xhrDelete.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        var dataDelete = "legajo_afp=" + encodeURIComponent(legajo_afp) +
                         "&carrerasDesmarcadas=" + encodeURIComponent(carrerasDesmarcadas.join(","));

        xhrDelete.onreadystatechange = function() {
            if (xhrDelete.readyState === 4 && xhrDelete.status === 200) {
                console.log("Carreras eliminadas con éxito.");
            }
        };

        xhrDelete.send(dataDelete);
    }

    // Mostrar un mensaje de éxito y recargar la página
    alert("Actualización realizada exitosamente.");
    setTimeout(function() {
        location.reload();
    }, 1000);  // 1 segundo de retraso antes de recargar la página
}
</script>







</body>
</html>

