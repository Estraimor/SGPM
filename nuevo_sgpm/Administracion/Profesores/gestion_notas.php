<?php
session_start();
if (empty($_SESSION["id"])){header('Location: ../../../login/login.php');}

// Asumimos que también almacenas el rol en la sesión.
$rolUsuario = $_SESSION["roles"];

// Definimos los roles permitidos para esta página.
$rolesPermitidos = ['1', '2', '4','5'];

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
<html lang="es">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>SGPM</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="../../assets/img/Logo ISPM 2 transparante.png" type="image/x-icon"/>
	<!-- DATA TABLES -->
   
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
<?php if ($imagenBase64 === '../../assets/img/1361728.png') : ?>
    <!-- Mostrar imagen por defecto si no hay avatar -->
    <img src="../../assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;">
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
									<a class="dropdown-item" href="../../../../login/cerrar_sesion.php">Cerrar Sesión</a>
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
										<a href="../../Estudiantes/Tecnicatura/ABM_estudiante/nuevo_estudiante.php">
											<span class="sub-item">Nuevo Estudiante</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li >
										<a href="../../Estudiantes/FP/nuevo_estudianteFP.php">
											<span class="sub-item">Nuevo Estudiante FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="../../Estudiantes/Tecnicatura/lista_estudiantes.php">
											<span class="sub-item">Lista Estudiantes</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../../Estudiantes/Tecnicatura/lista_estudiantes_2025.php">
											<span class="sub-item">Lista Estudiantes 2025</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../../Estudiantes/FP/lista_estudianteFP.php">
											<span class="sub-item">Lista Estudiantes FP</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="../../Estudiantes/Tecnicatura/Informes/informe_asistencia_tecnicaturas.php">
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
										<a href="../../Estudiantes/Tecnicatura/Informes/informe_lista_estudiantes.php">
											<span class="sub-item">Imprimir Lista de Estudiantes Técnicaturas</span>
										</a>
									</li>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
									<li>
										<a href="../../Estudiantes/FP/informesFP/informe_lista_estudiantesFP.php">
											<span class="sub-item">Imprimir Lista de Estudiantes FP</span>
										</a>
									</li>
									<?php endif; ?>
									<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
									<li>
										<a href="../../Estudiantes/Tecnicatura/Falta_justificada/falta_justificada.php">
											<span class="sub-item">Justificar Falta</span>
										</a>
									</li>
									<?php endif; ?>
									<li>
										<a href="../../Estudiantes/Tecnicatura/Retirados/estudiantes_retirados.php">
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
                            <a href="../../Estudiantes/FP/ver_FPS.php">
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
                        <li c>
                            <a href="../../Estudiantes/FP/ver_asistenciaFPS.php">
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
			<?php if ($rolUsuario == '4' || $rolUsuario == '1'|| $rolUsuario == '5'): ?>
			<li class="nav-item active submenu">
                <a data-toggle="collapse" href="#newMenu">
                    <i class="fas fa-book"></i>
                    <p>Utilidades</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse show" id="newMenu">
                    <ul class="nav nav-collapse">
					<li class="active">
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

<div class="contenido">

    

    <?php
    $contador = 1; // Inicializa el contador
    session_start();
    $idProfesor = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0; // ID del profesor desde la sesión
    $idCarrera = isset($_GET['comision']) ? (int)$_GET['comision'] : 0;
    $idMateria = isset($_GET['materia']) ? (int)$_GET['materia'] : 0;

  // Consulta para obtener los alumnos, sus notas y la nota final y condición
$sql1 = "
    SELECT a.legajo, a.apellido_alumno, a.nombre_alumno, 
           n.idnotas, n.numero_evaluacion, n.nota, n.cuatrimestre, n.tipo_evaluacion,
           n.nota_final, n.condicion
    FROM inscripcion_asignatura ia
    INNER JOIN alumno a ON ia.alumno_legajo = a.legajo
    LEFT JOIN notas n ON a.legajo = n.alumno_legajo AND n.materias_idMaterias = $idMateria
    WHERE ia.carreras_idCarrera = $idCarrera AND a.estado = 1
    ORDER BY a.apellido_alumno
