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
							<a data-toggle="collapse" href="#tables">
								<i class="fas fa-chalkboard-teacher"></i>
								<p>Profesores</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="tables">
								<ul class="nav nav-collapse">
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
						<a href="./Administracion/Profesores/acta_volante_materias.php">
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


// Definir un array de IDs de materias cuatrimestrales
$materias_cuatrimestrales = ['146','415','426','193','444','453'];

// Obtener el ID de la materia y el turno seleccionado
$idMateria = $_POST['materia'] ?? $_GET['materia'] ?? null;
$turno_seleccionado = $_POST['turno'] ?? $_GET['turno'] ?? null;
$año = $_POST['año'] ?? $_GET['año'] ?? null;
// Variables adicionales para el curso y la comisión
$idCurso = $_POST['curso'] ?? $_GET['curso'] ?? null;
$idComision = $_POST['comision'] ?? $_GET['comision'] ?? null;


// Determinar si la materia es cuatrimestral o anual
$es_cuatrimestral = in_array($idMateria, $materias_cuatrimestrales);

// Ajustar fechas de inicio y fin según el turno y tipo de materia
switch ($turno_seleccionado) {
    case 1:
        if ($es_cuatrimestral) {
            $fecha_inicio = "$año-07-01";
            $fecha_fin = "$año-08-31";
            $num_llamados = 1;
        } else {
            $fecha_inicio = "$año-11-01";
            $fecha_fin = "$año-12-31";
            $num_llamados = 2;
        }
        break;
    case 2:
        if ($es_cuatrimestral) {
            $fecha_inicio = "$año-11-01";
            $fecha_fin = "$año-12-31";
            $num_llamados = 2;
        } else {
            $fecha_inicio = "$año-02-01";
            $fecha_fin = "$año-03-31";
            $num_llamados = 2;
        }
        break;
    case 3:
        if ($es_cuatrimestral) {
            $fecha_inicio = "$año-02-01";
            $fecha_fin = "$año-03-31";
            $num_llamados = 2;
        } else {
            $fecha_inicio = "$año-07-01";
            $fecha_fin = "$año-08-31";
            $num_llamados = 1;
        }
        break;
    case 4:
        if ($es_cuatrimestral) {
            $fecha_inicio = "$año-07-01";
            $fecha_fin = "$año-08-31";
            $num_llamados = 1;
        } else {
            $fecha_inicio = "$año-11-01";
            $fecha_fin = "$año-12-31";
            $num_llamados = 2;
        }
        break;
    case 5:
        if ($es_cuatrimestral) {
            $fecha_inicio = "$año-11-01";
            $fecha_fin = "$año-12-31";
            $num_llamados = 2;
        } else {
            $fecha_inicio = "$año-02-01";
            $fecha_fin = "$año-03-31";
            $num_llamados = 2;
        }
        break;
    case 6:
        if ($es_cuatrimestral) {
            $fecha_inicio = "$año-02-01";
            $fecha_fin = "$año-03-31";
            $num_llamados = 2;
        } else {
            $fecha_inicio = "$año-07-01";
            $fecha_fin = "$año-08-31";
            $num_llamados = 1;
        }
        break;
    case 7:
        if ($es_cuatrimestral) {
            $fecha_inicio = "$año-07-01";
            $fecha_fin = "$año-08-31";
            $num_llamados = 1;
        } else {
            $fecha_inicio = "$año-11-01";
            $fecha_fin = "$año-12-31";
            $num_llamados = 2;
        }
        break;
    default:
        echo "<script>
            alert('Turno no válido.');
            window.location.href = 'pre_nota_final.php';
        </script>";
        exit;
}

// Mostrar las fechas para depuración
echo "Fecha Inicio: $fecha_inicio <br>";
echo "Fecha Fin: $fecha_fin <br>";


