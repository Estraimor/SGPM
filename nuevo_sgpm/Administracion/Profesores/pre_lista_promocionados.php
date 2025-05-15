<?php
 include'../../../conexion/conexion.php'; 
session_start();
if (empty($_SESSION["id"])){header('Location: ../../../login/login.php');}

// Asumimos que también almacenas el rol en la sesión.
$rolUsuario = $_SESSION["roles"];

// Definimos los roles permitidos para esta página.
$rolesPermitidos = ['1', '2', '3'];

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
									<img src="../../assets/img/1361728.png" alt="..." class="avatar-img rounded-circle" >
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
							<li>
								<div class="user-box">
									<div class="avatar-lg"><img src="../../assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;"></div>
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
									<a class="dropdown-item" href="#">Mi Perfil</a>
									<a class="dropdown-item" href="#">Cambiar Contraseña</a>
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
									<li>
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
									<li  >
										<a href="../../Estudiantes/Tecnicatura/Falta_justificada/falta_justificada.php">
											<span class="sub-item">Justificar Falta</span>
										</a>
									</li>
									<?php endif; ?>
									<li >
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
                        <li>
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
						<a href="../../ver_inscriptos_mesas.php">
							<span class="sub-item">Contador de Inscriptos a Mesas</span>
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
										<a href="./alta_docente.php">
											<span class="sub-item">Alta Docentes</span>
										</a>
									</li>
									<li >
										<a href="./lista_profesores.php">
											<span class="sub-item">Lista de Docentes</span>
										</a>
									</li>
									<li>
										<a href="./materia_profesor.php">
											<span class="sub-item">Asignar Materia a Profesor</span>
										</a>
									</li>	
									
									<?php if ($rolUsuario == '1'): ?>
									<li>
										<a href="../../Estudiantes/Tecnicatura/pagos.php">
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
                            <a href="./pre_parciales.php">
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
<li class="nav-item active">
    <a data-toggle="collapse" href="#preceptorUtilities">
        <i class="fas fa-toolbox"></i>
        <p>Utilidades<br> del Preceptor</p>
        <span class="caret"></span>
    </a>
    <div class="collapse show" id="preceptorUtilities">
        <ul class="nav nav-collapse">
            <li class="active">
                <a href="./pre_lista_promocionados.php">
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
session_start();
$idPreceptor = $_SESSION['id'];
$rolUsuario = $_SESSION["roles"];
$anioActual = date("Y");

// Consulta de materias según rol
if ($rolUsuario == 1) {
    $queryCarreras = "
        SELECT 
            c.idCarrera, 
            c.nombre_carrera, 
            m.Nombre, 
            m.idMaterias,
            cu.curso,
            cu.idCursos,
            co.comision,
            co.idComisiones
        FROM materias m
        INNER JOIN carreras c ON c.idCarrera = m.carreras_idCarrera
        INNER JOIN cursos cu ON cu.idCursos = m.cursos_idCursos
        INNER JOIN comisiones co ON co.idComisiones = m.comisiones_idComisiones
        GROUP BY m.idMaterias
    ";
    $stmt = $conexion->prepare($queryCarreras);
} else {
    $queryCarreras = "
        SELECT 
            c.idCarrera, 
            c.nombre_carrera, 
            m.Nombre, 
            m.idMaterias,
            cu.curso,
            cu.idCursos,
            co.comision,
            co.idComisiones
        FROM materias m
        INNER JOIN carreras c ON c.idCarrera = m.carreras_idCarrera
        INNER JOIN cursos cu ON cu.idCursos = m.cursos_idCursos
        INNER JOIN comisiones co ON co.idComisiones = m.comisiones_idComisiones
        INNER JOIN preceptores p ON 
            p.carreras_idCarrera = m.carreras_idCarrera AND 
            p.cursos_idCursos = m.cursos_idCursos AND 
            p.comisiones_idComisiones = m.comisiones_idComisiones AND 
            p.profesor_idProfesor = ?
        GROUP BY m.idMaterias
    ";
    $stmt = $conexion->prepare($queryCarreras);
    $stmt->bind_param("i", $idPreceptor);
}