";

$result = mysqli_query($conexion, $sql1);
$alumnos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $legajo = $row['legajo'];
    $cuatrimestre = $row['cuatrimestre'];
    $tipoEvaluacion = $row['tipo_evaluacion'];
    $nota = $row['nota'];
    $idnotas = $row['idnotas'];

    // Si el alumno no está en el arreglo, inicializamos su estructura
    if (!isset($alumnos[$legajo])) {
        $alumnos[$legajo] = [
            'apellido' => $row['apellido_alumno'],
            'nombre' => $row['nombre_alumno'],
            'primer_cuatri' => ['tps' => [], 'parcial' => '', 'recuperatorio' => '', 'promedio' => 0],
            'segundo_cuatri' => ['tps' => [], 'parcial' => '', 'recuperatorio' => '', 'promedio' => 0],
            'nota_final' => null,
            'condicion' => null
        ];
    }

    // Solo asignar nota_final y condición si no son NULL
    if (!is_null($row['nota_final'])) {
        $alumnos[$legajo]['nota_final'] = $row['nota_final'];
    }
    if (!is_null($row['condicion'])) {
        $alumnos[$legajo]['condicion'] = $row['condicion'];
    }

    // Asignación de las notas por cuatrimestre y tipo de evaluación
    if ($cuatrimestre == 1) {
        if ($tipoEvaluacion == 2) {
            $alumnos[$legajo]['primer_cuatri']['parcial'] = $nota;
        } elseif ($tipoEvaluacion == 3) {
            $alumnos[$legajo]['primer_cuatri']['recuperatorio'] = $nota;
        } else {
            $alumnos[$legajo]['primer_cuatri']['tps'][] = ['nota' => $nota, 'idnotas' => $idnotas];
        }
    } elseif ($cuatrimestre == 2) {
        if ($tipoEvaluacion == 2) {
            $alumnos[$legajo]['segundo_cuatri']['parcial'] = $nota;
        } elseif ($tipoEvaluacion == 3) {
            $alumnos[$legajo]['segundo_cuatri']['recuperatorio'] = $nota;
        } else {
            $alumnos[$legajo]['segundo_cuatri']['tps'][] = ['nota' => $nota, 'idnotas' => $idnotas];
        }
    }
}


    
   
    // Consulta para obtener el porcentaje de asistencia y ausencias ajustadas de cada alumno
$sql_asistencias = "
    SELECT 
        a.inscripcion_asignatura_alumno_legajo AS legajo,
        SUM(CASE WHEN a.1_Horario = 'Presente' OR a.2_Horario = 'Presente' THEN 1 ELSE 0 END) AS asistencias,
        SUM(CASE WHEN a.1_Horario = 'Ausente' OR a.2_Horario = 'Ausente' THEN 1 ELSE 0 END) AS ausencias,
        COUNT(*) AS total_clases,
        (SELECT COUNT(*) 
         FROM alumnos_justificados aj 
         WHERE aj.inscripcion_asignatura_alumno_legajo = a.inscripcion_asignatura_alumno_legajo
           AND (aj.materias_idMaterias = $idMateria OR aj.materias_idMaterias1 = $idMateria)
        ) AS justificaciones
    FROM 
        asistencia a
    WHERE 
        a.inscripcion_asignatura_alumno_legajo IN (SELECT alumno_legajo FROM inscripcion_asignatura WHERE carreras_idCarrera = $idCarrera)
        AND a.materias_idMaterias = $idMateria
    GROUP BY 
        a.inscripcion_asignatura_alumno_legajo";

$query_asistencias = mysqli_query($conexion, $sql_asistencias);

// Arreglo para almacenar el porcentaje de asistencia por alumno
$asistencias_alumnos = [];