// Consulta SQL para obtener los estudiantes inscritos y sus notas en el período actual
$query = "
    SELECT 
        a.nombre_alumno, 
        a.apellido_alumno, 
        a.dni_alumno, 
        mf.alumno_legajo,
        nef1.nota AS nota_primer_llamado,
        nef1.tomo AS tomo_primer_llamado,
        nef1.folio AS folio_primer_llamado,
        nef2.nota AS nota_segundo_llamado,
        nef2.tomo AS tomo_segundo_llamado,
        nef2.folio AS folio_segundo_llamado
    FROM mesas_finales mf
    JOIN alumno a ON mf.alumno_legajo = a.legajo
    JOIN fechas_mesas_finales fmf ON mf.fechas_mesas_finales_idfechas_mesas_finales = fmf.idfechas_mesas_finales
    JOIN tandas t ON fmf.tandas_idtandas = t.idtandas
    JOIN inscripcion_asignatura ia ON ia.alumno_legajo = a.legajo
    JOIN materias m ON m.comisiones_idComisiones = ia.Comisiones_idComisiones
    LEFT JOIN nota_examen_final nef1 
        ON mf.alumno_legajo = nef1.alumno_legajo 
        AND nef1.llamado = 1 
        AND nef1.materias_idMaterias = ?
    LEFT JOIN nota_examen_final nef2 
        ON mf.alumno_legajo = nef2.alumno_legajo 
        AND nef2.llamado = 2 
        AND nef2.materias_idMaterias = ?
    WHERE mf.materias_idMaterias = ?
      AND t.fecha BETWEEN ? AND ?
      AND ia.año_inscripcion = ?
      AND ia.cursos_idCursos = ?
      AND ia.Comisiones_idComisiones = ?
    GROUP BY mf.alumno_legajo
    ORDER BY a.apellido_alumno ASC
";

// Preparar la consulta con los parámetros necesarios
$stmt = $conexion->prepare($query);
$stmt->bind_param("iiissiii", $idMateria, $idMateria, $idMateria, $fecha_inicio, $fecha_fin, $año, $idCurso, $idComision);
$stmt->execute();
$result = $stmt->get_result();

// Obtener el nombre de la materia
$query_materia = "SELECT Nombre FROM materias WHERE idMaterias = ?";
$stmt_materia = $conexion->prepare($query_materia);
$stmt_materia->bind_param("i", $idMateria);
$stmt_materia->execute();
$result_materia = $stmt_materia->get_result();
$materia_nombre = $result_materia->fetch_assoc()['Nombre'];

// Array de materias cuatrimestrales (puedes mover esto a una configuración más centralizada si lo usas en varios lugares)
$materiasCuatrimestrales = ['146', '415', '426', '193', '444', '453'];

// Determinar si la materia es cuatrimestral
$isCuatrimestral = in_array($idMateria, $materiasCuatrimestrales);

// Definir los meses según el turno y el tipo de materia
if ($isCuatrimestral) {
    $meses_turno = [
        "1" => "Julio - Agosto",
        "2" => "Noviembre - Diciembre",
        "3" => "Febrero - Marzo",
        "4" => "Julio - Agosto",
        "5" => "Noviembre - Diciembre",
        "6" => "Febrero - Marzo",
        "7" => "Julio - Agosto",
    ];
} else {
    $meses_turno = [
        "1" => "Noviembre - Diciembre",
        "2" => "Febrero - Marzo",
        "3" => "Julio - Agosto",
        "4" => "Noviembre - Diciembre",
        "5" => "Febrero - Marzo",
        "6" => "Julio - Agosto",
        "7" => "Noviembre - Diciembre",
    ];
}

// Determinar los meses correspondientes al turno seleccionado
$meses = $meses_turno[$turno_seleccionado] ?? "Meses no definidos";

// Liberar recursos
$stmt_materia->close();
?>

