<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../login/login.php');
    exit;
}

// Asumimos que también almacenas el rol en la sesión.
$rolUsuario = $_SESSION["roles"];

// Definimos los roles permitidos para esta página.
$rolesPermitidos = ['1', '2', '3', '4','5'];

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
									<a class="dropdown-item" href="proximamente.php">Mi Perfil</a>
									<a class="dropdown-item" href="proximamente.php">Cambiar Contraseña</a>
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
			<li>
                <a href="./actas_volante_estudiantes_regulares.php">
                    <span class="sub-item">Actas Volantes Regulares</span>
                </a>
            </li>
			<li >
                <a href="./actas_volante_estudiantes_libres.php">
                    <span class="sub-item">Actas Volantes Libres</span>
                </a>
            </li>
            <li>
                <a href="./proximamente.php">
                    <span class="sub-item">Gestión de Comunicados</span>
                </a>
            </li>
			<li class="active">
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

<?php


// Obtener el ID del profesor y el rol desde la sesión
$idProfesor = $_SESSION['id'];
$rolUsuario = $_SESSION['roles'];

/* =============================
   1) Consulta de Carreras
   ============================= */
if ($rolUsuario == '1') {
    // Rol 1: Administrador => todas las carreras
    $sqlCarreras = "SELECT * FROM carreras";
} else {
    // Caso contrario: solo las carreras asignadas al profesor
    $sqlCarreras = "SELECT c.*
                    FROM carreras c
                    JOIN preceptores p ON c.idCarrera = p.carreras_idCarrera
                    WHERE p.profesor_idProrfesor = $idProfesor";
}
$resultCarreras = mysqli_query($conexion, $sqlCarreras);
if (!$resultCarreras) {
    die("Error al consultar carreras: " . mysqli_error($conexion));
}

/* =============================
   2) Consulta de Comisiones
   ============================= */
if ($rolUsuario == '1') {
    // Rol 1: Administrador => todas las comisiones
    $sqlComisiones = "SELECT * FROM comisiones";
} else {
    // Caso contrario: solo las comisiones asignadas al profesor
    $sqlComisiones = "SELECT co.*
                      FROM comisiones co
                      JOIN preceptores p ON co.idComisiones = p.comisiones_idComisiones
                      WHERE p.profesor_idProrfesor = $idProfesor";
}
$resultComisiones = mysqli_query($conexion, $sqlComisiones);
if (!$resultComisiones) {
    die("Error al consultar comisiones: " . mysqli_error($conexion));
}

/* =============================
   3) Consulta de Cursos
   ============================= */
if ($rolUsuario == '1') {
    // Rol 1: Administrador => todos los cursos
    $sqlCursos = "SELECT * FROM cursos";
} else {
    // Caso contrario: solo los cursos asignados al profesor
    $sqlCursos = "SELECT cu.*
                  FROM cursos cu
                  JOIN preceptores p ON cu.idCursos = p.cursos_idCursos
                  WHERE p.profesor_idProrfesor = $idProfesor";
}
$resultCursos = mysqli_query($conexion, $sqlCursos);
if (!$resultCursos) {
    die("Error al consultar cursos: " . mysqli_error($conexion));
}
?>

<!-- Suponiendo que ya has hecho el include del PHP que obtiene $resultCarreras, $resultCursos, $resultComisiones -->