while ($row_asistencias = mysqli_fetch_assoc($query_asistencias)) {
    $legajo = $row_asistencias['legajo'];
    $total_clases = $row_asistencias['total_clases'];
    $justificaciones = $row_asistencias['justificaciones'];
    
    // Ajustar ausencias y asistencias en función de las justificaciones
    $ajuste_ausencias = floor($justificaciones / 2); // Cada 2 justificaciones suman 1 ausencia
    $ausencias_ajustadas = $row_asistencias['ausencias'] + $ajuste_ausencias; // Se suman las justificaciones como ausencias
    $asistencias_ajustadas = $row_asistencias['asistencias'] - $ajuste_ausencias; // Se restan del presente

    // Calcular porcentajes ajustados
    $porcentaje_asistencia = ($asistencias_ajustadas / $total_clases) * 100;
    $porcentaje_ausencia = ($ausencias_ajustadas / $total_clases) * 100;

    // Almacenar los datos en el arreglo
    $asistencias_alumnos[$legajo] = [
        'porcentaje_asistencia' => $porcentaje_asistencia,
        'porcentaje_ausencia' => $porcentaje_ausencia,
        'justificaciones' => $justificaciones
    ];
}

    ?>
    <?php
     // Consulta para obtener el nombre de la carrera y de la materia
    $sql = "SELECT c.nombre_carrera AS carreraNombre, m.Nombre AS materiaNombre 
            FROM materias m 
            INNER JOIN carreras c ON m.carreras_idCarrera = c.idCarrera 
            WHERE m.carreras_idCarrera = $idCarrera AND m.idMaterias = $idMateria";
    
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        // Obtener el nombre de la carrera y la materia
        $fila = $resultado->fetch_assoc();
        $nombreCarrera = $fila['carreraNombre'];
        $nombreMateria = $fila['materiaNombre'];
        echo "<h1>Carrera: $nombreCarrera - Materia: $nombreMateria</h1>";
    } else {
        echo "<h1>Carrera o Materia no encontrada</h1>";
    }

    ?>
<h2>Control de Desempeño Académico</h2>
<p class="indicaciones">

    <b>Instrucciones para el Uso de los Botones y Cálculo del Promedio</b><br><br>
    
    - <b>Botón "+"</b>: Haz clic en el botón con el signo "+" para <b>añadir un nuevo Trabajo Práctico (TP)</b>. Esto te permitirá ingresar una nueva nota para dicho TP.<br><br>
    
    - <b>Botón "-"</b>: Haz clic en el botón con el signo "-" para <b>eliminar el último Trabajo Práctico (TP)</b> añadido. <b>Nota importante:</b> Una vez que hayas añadido un TP y guardado los cambios, <b>ya no será posible eliminar ese TP</b>. Por favor, asegúrate de ingresar y revisar correctamente la nota antes de guardar.<br><br>
    
    - <b>Cálculo del Promedio</b>: El promedio que aparece en la tabla <b>corresponde únicamente a las notas de los Trabajos Prácticos (TPs)</b>. <b>Las notas de los parciales no están incluidas en este promedio</b>. Los parciales y sus respectivas notas se consideran de manera independiente al cálculo del promedio de los TPs.