$stmt->execute();
$resultCarreras = $stmt->get_result();
$carreras = $resultCarreras->fetch_all(MYSQLI_ASSOC);
?>

<!-- HTML -->
<div class="contenido">
    <div class="contenedor-materias">
        <h2 class="titulo">Acta Volantes de Promocionados</h2>

        <!-- Selector de año -->
        <div style="margin-bottom: 1rem;">
            <label for="anio">Seleccionar año:</label>
            <select id="anio" name="anio">
                <option value="">Seleccione un año</option>
                <?php for ($anio = 2023; $anio <= $anioActual; $anio++) { ?>
                    <option value="<?php echo $anio; ?>"><?php echo $anio; ?></option>
                <?php } ?>
            </select>
        </div>
       
        <div class="tabla-contenedor">
            <table id="tablaMaterias" class="tabla-materias">
                <thead>
                    <tr>
                        <th>Carrera</th>
                        <th>Unidad Curricular</th>
                        <th>Curso</th>
                        <th>Comisión</th>
                        <th>Seleccionar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($carreras) && is_array($carreras)) {
                        foreach ($carreras as $carrera) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($carrera['nombre_carrera']); ?></td>
                                <td><?php echo htmlspecialchars($carrera['Nombre']); ?></td>
                                <td><?php echo htmlspecialchars($carrera['curso']); ?></td>
                                <td><?php echo htmlspecialchars($carrera['comision']); ?></td>
                                <td>
                                    <button class="btn-seleccionar" onclick="validarYRedirigir(
                                        '<?php echo $carrera['idCarrera']; ?>', 
                                        '<?php echo $carrera['idMaterias']; ?>', 
                                        '<?php echo $carrera['idCursos']; ?>', 
                                        '<?php echo $carrera['idComisiones']; ?>')">
                                        <span class="icono">&#10003;</span> Seleccionar
                                    </button>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="5">No hay materias asociadas.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
function validarYRedirigir(idCarrera, idMaterias, idCurso, idComision) {
    const anio = document.getElementById('anio').value;

    if (!anio || anio.trim() === "") {
        alert("Por favor, seleccioná un año antes de continuar.");
        return;
    }

    window.location.href = "lista_promocionados.php?carrera=" + idCarrera + 
                           "&materia=" + idMaterias + 
                           "&curso=" + idCurso + 
                           "&comision=" + idComision + 
                           "&anio=" + anio;
}

new DataTable(document.querySelector("#tablaMaterias"));
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


.contenedor-materias {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
	width: 75%;
}

.titulo {
    text-align: center;
    color: #f3545d; /* Rojo fuerte */
    font-size: 24px;
    margin-bottom: 20px;
}

.indicaciones {
    background-color: #fff8b0; /* Fondo amarillo */
    padding: 10px;
    border-radius: 5px;
    font-size: 16px;
    color: #333;
    margin-bottom: 20px;
}

.input-buscador {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #f3545d;
    border-radius: 5px;
    font-size: 16px;
}

.tabla-contenedor {
    overflow-x: auto;
}

.tabla-materias {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.tabla-materias th, .tabla-materias td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

.tabla-materias th {
    background-color: #f3545d; /* Rojo */
    color: #fff; /* Blanco */
    position: sticky;
    top: 0;
}

.tabla-materias tr:nth-child(even) {
    background-color: #f9f9f9;
}

.tabla-materias tr:hover {
    background-color: #f3545d;
	color: white;
}

.btn-seleccionar {
    background-color: #ff045d; /* Rojo */
    color: #fff;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.btn-seleccionar:hover {
    background-color: #b71c1c; /* Rojo más oscuro */
}

.icono {
    margin-right: 5px;
}
</style>
</body>
</html>