<div class="contenido">
    <div class="form-container">
        <h2>Mesa Final: Lista de Inscriptos: <?php echo htmlspecialchars($materia_nombre); ?> - Mesas del período actual -   Turno <?php echo htmlspecialchars($turno_seleccionado); ?> 
    (<?php echo htmlspecialchars($meses); ?>) </h2>
        <?php if ($result->num_rows > 0) { ?>
            <form action="guardar_nota_examen_final.php" method="POST">
                <div class="table-responsive">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Apellido</th>
                                <th>Nombre</th>
                                <th>DNI</th>
                                <th colspan="4" class="llamados">Primer Llamado</th>
                                <th colspan="4" class="llamados">Segundo Llamado</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Nota</th>
                                <th>Tomo</th>
                                <th>Folio</th>
                                <th>Ausente</th>
                                <th>Nota</th>
                                <th>Tomo</th>
                                <th>Folio</th>
                                <th>Ausente</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php 
    $contador = 1;
    while ($row = $result->fetch_assoc()) { 
        // Verificar si el segundo llamado debe estar habilitado
        $habilitarSegundoLlamado = ($row['nota_primer_llamado'] !== null && $row['nota_primer_llamado'] < 6);
    ?>
        <tr>
            <td><?php echo $contador; ?></td>
            <td><?php echo htmlspecialchars($row['apellido_alumno']); ?></td>
            <td><?php echo htmlspecialchars($row['nombre_alumno']); ?></td>
            <td><?php echo htmlspecialchars($row['dni_alumno']); ?></td>

            <!-- Input oculto para el legajo del estudiante -->
            <input type="hidden" name="alumno_legajo[<?php echo $contador; ?>]" value="<?php echo htmlspecialchars($row['alumno_legajo']); ?>">

          <!-- Primer Llamado -->
<td id="primer-llamado-<?php echo $contador; ?>" class="input-nota-container">
    <?php if ($row['nota_primer_llamado'] !== null && ($row['nota_primer_llamado'] === '0' || $row['nota_primer_llamado'] == 0)): ?>
        <!-- Mostrar la "A" si la nota es 0 -->
        <span class="ausente">A</span>
    <?php else: ?>
        <!-- Mostrar input si la nota no es 0 o es NULL -->
        <input 
            type="number" 
            name="nota_final_1[<?php echo $contador; ?>]" 
            class="input-nota nota-1" 
            data-segundo-llamado="segundo-llamado-<?php echo $contador; ?>" 
            min="0" 
            max="10" 
            step="0.1" 
            onchange="evaluarNota(this)"
            value="<?php echo htmlspecialchars($row['nota_primer_llamado']); ?>">
    <?php endif; ?>
</td>
            <td><input type="number" name="tomo_1[<?php echo $contador; ?>]" class="input-nota nota-1" value="<?php echo htmlspecialchars($row['tomo_primer_llamado']); ?>"></td>
            <td><input type="number" name="folio_1[<?php echo $contador; ?>]" class="input-nota nota-1" value="<?php echo htmlspecialchars($row['folio_primer_llamado']); ?>"></td>
            <td>
                <input 
                    type="checkbox" 
                    name="ausente_1[<?php echo $contador; ?>]" 
                    value="1" 
                    data-llamado="primer-llamado-<?php echo $contador; ?>" 
                    onchange="toggleAusente(this)"
                    <?php 
                    echo ($row['nota_primer_llamado'] !== null && $row['nota_primer_llamado'] !== '0') ? 'disabled' : ''; 
                    ?>
                    <?php echo ($row['nota_primer_llamado'] === '0') ? 'checked' : ''; ?>>
            </td>

           <!-- Segundo Llamado -->
<td id="segundo-llamado-<?php echo $contador; ?>" class="input-nota-container">
    <?php if ($row['nota_segundo_llamado'] !== null && ($row['nota_segundo_llamado'] === '0' || $row['nota_segundo_llamado'] == 0)): ?>
        <!-- Mostrar la "A" si la nota es 0 -->
        <span class="ausente">A</span>
    <?php else: ?>
        <!-- Mostrar input si la nota no es 0 o es NULL -->
        <input 
            type="number" 
            name="nota_final_2[<?php echo $contador; ?>]" 
            class="input-nota nota-2" 
            min="0" 
            max="10" 
            step="0.1" 
            value="<?php echo htmlspecialchars($row['nota_segundo_llamado']); ?>" 
            <?php echo !$habilitarSegundoLlamado ? 'disabled' : ''; ?>>
    <?php endif; ?>
</td>
            <td><input type="number" name="tomo_2[<?php echo $contador; ?>]" class="input-nota nota-1" value="<?php echo htmlspecialchars($row['tomo_segundo_llamado']); ?>" 
                <?php echo !$habilitarSegundoLlamado ? 'disabled' : ''; ?>></td>
            <td><input type="number" name="folio_2[<?php echo $contador; ?>]" class="input-nota nota-1" value="<?php echo htmlspecialchars($row['folio_segundo_llamado']); ?>" 
                <?php echo !$habilitarSegundoLlamado ? 'disabled' : ''; ?>></td>
            <td>
                <input 
                    type="checkbox" 
                    name="ausente_2[<?php echo $contador; ?>]" 
                    value="1" 
                    data-llamado="segundo-llamado-<?php echo $contador; ?>" 
                    onchange="toggleAusente(this)"
                    <?php echo !$habilitarSegundoLlamado ? 'disabled' : ''; ?>
                    <?php echo ($row['nota_segundo_llamado'] === '0') ? 'checked' : ''; ?>>
            </td>
        </tr>
    <?php $contador++; } ?>
</tbody>
                    </table>
                </div>
                <input type="hidden" name="materia" value="<?php echo htmlspecialchars($idMateria); ?>">
                <input type="hidden" name="turno" value="<?php echo htmlspecialchars($turno_seleccionado); ?>">
                <input type="hidden" name="año" value="<?php echo htmlspecialchars($año); ?>">
                <button type="submit" class="btn-submit">Guardar Notas</button>
            </form>
        <?php } else { ?>
            <p>No hay estudiantes inscriptos en la mesa final para el período actual sin nota registrada.</p>
        <?php } ?>
    </div>
