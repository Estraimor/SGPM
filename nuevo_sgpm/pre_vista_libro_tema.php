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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
    <script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>

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
						<li class="nav-item ">
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
			<?php if ($rolUsuario == '4' || $rolUsuario == '1'|| $rolUsuario == '5'): ?>
			<li class="nav-item active">
                <a data-toggle="collapse" href="#newMenu">
                    <i class="fas fa-book"></i>
                    <p>Utilidades</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse show" id="newMenu">
                    <ul class="nav nav-collapse">
                        <li>
                            <a href="./Administracion/Profesores/pre_parciales.php">
                                <span class="sub-item">Gestión de Notas</span>
                            </a>
                        </li>
						<li class="active">
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
<div class="contenido">
    <?php 
   // Obtener los datos de carrera y materias
$carrera = $_GET['carrera'] ?? $_POST['carrera'] ?? null;
$materias = $_GET['materias'] ?? $_POST['materias'] ?? [];

// Verificar si los datos están vacíos y redirigir a la página anterior si es así
if (empty($carrera) || empty($materias)) {
    // Almacena el mensaje de error en la sesión
    $_SESSION['error_message'] = "Selecciona una carrera y materia.";
    // Redirigir a la página pre_libro.php
    header("Location: pre_libro.php");
    exit(); // Asegura que el script se detenga después de la redirección
}
?>

<?php
// Consulta para obtener el nombre de la carrera
    $sqlCarrera = "SELECT nombre_carrera FROM carreras WHERE idCarrera = $carrera";
    $resultadoCarrera = $conexion->query($sqlCarrera);

    if ($resultadoCarrera->num_rows > 0) {
        $filaCarrera = $resultadoCarrera->fetch_assoc();
        $nombreCarrera = $filaCarrera['nombre_carrera'];
        echo "<h1>Carrera: $nombreCarrera</h1>";
    } else {
        echo "<h1>Carrera no encontrada</h1>";
    }

    // Convertir el array de materias en una lista separada por comas para la consulta SQL
    $materiasIn = implode(",", array_map('intval', $materias)); // Sanitiza valores a enteros

    // Consulta para obtener los nombres de las materias
    if (!empty($materiasIn)) {
        $sqlMaterias = "SELECT Nombre FROM materias WHERE idMaterias IN ($materiasIn)";
        $resultadoMaterias = $conexion->query($sqlMaterias);

        if ($resultadoMaterias->num_rows > 0) {
            $nombresMaterias = [];
            while ($filaMateria = $resultadoMaterias->fetch_assoc()) {
                $nombresMaterias[] = $filaMateria['Nombre'];
            }
            // Convertir el array de nombres de materias en una cadena separada por comas
            $materiasTexto = implode(", ", $nombresMaterias);
            echo "<h2>Unidad Curricular: $materiasTexto</h2>";
        } else {
            echo "<h2>No se encontraron materias.</h2>";
        }
    } else {
        echo "<h2>No se seleccionaron materias.</h2>";
    }
?>
    
    <!-- Botón para abrir el modal -->
    <button id="abrirModal" class="btn-enviar">Cargar Día</button>
    
    <!-- Modal -->
    <div id="modalForm" class="modal" style="display:none;">
        <div class="modal-content">
            <span id="cerrarModal" class="close">&times;</span>
            <!-- Aquí dentro se incluye el formulario -->
            <form id="nuevoLibroForm" action="guardar_libro_tema.php" method="post">
                <div>
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha">
                </div>
                <div>
                    <label for="capacidades">Capacidades</label>
                    <textarea name="capacidades" placeholder="Capacidades"></textarea>
                </div>
                <div>
                    <label for="contenidos">Contenidos</label>
                    <textarea name="contenidos" placeholder="Contenidos"></textarea>
                </div>
                <div>
                    <label for="evaluacion">Evaluación</label>
                    <textarea name="evaluacion" placeholder="Evaluación"></textarea>
                </div>
                <div>
                    <label for="observacion">Observación Diaria</label>
                    <textarea name="observacion" placeholder="Observación Diaria"></textarea>
                </div>
                <div>
                    <input type="hidden" name="profesor" value="<?php echo $_SESSION['id']; ?>">
                    <input type="hidden" name="carrera" value="<?php echo $carrera; ?>">
                    <input type="hidden" name="materias" value="<?php echo implode(',', $materias); ?>">
                    <input type="submit" name="enviar" value="Confirmar" class="btn-enviar">
                </div>
            </form>
        </div>
    </div>
    
    <table id="tablaLibros" class="tabla-elegante">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Capacidades</th>
                <th>Contenidos</th>
                <th>Evaluación</th>
                <th>Observaciones diarias</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Aquí se cargarán los datos dinámicamente -->
        </tbody>
    </table>
