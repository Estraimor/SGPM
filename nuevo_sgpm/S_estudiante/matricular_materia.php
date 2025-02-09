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
				
				<a href="#" class="logo">
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
						<li class="nav-item ">
							<a href="index_estudiante.php">
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
                    <li class="nav-item active">
    <a data-toggle="collapse" href="#menuMatriculacionMaterias">
        <i class="fas fa-pen-square"></i>
        <p> Materias</p>
        <span class="caret"></span>
    </a>
    <div class="collapse" id="menuMatriculacionMaterias">
        <ul class="nav nav-collapse">
            <li>
                <a href="./matricular_materia.php">
                    <span class="sub-item">Matriculacion de Materias</span>
                </a>
            </li>
           
        </ul>
    </div>
</li>

                </div>
            </li>
            
						
		</div>
		<!-- End Sidebar -->
	</div>
	
</div>

<?php
// Iniciar sesión y conectar a la base de datos

// Obtener datos del estudiante desde la sesión
$alumno_legajo = $_SESSION['id'];
$idCarrera = $_SESSION["idCarrera"];
$idCurso = $_SESSION["idCurso"];
$idComision = $_SESSION["idComision"];

// 1. Obtener el año de inscripción en primer año
$queryInscripcion = "
    SELECT MIN(año_matriculacion) as año_ingreso 
    FROM matriculacion_materias 
    WHERE alumno_legajo = ? 
    AND materias_idMaterias IN (SELECT idMaterias FROM materias WHERE cursos_idCursos = 1)
";

$stmt = $conexion->prepare($queryInscripcion);
$stmt->bind_param("i", $alumno_legajo);
$stmt->execute();
$resultInscripcion = $stmt->get_result();
$row = $resultInscripcion->fetch_assoc();
$añoIngreso = $row['año_ingreso'] ?? date("Y"); 
$stmt->close();

// Determinar el año actual de cursada
$añoActual = date("Y") - $añoIngreso + 1;

// 2. Obtener materias organizadas por nivel
$queryMaterias = "
    SELECT DISTINCT m.idMaterias, m.Nombre, m.cursos_idCursos 
    FROM materias m
    WHERE m.carreras_idCarrera = ? 
    AND m.comisiones_idComisiones = ?
";

$stmt = $conexion->prepare($queryMaterias);
$stmt->bind_param("ii", $idCarrera, $idComision);
$stmt->execute();
$resultMaterias = $stmt->get_result();

$materiasPorNivel = [];
while ($row = $resultMaterias->fetch_assoc()) {
    $nivel = $row['cursos_idCursos'];
    $materiasPorNivel[$nivel][$row['idMaterias']] = [
        'nombre' => $row['Nombre'],
        'nivel' => $nivel
    ];
}
$stmt->close();

// 3. Obtener condición de cada materia desde `notas`
$queryCondiciones = "
    SELECT DISTINCT n.materias_idMaterias, n.condicion
    FROM notas n
    WHERE n.alumno_legajo = ?
    ORDER BY n.fecha DESC
";

$stmt = $conexion->prepare($queryCondiciones);
$stmt->bind_param("i", $alumno_legajo);
$stmt->execute();
$resultCondiciones = $stmt->get_result();

$condicionesMaterias = [];
while ($row = $resultCondiciones->fetch_assoc()) {
    if (!isset($condicionesMaterias[$row['materias_idMaterias']])) {
        $condicionesMaterias[$row['materias_idMaterias']] = $row['condicion'];
    }
}
$stmt->close();

// 4. Obtener materias aprobadas y verificar si faltan exámenes finales
$queryAprobadas = "
    SELECT DISTINCT materias_idMaterias, nota FROM nota_examen_final 
    WHERE alumno_legajo = ?
";

$stmt = $conexion->prepare($queryAprobadas);
$stmt->bind_param("i", $alumno_legajo);
$stmt->execute();
$resultAprobadas = $stmt->get_result();

$materiasAprobadas = [];
$materiasFaltanExamen = [];
while ($row = $resultAprobadas->fetch_assoc()) {
    if ($row['nota'] >= 6) {
        $materiasAprobadas[$row['materias_idMaterias']] = true;
    } else {
        $materiasFaltanExamen[$row['materias_idMaterias']] = true;
    }
}
$stmt->close();

// 5. Verificar correlatividades
$estadoMaterias = [];
$correlatividadesPendientes = [];

$queryCorrelatividad = "
    SELECT c.materias_idMaterias, c.materias_idMaterias1, c.tipo_correlatividad_idtipo_correlatividad 
    FROM correlatividades c
";

$resultCorrelatividad = $conexion->query($queryCorrelatividad);

while ($row = $resultCorrelatividad->fetch_assoc()) {
    $materia = $row['materias_idMaterias'];
    $correlativa = $row['materias_idMaterias1'];
    $tipoCorrelatividad = $row['tipo_correlatividad_idtipo_correlatividad'];

    if (!isset($estadoMaterias[$materia])) {
        $estadoMaterias[$materia] = 'habilitada';
    }

    if ($tipoCorrelatividad == 1) { // Regularización requerida
        if (!isset($condicionesMaterias[$correlativa]) || $condicionesMaterias[$correlativa] !== 'Regular') {
            $estadoMaterias[$materia] = 'bloqueada';
            $correlatividadesPendientes[$materia][] = "Debe regularizar: " . $correlativa;
        }
    }

    if ($tipoCorrelatividad == 2 && !isset($materiasAprobadas[$correlativa])) { // Aprobación requerida
        $estadoMaterias[$materia] = 'bloqueada';
        $correlatividadesPendientes[$materia][] = "Debe aprobar: " . $correlativa;
    }
}