</div>

<script>
    function evaluarNota(input) {
    const nota = parseFloat(input.value);
    const segundoLlamadoId = input.getAttribute('data-segundo-llamado');
    const segundoLlamado = document.getElementById(segundoLlamadoId);

    // Seleccionar los campos del Segundo Llamado dentro del mismo contenedor
    const notaInput = segundoLlamado.querySelector(`input[name="nota_final_2[${segundoLlamadoId.split('-')[2]}]"]`);
    const tomoInput = segundoLlamado.parentElement.querySelector(`input[name="tomo_2[${segundoLlamadoId.split('-')[2]}]"]`);
    const folioInput = segundoLlamado.parentElement.querySelector(`input[name="folio_2[${segundoLlamadoId.split('-')[2]}]"]`);
    const ausenteCheckbox = segundoLlamado.parentElement.querySelector(`input[name="ausente_2[${segundoLlamadoId.split('-')[2]}]"]`);

    if (nota >= 6) {
        if (notaInput) notaInput.disabled = true, notaInput.value = '';
        if (tomoInput) tomoInput.disabled = true, tomoInput.value = '';
        if (folioInput) folioInput.disabled = true, folioInput.value = '';
        if (ausenteCheckbox) ausenteCheckbox.disabled = true, ausenteCheckbox.checked = false;
    } else {
        if (notaInput) notaInput.disabled = false;
        if (tomoInput) tomoInput.disabled = false;
        if (folioInput) folioInput.disabled = false;
        if (ausenteCheckbox) ausenteCheckbox.disabled = false;
    }
    
    // Asegurarse de habilitar siempre tomo y folio
    if (tomoInput) tomoInput.disabled = false;
    if (folioInput) folioInput.disabled = false;
}