<div class="contenido">
    <div class="form-container">
        <form action="nota_examen_final.php" method="POST">
            <h2>Asignar Notas Finales</h2>

            <!-- FILTRO: CARRERA -->
            <label for="selectCarrera">Seleccione una carrera:</label>
            <select id="selectCarrera" name="carrera" class="input-form" required>
                <option value="">Seleccione una carrera</option>
                <?php
                while ($row = mysqli_fetch_assoc($resultCarreras)) {
                    echo '<option value="' . $row['idCarrera'] . '">' . $row['nombre_carrera'] . '</option>';
                }
                ?>
            </select>

            
            
            <!-- FILTRO: CURSO -->
            <label for="curso">Seleccione un curso:</label>
            <select id="selectCurso" name="curso" class="input-form" required>
                <option value="">Seleccione un curso</option>
                <?php
                while ($rowC = mysqli_fetch_assoc($resultCursos)) {
                    echo '<option value="' . $rowC['idCursos'] . '">' . $rowC['curso'] . '</option>';
                }
                ?>
            </select>
            
            <!-- FILTRO: COMISIÓN -->
            <label for="comision">Seleccione una comisión:</label>
            <select id="selectComision" name="comision" class="input-form" required>
                <option value="">Seleccione una comisión</option>
                <?php
                while ($rowCo = mysqli_fetch_assoc($resultComisiones)) {
                    echo '<option value="' . $rowCo['idComisiones'] . '">' . $rowCo['comision'] . '</option>';
                }
                ?>
            </select>
            
            <!-- FILTRO: MATERIA -->
            <label for="materia">Seleccione una Unidad Curricular:</label>
            <select id="selectMateria" name="materia" class="input-form" required>
                <option value="">Seleccione una Unidad Curricular</option>
            </select>

            <!-- FILTRO: TURNO -->
            <!-- FILTRO: TURNO -->
<label for="turno">Seleccione un turno:</label>
<select id="selectTurno" name="turno" class="input-form" required>
    <option hidden value="">Selecciona un turno</option>
</select>


            <!-- FILTRO: AÑO -->
            <label for="turno">Seleccione un año:</label>
            <select name="año" class="input-form" required>
                <option hidden value="">Selecciona un año</option>
                <?php for ($year = 2024; $year <= 2034; $year++) { ?>
                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                <?php } ?>
            </select>

            <button type="submit" class="btn-submit">Mostrar Alumnos</button>
        </form>
    </div>
</div>

<!-- ================================
     INICIO DEL SCRIPT COMPLETO
   ================================ -->
<script>
   // 1) Lista de materias cuatrimestrales (IDs) para actualizar turnos
const materiasCuatrimestrales = ['146', '415', '426', '193', '444', '453'];

// 2) Función que actualiza las opciones del turno según si la materia es cuatrimestral
function actualizarTurnos(isCuatrimestral) {
    const selectTurno = document.getElementById('selectTurno');

    // Definir las opciones de turno con los meses
    const opcionesTurno = isCuatrimestral
        ? [
            { value: "1", text: "Turno 1 (Julio - Agosto)" },
            { value: "2", text: "Turno 2 (Noviembre - Diciembre)" },
            { value: "3", text: "Turno 3 (Febrero - Marzo)" },
            { value: "4", text: "Turno 4 (Julio - Agosto)" },
            { value: "5", text: "Turno 5 (Noviembre - Diciembre)" },
            { value: "6", text: "Turno 6 (Febrero - Marzo)" },
            { value: "7", text: "Turno 7 (Julio - Agosto)" }
          ]
        : [
            { value: "1", text: "Turno 1 (Noviembre - Diciembre)" },
            { value: "2", text: "Turno 2 (Febrero - Marzo)" },
            { value: "3", text: "Turno 3 (Julio - Agosto)" },
            { value: "4", text: "Turno 4 (Noviembre - Diciembre)" },
            { value: "5", text: "Turno 5 (Febrero - Marzo)" },
            { value: "6", text: "Turno 6 (Julio - Agosto)" },
            { value: "7", text: "Turno 7 (Noviembre - Diciembre)" }
          ];

    // Limpiar las opciones actuales del select de turnos
    selectTurno.innerHTML = '<option hidden value="">Selecciona un turno</option>';

    // Agregar las nuevas opciones dinámicamente
    opcionesTurno.forEach(opc => {
        const optionElement = document.createElement('option');
        optionElement.value = opc.value;
        optionElement.textContent = opc.text;
        selectTurno.appendChild(optionElement);
    });
}

    // 3) Escucha el cambio en "selectMateria" para cambiar turnos si la materia es cuatrimestral
    document.getElementById('selectMateria').addEventListener('change', function() {
        const materiaId = this.value;
        const isCuatrimestral = materiasCuatrimestrales.includes(materiaId);
        actualizarTurnos(isCuatrimestrales.includes(materiaId));
    });

   // 3) Detectar el cambio en el select de materias