// 6. Obtener materias en las que ya está matriculado
$queryMatriculadas = "
    SELECT DISTINCT materias_idMaterias FROM matriculacion_materias WHERE alumno_legajo = ?
";

$stmt = $conexion->prepare($queryMatriculadas);
$stmt->bind_param("i", $alumno_legajo);
$stmt->execute();
$resultMatriculadas = $stmt->get_result();

$materiasMatriculadas = [];
while ($row = $resultMatriculadas->fetch_assoc()) {
    $materiasMatriculadas[$row['materias_idMaterias']] = true;
}
$stmt->close();

// 7. Generar las tablas por nivel
?>
<div class="contenido">
    <h3>Tu año actual de cursada: Año <?= $añoActual ?> (Curso: <?= $idCurso ?>)</h3>
    <?php foreach ($materiasPorNivel as $nivel => $materias): ?>
        <h3 class="mt-4">Año <?= $nivel ?></h3>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Materias</th>
                    <th>Nivel</th>
                    <th>Condición</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $idMateria => $datosMateria): ?>
                    <?php
                    $claseFila = '';
                    $condicion = $condicionesMaterias[$idMateria] ?? 'No cursada';

                    if (isset($materiasAprobadas[$idMateria])) {
                        $claseFila = 'table-success';
                        $condicion = 'Aprobada';
                    } elseif (isset($materiasFaltanExamen[$idMateria])) {
                        $claseFila = 'table-warning';
                        $condicion = 'Falta examen final';
                    } elseif ($nivel > $añoActual) {
                        $claseFila = 'table-danger';
                        $condicion = 'No permitido';
                    } elseif (isset($estadoMaterias[$idMateria]) && $estadoMaterias[$idMateria] == 'bloqueada') {
                        $claseFila = 'table-danger';
                    }
                    ?>
                    <tr class="<?= $claseFila ?>">
                        <td><?= htmlspecialchars($datosMateria['nombre']) ?></td>
                        <td><?= $datosMateria['nivel'] ?></td>
                        <td><?= $condicion ?></td>
                        <td>
                            <?php if ($condicion == 'Aprobada'): ?>
                                <span class="badge badge-success">Aprobada</span>
                            <?php elseif ($condicion == 'Falta examen final'): ?>
                                <span class="badge badge-warning">Falta examen final</span>
                            <?php else: ?>
                                <button class="btn btn-primary btn-sm">Inscribirse</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>
















	<style>
		.contenido {
        padding: 20px;
    }
    table {
        width: 100%;
        max-width: 500px;
        margin: 20px 0; /* Elimina el auto para centrar y ajusta los márgenes */
        border-collapse: collapse;
    }
    table, th, td {
        border: 1px solid #ddd;
    }
    th, td {
        padding: 12px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
        color: #333;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
    .error-message {
        color: red;
        display: none;
        font-size: 14px;
        margin-top: 5px;
    }
	.button_est {
        background-color: #ff4b5c;
        color: white;
        border: none;
        padding: 5px 10px; /* Reducción del tamaño del botón */
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px; /* Tamaño de fuente más pequeño */
        transition: background-color 0.3s ease;
        width: auto; /* Ajuste automático del ancho del botón */
    }
    .button_est:hover {
        background-color: #e04350;
    }
    




    /* Contenedor de la tabla */
.table-box {
    max-width: 50%;
    padding: 20px;
    border-radius: 8px;
    background-color: #ffffff05;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.titulo-unidad {
    font-size: 1.5em;
    font-weight: bold;
    color: #f3545d;
    text-align: center;
    margin-bottom: 20px;
}

/* Estilos para la tabla */
table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    color: #333333;
}
.button-dar-baja {
  background-color: #f3545d; /* Color de fondo principal */
  color: #ffffff; /* Texto en blanco */
  border: none; /* Sin bordes */
  border-radius: 6px; /* Bordes redondeados */
  padding: 8px 16px; /* Espaciado interno reducido */
  font-size: 14px; /* Tamaño de fuente más pequeño */
  font-weight: bold; /* Texto en negrita */
  cursor: pointer; /* Cambiar cursor a mano */
  transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease; /* Animaciones suaves */
  box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.1); /* Sombra sutil */
}

.button-dar-baja:hover {
  background-color: #ffffff; /* Fondo cambia a blanco */
  color: #f3545d; /* Texto cambia a rojo */
  box-shadow: 0px 4px 7px rgba(0, 0, 0, 0.15); /* Sombra más pronunciada */
  transform: scale(1.03); /* Aumenta ligeramente de tamaño */
}

.button-dar-baja:active {
  transform: scale(0.95); /* Disminuye ligeramente al hacer clic */
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2); /* Sombra más cercana */
}

table td{
    color: black;
}
table td:hover{
    color: white;
}

table th {
    background-color: #f3545d;
    color: #ffffff;
    font-weight: bold;
    position: sticky;
    top: 0;
}

table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
    color: white;
}

table tbody tr:hover {
    background-color: #f3545d;
    color: #ffffff;
    cursor: pointer;

}

/* Estilos para el botón de inscripción */
.boton-inscripcion {
    background-color: #f3545d;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    font-size: 1em;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.boton-inscripcion:hover {
    background-color: #d3444c;
    
}

.boton-inscripcion:focus {
    outline: none;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

/* Responsividad */
@media (max-width: 768px) {
    table th, table td {
        padding: 8px 10px;
        font-size: 0.9em;
    }

    .titulo-unidad {
        font-size: 1.2em;
    }

    .boton-inscripcion {
        font-size: 0.9em;
        padding: 8px 15px;
    }
    .table-box {
        max-width: 100%;
     }
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