</p>
<?php
foreach ($alumnos as $legajo => $alumno) {
    // Asegúrate de que los TPs del primer cuatrimestre estén ordenados por 'numero_evaluacion'
    usort($alumnos[$legajo]['primer_cuatri']['tps'], function ($a, $b) {
        return $a['idnotas'] <=> $b['idnotas'];
    });
    // Asegúrate de que los TPs del segundo cuatrimestre estén ordenados por 'numero_evaluacion'
    usort($alumnos[$legajo]['segundo_cuatri']['tps'], function ($a, $b) {
        return $a['idnotas'] <=> $b['idnotas'];
    });
} ?>
    <form action="guardar_notas.php" method="POST">
        
        <table>
        <thead>
    <tr>
        <th rowspan="2" class="col-apellido">#</th>
        <th rowspan="2" class="col-apellido">Apellido</th>
        <th rowspan="2" class="col-nombre">Nombre</th>

        <!-- Primer Cuatrimestre -->
        <th colspan="4" class="cuatrimestre-header tps-columna">Primer Cuatrimestre</th>

        <!-- Segundo Cuatrimestre -->
        <th colspan="4" class="cuatrimestre-header tps-columna">Segundo Cuatrimestre</th>

        <!-- Asistencia -->
        <th colspan="2" class="col-asistencia">Asistencia (%)</th>

        <!-- Nota Final y Condición -->
        <th rowspan="2" class="col-nota-final">Nota Final</th>
        <th rowspan="2" class="col-condicion">Condición</th>
    </tr>

    <tr>
        <!-- Botones dentro de TPs del Primer Cuatrimestre -->
        <th class="col-tp-primer">
            <button type="button" id="btnEliminarTP1Tabla">-</button> TPs <button type="button" id="btnAgregarTP1Tabla">+</button>
        </th>
        <th class="col-parcial-primer">Parcial</th>
        <th class="col-recuperatorio-primer">Recuperatorio</th>
        <th class="col-promedio-primer">Promedio</th>

        <!-- Botones dentro de TPs del Segundo Cuatrimestre -->
        <th class="col-tp-segundo">
            <button type="button" id="btnEliminarTP2Tabla">-</button> TPs <button type="button" id="btnAgregarTP2Tabla">+</button>
        </th>
        <th class="col-parcial-segundo">Parcial</th>
        <th class="col-recuperatorio-segundo">Recuperatorio</th>
        <th class="col-promedio-segundo">Promedio</th>

        <!-- Presente y Ausente -->
        <th class="col-presente">Presente</th>
        <th class="col-ausente">Ausente</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($alumnos as $legajo => $alumno): ?>
    <tr>
         <td class="col-contador"><?php echo $contador++; ?></td>
        <td class="col-apellido"><?php echo htmlspecialchars($alumno['apellido']); ?></td>
        <td class="col-nombre"><?php echo htmlspecialchars($alumno['nombre']); ?></td>

       <!-- TPs Primer Cuatrimestre -->
                    <td class="col-tp-segundo" id="tp-primer-<?php echo $legajo; ?>">
                        <?php foreach ($alumno['primer_cuatri']['tps'] as $index => $tp): ?>
                            TP<?php echo $index + 1; ?>: 
                            <input type="hidden" name="idnotas_primer_<?php echo $legajo; ?>[]" value="<?php echo htmlspecialchars($tp['idnotas']); ?>">
                            <input type="number" name="tp_primer_<?php echo $legajo; ?>[]" value="<?php echo htmlspecialchars($tp['nota']); ?>" step="0.1" min="0" max="10" class="tp-primer-cuatrimestre" data-legajo="<?php echo $legajo; ?>">
                            <input type="hidden" name="tipo_evaluacion_primer_<?php echo $legajo; ?>[]" value="1"> <!-- 1 para TP -->
                        <?php endforeach; ?>
                    </td>

        <!-- Parcial Primer Cuatrimestre -->
        <td class="col-parcial-primer">
            <input type="number" name="parcial_primer_<?php echo $legajo; ?>" value="<?php echo isset($alumno['primer_cuatri']['parcial']) ? htmlspecialchars($alumno['primer_cuatri']['parcial']) : 0; ?>" step="0.1" min="0" max="10">
            <input type="hidden" name="tipo_evaluacion_parcial_primer_<?php echo $legajo; ?>" value="2"> <!-- 2 para Parcial -->
        </td>

        <!-- Recuperatorio Primer Cuatrimestre -->
        <td class="col-recuperatorio-primer">
            <input type="number" name="recuperatorio_primer_<?php echo $legajo; ?>" value="<?php echo isset($alumno['primer_cuatri']['recuperatorio']) ? htmlspecialchars($alumno['primer_cuatri']['recuperatorio']) : ''; ?>" step="0.1" min="0" max="10">
            <input type="hidden" name="tipo_evaluacion_recuperatorio_primer_<?php echo $legajo; ?>" value="3"> <!-- 3 para Recuperatorio -->
        </td>

        <!-- Promedio Primer Cuatrimestre -->
        <td class="col-promedio-primer">
            <input type="number" name="promedio_primer_<?php echo $legajo; ?>" value="0" min="0" max="10" readonly class="promedio-primer-cuatrimestre" id="promedio-primer-<?php echo $legajo; ?>">
        </td>

        <!-- TPs Segundo Cuatrimestre -->
        <td class="col-tp-segundo" id="tp-segundo-<?php echo $legajo; ?>">
            <?php foreach ($alumno['segundo_cuatri']['tps'] as $index => $tp): ?>
                TP<?php echo $index + 1; ?>: 
                <input type="hidden" name="idnotas_segundo_<?php echo $legajo; ?>[]" value="<?php echo htmlspecialchars($tp['idnotas']); ?>">
                <input type="number" name="tp_segundo_<?php echo $legajo; ?>[]" value="<?php echo htmlspecialchars($tp['nota']); ?>" step="0.1" min="0" max="10" class="tp-segundo-cuatrimestre" data-legajo="<?php echo $legajo; ?>">
                <input type="hidden" name="tipo_evaluacion_segundo_<?php echo $legajo; ?>[]" value="1"> <!-- 1 para TP -->
            <?php endforeach; ?>
        </td>

        <!-- Parcial Segundo Cuatrimestre -->
        <td class="col-parcial-segundo">
            <input type="number" name="parcial_segundo_<?php echo $legajo; ?>" value="<?php echo htmlspecialchars($alumno['segundo_cuatri']['parcial']); ?>"  min="0" max="10" step="0.1">
            <input type="hidden" name="tipo_evaluacion_parcial_segundo_<?php echo $legajo; ?>" value="2"> <!-- 2 para Parcial -->
        </td>

        <!-- Recuperatorio Segundo Cuatrimestre -->
        <td class="col-recuperatorio-segundo">
            <input type="number" name="recuperatorio_segundo_<?php echo $legajo; ?>" value="<?php echo htmlspecialchars($alumno['segundo_cuatri']['recuperatorio']); ?>"  min="0" max="10" step="0.1">
            <input type="hidden" name="tipo_evaluacion_recuperatorio_segundo_<?php echo $legajo; ?>" value="3"> <!-- 3 para Recuperatorio -->
        </td>

        <!-- Promedio Segundo Cuatrimestre -->
        <td class="col-promedio-segundo">
            <input type="number" name="promedio_segundo_<?php echo $legajo; ?>" value="0" min="0" max="10" readonly class="promedio-segundo-cuatrimestre" id="promedio-segundo-<?php echo $legajo; ?>">
        </td>

        <!-- Columna Asistencia Presente -->
        <td class="col-presente">
            <?php 
            if (isset($asistencias_alumnos[$legajo])) {
                echo number_format($asistencias_alumnos[$legajo]['porcentaje_asistencia'], 2) . '%';
            } else {
                echo 'No disponible';
            }
            ?>
        </td>

        <!-- Columna Asistencia Ausente -->
        <td class="col-ausente">
            <?php 
            if (isset($asistencias_alumnos[$legajo])) {
                echo number_format($asistencias_alumnos[$legajo]['porcentaje_ausencia'], 2) . '%';
            } else {
                echo 'No disponible';
            }
            ?>
        </td>

       <!-- Nota Final -->