</div>
<script>
 
 $(document).ready(function() {
        // Mostrar el modal al hacer clic en el botón "Cargar Día"
        $('#abrirModal').on('click', function() {
            $('#modalForm').fadeIn();
        });

        // Ocultar el modal al hacer clic en el botón de cerrar
        $('#cerrarModal').on('click', function() {
            $('#modalForm').fadeOut();
        });

        // Cerrar el modal al hacer clic fuera del contenido del modal
        $(window).on('click', function(event) {
            if (event.target == document.getElementById('modalForm')) {
                $('#modalForm').fadeOut();
            }
        });

        function cargarDatos() {
            $.ajax({
                url: 'ajax_libro_tema.php',
                method: 'GET',
                data: {
                    carrera: "<?php echo $carrera; ?>",
                    materias: "<?php echo implode(',', $materias); ?>"
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    const tbody = $('#tablaLibros tbody');
                    
                    // Si hay datos, agregarlos a la tabla
                    if (data.length > 0) {
    data.forEach(row => {
       const fechaOriginal = row.fecha;  // Usar directamente la cadena de fecha
        const fechaFormateada = fechaOriginal;  // Ya está en formato yyyy-mm-dd

        const newRow = $(`
            <tr>
                <td class="fecha">${fechaFormateada}</td>
                <td class="editable" data-campo="capacidades">${row.capacidades}</td>
                <td class="editable" data-campo="contenidos">${row.contenidos}</td>
                <td class="editable" data-campo="evaluacion">${row.evaluacion}</td>
                <td class="editable" data-campo="observacion_diaria">${row.observacion_diaria}</td>
                <td>
                    <button class="btn-modificar">Modificar</button>
                    <button class="btn-borrar">Borrar</button>
                    <button type="submit" class="btn-actualizar" style="display: none;">Actualizar cambios</button>
                    <input type="hidden" class="materia-id" value="${row.idMaterias}">
                    <input type="hidden" class="carrera-id" value="${row.idCarrera}">
                </td>
            </tr>
        `);
        tbody.append(newRow);
    });
}

                    // Delegación de eventos para los botones
                    tbody.on('click', '.btn-modificar', function() {
                        var fila = $(this).closest('tr');
                        fila.find('.editable').each(function() {
                            var contenido = $(this).text();
                            $(this).html('<input type="text" value="' + contenido + '">');
                        });
                        fila.find('.btn-modificar').hide();
                        fila.find('.btn-actualizar').show();
                    });

                    tbody.on('click', '.btn-actualizar', function() {
                        var fila = $(this).closest('tr');
                        var datos = {
                            original_fecha: fila.find('.fecha').text(),
                            materia: fila.find('.materia-id').val(),
                            carrera: fila.find('.carrera-id').val(),
                            profesor: "<?php echo $_SESSION['id']; ?>"
                        };

                        fila.find('.editable').each(function() {
                            var campo = $(this).data('campo');
                            var contenido = $(this).find('input').val();
                            datos[campo] = contenido;
                            $(this).html(contenido);
                        });

                        $.ajax({
                            url: 'update_libro_tema.php',
                            method: 'POST',
                            data: datos,
                            success: function(response) {
                                alert('Datos actualizados exitosamente.');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al actualizar los datos:', error);
                            }
                        });

                        fila.find('.btn-actualizar').hide();
                        fila.find('.btn-modificar').show();
                    });

                    // Confirmación y eliminación
                  tbody.on('click', '.btn-borrar', function() {
    var fila = $(this).closest('tr');
    var fecha = fila.find('.fecha').text();
    var materia = fila.find('.materia-id').val();
    var carrera = fila.find('.carrera-id').val();

    // Mostrar los datos que se enviarán

    if (confirm('¿Está seguro de que desea borrar este registro?')) {
        $.ajax({
            url: 'delete_libro_tema.php',
            method: 'POST',
            data: {
                fecha: fecha,
                materia: materia,
                carrera: carrera
            },
            success: function(response) {
                alert(response);  // Mostrar el mensaje del PHP
                if (response.includes('exitosamente')) {
                    fila.remove();  // Eliminar la fila de la tabla si el borrado fue exitoso
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al borrar el registro:', error);
                alert('Error al borrar el registro.');
            }
        });
    }
});
                }
            });
        }

        // Llamar a la función para cargar los datos
        cargarDatos();
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

<script>
   $(document).ready(function() {
    // Función para hacer que los textarea crezcan dinámicamente verticalmente según el contenido
    $('textarea').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    }).on('input', function () {
        this.style.height = 'auto';  // Resetea la altura para calcular el nuevo tamaño
        this.style.height = (this.scrollHeight) + 'px';  // Ajusta la altura al contenido
    });
});
</script>
<style>
	.contenido {
    position: absolute;
    top: 55px;
    left: 270px;
    width: calc(100% - 270px);
    background-color: #ffffff;
    background-image: url(assets/img/fondo.png);
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

    /* Tabla elegante */
    .tabla-elegante {
        width: 100%;
        border-collapse: collapse;
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
        margin-top: 5px;
    }
    .tabla-elegante thead th {
        background-color: #f3545d;
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: bold;
        letter-spacing: 1px;
    }
    .tabla-elegante tbody td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }
    .tabla-elegante tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .tabla-elegante tbody tr:hover {
        background-color: #f1f1f1;
        cursor: pointer;
    }
    input[type="date"]{
        width: 100%;
        padding: 10px;
        border: 2px solid #f3545d;
        border-radius: 5px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        font-size: 14px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        min-height: 25px;
    }
    textarea{
        width: 100%;
        min-height: 100px; /* Ajusta esta altura según prefieras */
        padding: 10px;
        border: 2px solid #c70039;
        border-radius: 5px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        font-size: 14px;
        resize: none; /* Evita el redimensionado manual en horizontal y vertical */
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    input[type="date"]:focus, textarea:focus {
        outline: none;
        border-color: #ff002b;
        box-shadow: 0 0 8px rgba(199, 0, 57, 0.3);
    }
    .btn-enviar {
        background-color: #f3545d;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        text-transform: uppercase;
        transition: background-color 0.3s ease, transform 0.3s ease;
        display: inline-block;
    }
    .btn-enviar:hover {
        background-color: #ff002b;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    /* Estilos del modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }
    .modal-content {
        
        background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 10px;
    position: relative;
    top: 10%; /* Ajusta este valor para subir o bajar el modal */
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover, .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    /* Botón de modificar */
.btn-modificar {
    background-color: #007bff;  /* Color azul para el botón de modificar */
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn-modificar:hover {
    background-color: #0056b3;  /* Un azul más oscuro al hacer hover */
    transform: translateY(-2px);  /* Efecto de elevar el botón */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Botón de borrar */
.btn-borrar {
    background-color: #dc3545;  /* Rojo para el botón de borrar */
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn-borrar:hover {
    background-color: #c82333;  /* Un rojo más oscuro al hacer hover */
    transform: translateY(-2px);  /* Efecto de elevar el botón */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Botón de actualizar (aparece cuando se modifican los campos) */
.btn-actualizar {
    background-color: #28a745;  /* Verde para el botón de actualizar */
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    display: none; /* Inicialmente oculto */
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn-actualizar:hover {
    background-color: #218838;  /* Un verde más oscuro al hacer hover */
    transform: translateY(-2px);  /* Efecto de elevar el botón */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

</style>
</body>
</html>