document.getElementById('selectMateria').addEventListener('change', function() {
    const materiaId = this.value;
    const isCuatrimestral = materiasCuatrimestrales.includes(materiaId);
    actualizarTurnos(isCuatrimestral);
});

// 4) Cargar materias dinámicamente (ajustado para asegurar que se actualicen correctamente)
const selectCarrera  = document.getElementById('selectCarrera');
const selectCurso    = document.getElementById('selectCurso');
const selectComision = document.getElementById('selectComision');
const selectMateria  = document.getElementById('selectMateria');

function cargarMaterias() {
    const carreraId  = selectCarrera.value;
    const cursoId    = selectCurso.value;
    const comisionId = selectComision.value;

    if (carreraId && cursoId && comisionId) {
        const params = new URLSearchParams();
        params.append('carrera_id',  carreraId);
        params.append('curso_id',    cursoId);
        params.append('comision_id', comisionId);

        fetch('obtener_materias_preces_mesas_final.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params.toString()
        })
        .then(response => response.json())
        .then(materias => {
            console.log("Materias recibidas:", materias);
            selectMateria.innerHTML = '<option value="">Seleccione una Unidad Curricular</option>';
            
            materias.forEach(m => {
                const option = document.createElement('option');
                option.value = m.idMaterias;
                option.text  = m.Nombre;
                selectMateria.appendChild(option);
            });
            selectMateria.disabled = false;
        })
        .catch(error => {
            console.error('Error al cargar materias:', error);
            selectMateria.innerHTML = '<option value="">Error cargando materias</option>';
            selectMateria.disabled = true;
        });
    } else {
        selectMateria.innerHTML = '<option value="">Seleccione una Unidad Curricular</option>';
        selectMateria.disabled = true;
    }
}

// Escuchar cambios en carrera, curso y comisión
selectCarrera.addEventListener('change', cargarMaterias);
selectCurso.addEventListener('change', cargarMaterias);
selectComision.addEventListener('change', cargarMaterias);

    // ==========================
    // 5) Validar selects antes de enviar (ya lo tenías)
    // ==========================
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const selects = document.querySelectorAll("select");

        form.addEventListener("submit", function (event) {
            let allSelected = true;

            selects.forEach(select => {
                if (select.value === "") {
                    allSelected = false;
                    select.classList.add("error"); // Agregar clase para indicar error visualmente
                } else {
                    select.classList.remove("error");
                }
            });

            if (!allSelected) {
                event.preventDefault(); // Detener el envío del formulario
                alert("Por favor, selecciona todos los campos antes de continuar.");
            }
        });

        // Remueve la clase de error al cambiar la selección
        selects.forEach(select => {
            select.addEventListener("change", function () {
                if (select.value !== "") {
                    select.classList.remove("error");
                }
            });
        });
    });
</script>
<!-- ================================
     FIN DEL SCRIPT COMPLETO
   ================================ -->


<!-- Core JS Files (si los requieres) -->
<script src="assets/js/core/jquery.3.2.1.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="assets/js/ready.min.js"></script>


<style>
	/* Cuadro semitransparente */
.form-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 30px;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Estilo para inputs y selects */
input.input-form, select.input-form {
    width: 90%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    transition: border 0.3s;
}

input.input-form:focus, select.input-form:focus {
    border-color: #f3545d;
    outline: none;
}

/* Estilo para el botón */
button.btn-submit {
    background-color: #f3545d;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button.btn-submit:hover {
    background-color: #d8444a;
}

/* Etiquetas del formulario */
form label {
    font-weight: bold;
    margin-top: 10px;
    display: block;
    color: #333;
}



select:disabled {
    background-color: #f0f0f0;
}


.error {
        border: 2px solid red; /* Destaca visualmente los selects no seleccionados */
    }
</style>



</body>
</html>