<td class="col-nota-final">
    <input type="number" name="nota_final_<?php echo $legajo; ?>" 
           id="nota_final_<?php echo $legajo; ?>"
           value="<?php echo isset($alumnos[$legajo]['nota_final']) ? htmlspecialchars($alumnos[$legajo]['nota_final']) : ''; ?>" 
           min="0" max="10" step="0.1" oninput="actualizarCondicion('<?php echo $legajo; ?>')">
</td>

<!-- Condición -->
<td class="col-condicion">
    <select name="condicion_<?php echo $legajo; ?>" id="condicion_<?php echo $legajo; ?>">
        <option value="" hidden>Seleccionar</option>
        <option value="Libre" <?php echo (isset($alumnos[$legajo]['condicion']) && $alumnos[$legajo]['condicion'] == 'Libre') ? 'selected' : ''; ?>>Libre</option>
        <option value="Regular" <?php echo (isset($alumnos[$legajo]['condicion']) && $alumnos[$legajo]['condicion'] == 'Regular') ? 'selected' : ''; ?>>Regular</option>
        <option value="Promocion" <?php echo (isset($alumnos[$legajo]['condicion']) && $alumnos[$legajo]['condicion'] == 'Promocion') ? 'selected' : ''; ?>>Promoción</option>
        <option value="Abandono" <?php echo (isset($alumnos[$legajo]['condicion']) && $alumnos[$legajo]['condicion'] == 'Abandono') ? 'selected' : ''; ?>>Abandonó</option>
        <?php if (in_array($idMateria, ['35','98','121','227',251,'273','291','310','336','417','428','436','443','451','460','511','524','533','541','548','555','99','120','226','250','272','309','311','312','337','523','525','526','534','535','536','542','543','549','556','557','148'])) : ?>
            <option value="Desaprobo" <?php echo (isset($alumnos[$legajo]['condicion']) && $alumnos[$legajo]['condicion'] == 'Desaprobo') ? 'selected' : ''; ?>>Desaprobó</option>
        <?php endif; ?>
    </select>