function toggleAusente(checkbox) {
    const llamadoId = checkbox.getAttribute('data-llamado');
    const llamado = document.getElementById(llamadoId);

    if (checkbox.checked) {
        const notaInput = llamado.querySelector(`input[name="nota_final_1[${llamadoId.split('-')[2]}]"]`);
        if (notaInput) notaInput.value = '', notaInput.disabled = true;

        const segundoLlamadoId = llamadoId.replace('primer-llamado', 'segundo-llamado');
        const segundoLlamado = document.getElementById(segundoLlamadoId);

        if (segundoLlamado) {
            const notaInputSegundo = segundoLlamado.querySelector(`input[name="nota_final_2[${segundoLlamadoId.split('-')[2]}]"]`);
            const tomoInputSegundo = segundoLlamado.parentElement.querySelector(`input[name="tomo_2[${segundoLlamadoId.split('-')[2]}]"]`);
            const folioInputSegundo = segundoLlamado.parentElement.querySelector(`input[name="folio_2[${segundoLlamadoId.split('-')[2]}]"]`);
            const ausenteCheckboxSegundo = segundoLlamado.parentElement.querySelector(`input[name="ausente_2[${segundoLlamadoId.split('-')[2]}]"]`);

            if (notaInputSegundo) notaInputSegundo.disabled = false;
            if (tomoInputSegundo) tomoInputSegundo.disabled = false;
            if (folioInputSegundo) folioInputSegundo.disabled = false;
            if (ausenteCheckboxSegundo) ausenteCheckboxSegundo.disabled = false;
        }
    } else {
        if (llamadoId.includes('primer-llamado')) {
            const notaInput = llamado.querySelector(`input[name="nota_final_1[${llamadoId.split('-')[2]}]"]`);
            if (notaInput) notaInput.disabled = false;

            const segundoLlamadoId = llamadoId.replace('primer-llamado', 'segundo-llamado');
            const segundoLlamado = document.getElementById(segundoLlamadoId);

            if (segundoLlamado) {
                const notaInputSegundo = segundoLlamado.querySelector(`input[name="nota_final_2[${segundoLlamadoId.split('-')[2]}]"]`);
                const tomoInputSegundo = segundoLlamado.parentElement.querySelector(`input[name="tomo_2[${segundoLlamadoId.split('-')[2]}]"]`);
                const folioInputSegundo = segundoLlamado.parentElement.querySelector(`input[name="folio_2[${segundoLlamadoId.split('-')[2]}]"]`);
                const ausenteCheckboxSegundo = segundoLlamado.parentElement.querySelector(`input[name="ausente_2[${segundoLlamadoId.split('-')[2]}]"]`);

                if (notaInputSegundo) notaInputSegundo.disabled = true, notaInputSegundo.value = '';
                if (tomoInputSegundo) tomoInputSegundo.disabled = true, tomoInputSegundo.value = '';
                if (folioInputSegundo) folioInputSegundo.disabled = true, folioInputSegundo.value = '';
                if (ausenteCheckboxSegundo) ausenteCheckboxSegundo.checked = false, ausenteCheckboxSegundo.disabled = true;
            }
        }
    }
}
</script>



<?php
$stmt->close();
$conexion->close();
?>


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
      .llamados {
        text-align: center; /* Centrar horizontalmente */
        vertical-align: middle; /* Centrar verticalmente */
    }
    /* Para navegadores con motor WebKit (Chrome, Safari, Edge) */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0; /* Asegura que no queden espacios adicionales */
}

/* Para Firefox */
input[type="number"] {
    -moz-appearance: textfield; /* Cambia el estilo para que parezca un input de texto */
}
    /* Estilo general para el checkbox */
input[type="checkbox"] {
    appearance: none; /* Elimina el diseño predeterminado del navegador */
    width: 20px;
    height: 20px;
    border: 2px solid #ccc; /* Borde minimalista */
    border-radius: 4px; /* Bordes ligeramente redondeados */
    cursor: pointer;
    transition: all 0.3s ease; /* Suaviza los cambios de estilo */
}

/* Estilo para el estado seleccionado */
input[type="checkbox"]:checked {
    background-color: #f3545d; /* Color rojo al estar marcado */
    border-color: #f3545d; /* El borde también cambia a rojo */
}

/* Efecto hover para indicar interactividad */
input[type="checkbox"]:hover {
    border-color: #999; /* Cambia el color del borde al pasar el mouse */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2); /* Añade un pequeño sombreado */
}
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

 .form-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 30px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .table-responsive {
        overflow-x: auto;
        margin-top: 20px;
    }

    .styled-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 16px;
        text-align: left;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        min-width: 700px;
    }

    .styled-table thead tr {
        background-color: #f3545d;
        color: #ffffff;
    }

    .styled-table th, .styled-table td {
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    .styled-table tbody tr:nth-of-type(even) {
        background-color: #f9f9f9;
    }

    .styled-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .styled-table td input.input-nota {
        width: 100%;
        max-width: 80px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: center;
    }

    button.btn-submit {
        background-color: #f3545d;
        color: #fff;
        padding: 10px 20px;
        margin-top: 15px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button.btn-submit:hover {
        background-color: #d8444a;
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 15px;
        }

        .styled-table th, .styled-table td {
            padding: 8px 10px;
        }
    }

</style>

</body>
</html>