</td>

    </tr>
    <?php endforeach; ?>
</tbody>
        </table>

        <!-- Enviar los IDs de carrera, materia y profesor para procesarlos en PHP -->
        <input type="hidden" name="idCarrera" value="<?php echo $idCarrera; ?>">
        <input type="hidden" name="idMateria" value="<?php echo $idMateria; ?>">
        <input type="hidden" name="idProfesor" value="<?php echo $idProfesor; ?>">
        <br><br><br>
        <div class="fixed-buttons">
    <a href="PDF_general_notas.php?idCarrera=<?php echo $idCarrera; ?>&idMateria=<?php echo $idMateria; ?>" class="btn">Descargar Planilla de Notas</a>
    <button type="submit" class="btn">Guardar Calificaciones</button>
</div>
    </form>
</div>





</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

let tpsEliminados = {}; // Objeto para almacenar los TPs eliminados

// Función para agregar TP al cuatrimestre correcto
function agregarTP(cuatrimestre, legajo) {
    const tpColumn = document.getElementById(`tp-${cuatrimestre}-${legajo}`);
    const newTP = document.createElement('input');
    newTP.type = 'number';
    newTP.min = 0;
    newTP.max = 10;
    newTP.step = 0.1; // Permite decimales de un solo dígito
    newTP.name = `tp_${cuatrimestre}_${legajo}[]`; // Nombre único por legajo
    newTP.classList.add(`tp-${cuatrimestre}-cuatrimestre`);
    newTP.setAttribute('data-legajo', legajo);
    newTP.addEventListener('input', function () {
        recalcularPromedio(cuatrimestre, legajo);
    });
    tpColumn.appendChild(newTP);
}

// Eliminar el último TP agregado
function eliminarTP(cuatrimestre, legajo) {
    const tpColumn = document.getElementById(`tp-${cuatrimestre}-${legajo}`);
    const tpInputs = tpColumn.querySelectorAll(`input.tp-${cuatrimestre}-cuatrimestre`);

    if (tpInputs.length > 0) {
        const inputEliminado = tpInputs[tpInputs.length - 1];

        // Agregar el idnotas del TP eliminado a un array de eliminados
        const idNotas = inputEliminado.getAttribute('data-idnotas');
        if (idNotas) {
            if (!tpsEliminados[legajo]) {
                tpsEliminados[legajo] = [];
            }
            tpsEliminados[legajo].push(idNotas);
        }

        // Eliminar el input visualmente
        tpColumn.removeChild(inputEliminado);

        // Recalcular el promedio después de eliminar
        recalcularPromedio(cuatrimestre, legajo);
    }
}

// Recalcular el promedio en tiempo real
function recalcularPromedio(cuatrimestre, legajo) {
    let sumaNotas = 0;
    let totalNotas = 0;

    const tpInputs = document.querySelectorAll(`.tp-${cuatrimestre}-cuatrimestre[data-legajo='${legajo}']`);
    tpInputs.forEach(function (input) {
        const valor = parseFloat(input.value);
        if (!isNaN(valor)) {
            sumaNotas += valor;
            totalNotas++;
        }
    });

    const promedio = totalNotas > 0 ? (sumaNotas / totalNotas).toFixed(2) : 0;
    document.getElementById(`promedio-${cuatrimestre}-${legajo}`).value = promedio;
}

// Recalcular los promedios desde las notas de la base de datos al cargar la página
function inicializarPromedios() {
    document.querySelectorAll('.tp-primer-cuatrimestre, .tp-segundo-cuatrimestre').forEach(function (input) {
        const cuatrimestre = input.classList.contains('tp-primer-cuatrimestre') ? 'primer' : 'segundo';
        const legajo = input.getAttribute('data-legajo');
        recalcularPromedio(cuatrimestre, legajo);
    });
}

// Guardar los TPs eliminados en un campo oculto antes de enviar el formulario
document.querySelector('form').addEventListener('submit', function () {
    // Crear un input hidden para los TPs eliminados
    const inputEliminados = document.createElement('input');
    inputEliminados.type = 'hidden';
    inputEliminados.name = 'tps_eliminados';
    inputEliminados.value = JSON.stringify(tpsEliminados); // Convertir el objeto en string JSON

    // Agregarlo al formulario
    this.appendChild(inputEliminados);
});

// Asignar eventos para los botones en el encabezado de la tabla
document.getElementById('btnAgregarTP1Tabla').addEventListener('click', function () {
    document.querySelectorAll('[id^=tp-primer-]').forEach(function (col) {
        const legajo = col.getAttribute('id').replace('tp-primer-', '');
        agregarTP('primer', legajo);
    });
});

document.getElementById('btnAgregarTP2Tabla').addEventListener('click', function () {
    document.querySelectorAll('[id^=tp-segundo-]').forEach(function (col) {
        const legajo = col.getAttribute('id').replace('tp-segundo-', '');
        agregarTP('segundo', legajo);
    });
});

document.getElementById('btnEliminarTP1Tabla').addEventListener('click', function () {
    document.querySelectorAll('[id^=tp-primer-]').forEach(function (col) {
        const legajo = col.getAttribute('id').replace('tp-primer-', '');
        eliminarTP('primer', legajo);
    });
});

document.getElementById('btnEliminarTP2Tabla').addEventListener('click', function () {
    document.querySelectorAll('[id^=tp-segundo-]').forEach(function (col) {
        const legajo = col.getAttribute('id').replace('tp-segundo-', '');
        eliminarTP('segundo', legajo);
    });
});

// Inicializar promedios para todas las notas al cargar la página
inicializarPromedios();

// Recalcular el promedio en tiempo real para todos los inputs de TP existentes
document.querySelectorAll('.tp-primer-cuatrimestre, .tp-segundo-cuatrimestre').forEach(function (input) {
    input.addEventListener('input', function () {
        const cuatrimestre = input.classList.contains('tp-primer-cuatrimestre') ? 'primer' : 'segundo';
        const legajo = input.getAttribute('data-legajo');
        recalcularPromedio(cuatrimestre, legajo);
    });
});
});

</script>

<script>
function actualizarCondicion(legajo) {
    const notaFinalInput = document.getElementById(`nota_final_${legajo}`);
    const notaFinal = parseFloat(notaFinalInput.value) || 0;
    const selectCondicion = document.getElementById(`condicion_${legajo}`);
    
    // Habilitar todas las opciones antes de aplicar la restricción
    Array.from(selectCondicion.options).forEach(option => option.disabled = false);

    if (notaFinal < 6.00) {
        // Solo permitir "Libre"
        selectCondicion.querySelector('option[value="Regular"]').disabled = true;
        selectCondicion.querySelector('option[value="Promocion"]').disabled = true;
        selectCondicion.value = "Libre";
    } else if (notaFinal < 8.00) {
        // Solo permitir "Libre" y "Regular"
        selectCondicion.querySelector('option[value="Promocion"]').disabled = true;
        if (selectCondicion.value === "Promocion" || selectCondicion.value === "") {
            selectCondicion.value = "Regular";
        }
    } else {
        // Permitir todas las opciones si la nota es 8 o superior
        selectCondicion.querySelector('option[value="Libre"]').disabled = false;
        selectCondicion.querySelector('option[value="Regular"]').disabled = false;
        selectCondicion.querySelector('option[value="Promocion"]').disabled = false;
    }
}
</script>





   

<!--   Core JS Files   -->
<script src="../../assets/js/core/jquery.3.2.1.min.js"></script>

<script src="../../assets/js/core/bootstrap.min.js"></script>


<!-- jQuery UI -->
<script src="../../assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>


<!-- jQuery Scrollbar -->
<script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Azzara JS -->
<script src="../../assets/js/ready.min.js"></script>








<style>
    
    .contenido {
        position: absolute;
        top: 55px;
        left: 270px;
        background-color: #ffffff;
        background-image: url(../../assets/img/fondo.png);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: local;
        padding: 20px;
        min-height: 100%;
        background-attachment: local;
    }

    /* Estilo para los botones de "+" y "-" */
    .titulo-boton {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .titulo-boton button {
        background-color: #ff6f61; /* Ajusta el color si lo deseas */
        border: none;
        color: white;
        padding: 0.5em 1em;
        font-size: 1em;
        cursor: pointer;
        border-radius: 5px;
        margin: 0 0.5em;
    }

    .titulo-boton button:hover {
        background-color: #ff4a3a; /* Cambia el color al hacer hover */
    }

    /* Estilo para la tabla */
    table {
        width: 100%;
    border-collapse: collapse;
    
    }
    thead th,
thead .cuatrimestre-header,
thead .col-asistencia {
    position: sticky;
    top: 55px;
    background-color: #ff6f61; /* El color de fondo que ya tienes */
    z-index: 10;
}
    th {
        
        text-align: center;
        border: 1px solid #ccc;
    }
    td {
        padding-left: 0.5em;
        padding-right: 0.5em;
        text-align: center;
        border: 1px solid #ccc;
    }

    th {
        background-color: #ff6f61;
        color: white;
    }

    /* Ajustar ancho de la columna de TPs */
   

    /* Botones "+" y "-" dentro de TPs */
    th button {
        padding: 0.3em 0.6em;
        font-size: 1.2em;
        border-radius: 50%;
        margin: 0.2em;
        cursor: pointer;    
      
    }

    /* Estilo para inputs */
    input[type="number"] {
        width: 100%;
        padding: 0.5em;
        font-size: 1em;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    /* Estilo para selects */
    select {
        padding: 0.5em;
        border-radius: 5px;
        font-size: 1em;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
    }

  
/* Estilo para la columna TPs */

/* Estilo para la columna Nota Final */

/* Deshabilitar las flechas de los inputs de tipo number en Chrome, Safari, Edge, Opera */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Deshabilitar las flechas en Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}


.col-parcial-primer{
    width:5%;
}
.col-tp-primer{
    width: 7%;
    font-size: 0.8em;
}
.col-parcial-segundo{
    width:5%;
}
.col-tp-segundo{
    width: 7%;
    font-size: 0.8em;
}
.indicaciones {
    background-color: #fff8b0; /* Fondo amarillo */
    padding: 10px;
    border-radius: 5px;
    font-size: 16px;
    color: #333;
    margin-bottom: 20px;
}
.fixed-buttons {
    position: fixed;
    bottom: 10px;
    right: 10px;
    display: flex;
    gap: 10px; /* Espacio entre los botones */
}

.fixed-buttons .btn {
    background-color: #ff6f61;
    color: white;
    padding: 1em 2em;
    border: none;
    font-size: 1em;
    cursor: pointer;
    border-radius: 5px;
    text-decoration: none; /* Para que los enlaces no tengan subrayado */
    display: inline-block;
    text-align: center;
}

.fixed-buttons .btn:hover {
    background-color: #ff4a3a;
}



</style>

</body>
</html>

