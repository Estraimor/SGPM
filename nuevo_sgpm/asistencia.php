<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../login/login.php');
    exit;
}

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


include '../conexion/conexion.php';
$comisionId = isset($_GET['comision']) ? intval($_GET['comision']) : 0;

if ($comisionId <= 0) {
    die('Error: ID de comisión inválido.');
}

$sql_materias = "SELECT m.idMaterias, m.Nombre FROM materias m
                 INNER JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
                 WHERE c.idCarrera = '$comisionId'";
$query_materias = mysqli_query($conexion, $sql_materias);
$materias = mysqli_fetch_all($query_materias, MYSQLI_ASSOC);

$sql = "SELECT ia.alumno_legajo, a2.nombre_alumno, a2.apellido_alumno
        FROM asistencia a 
        RIGHT JOIN inscripcion_asignatura ia ON a.inscripcion_asignatura_idinscripcion_asignatura = ia.idinscripcion_asignatura 
        INNER JOIN alumno a2 ON ia.alumno_legajo = a2.legajo   
        WHERE ia.carreras_idCarrera = '$comisionId' and a2.estado = '1'
        GROUP BY a2.nombre_alumno, a2.apellido_alumno
        ORDER BY a2.apellido_alumno";
$query = mysqli_query($conexion, $sql);
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
									<img src="assets/img/1361728.png" alt="..." class="avatar-img rounded-circle" >
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
							<li>
								<div class="user-box">
									<div class="avatar-lg"><img src="assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;"></div>
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
									<?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
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
						<li class="nav-item active">
                			<a data-toggle="collapse" href="#takeAttendance">
                    			<i class="fas fa-pen-square"></i>
                    			<p>Tomar Asistencia</p>
                    			<span class="caret"></span>
                			</a>
                			<div class="collapse show" id="takeAttendance">
								
                    <ul class="nav nav-collapse">
					<?php if ($rolUsuario == '1' || $rolUsuario == '2'|| $rolUsuario == '3'): ?>
                        <li class="active">
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
						<?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
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
						
		</div>
		<!-- End Sidebar -->
	</div>
	
</div>
<div class="form-container">
    <form action="../../Profesor/asistencia/guardar_asistencia_enfermeria.php" method="post" onsubmit="showModalMessage()">
        <div class="date-picker" style="display: none;">
            <input type="hidden" id="fecha" name="fecha" readonly>
        </div>
        <div class="inner-container">
            <div class="table-container">
                <table class="table-comision-a">
                    <thead>
                        <tr>
                            <th rowspan="2">N°</th>
                            <th rowspan="2">Apellido</th>
                            <th rowspan="2">Nombre</th>
                            <th colspan="2">
                                <select name="materiaPrimera" class="form-container__input">
                                    <option hidden>Primera Materia</option>
                                    <?php foreach ($materias as $materia) { ?>
                                        <option value="<?php echo $materia['idMaterias']; ?>"><?php echo $materia['Nombre']; ?></option>
                                    <?php } ?>
                                </select>
                                <button type="button" class="toggle-materia-btn" onclick="toggleMateria('materiaTercera')">Pareja pedagógica 1</button>
                            </th>
                            <th colspan="2">
                                <select name="materiaSegunda" class="form-container__input">
                                    <option hidden>Segunda Materia</option>
                                    <?php foreach ($materias as $materia) { ?>
                                        <option value="<?php echo $materia['idMaterias']; ?>"><?php echo $materia['Nombre']; ?></option>
                                    <?php } ?>
                                </select>
                                <button type="button" class="toggle-materia-btn" onclick="toggleMateria('materiaCuarta')">Pareja pedagógica 2</button>
                            </th>
                            <th colspan="2" class="materiaTercera">
                                <select name="materiaTercera" class="form-container__input">
                                    <option hidden>Tercera Materia</option>
                                    <?php foreach ($materias as $materia) { ?>
                                        <option value="<?php echo $materia['idMaterias']; ?>"><?php echo $materia['Nombre']; ?></option>
                                    <?php } ?>
                                </select>
                            </th>
                            <th colspan="2" class="materiaCuarta">
                                <select name="materiaCuarta" class="form-container__input">
                                    <option hidden>Cuarta Materia</option>
                                    <?php foreach ($materias as $materia) { ?>
                                        <option value="<?php echo $materia['idMaterias']; ?>"><?php echo $materia['Nombre']; ?></option>
                                    <?php } ?>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th>Presente</th>
                            <th>Ausente</th>
                            <th>Presente</th>
                            <th>Ausente</th>
                            <th class="materiaTercera">Presente</th>
                            <th class="materiaTercera">Ausente</th>
                            <th class="materiaCuarta">Presente</th>
                            <th class="materiaCuarta">Ausente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $contador = 1;
                        $query = mysqli_query($conexion, $sql);
                        while ($datos = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?php echo $contador++; ?></td>
                                <td><?php echo $datos['apellido_alumno']; ?></td>
                                <td><?php echo $datos['nombre_alumno']; ?></td>
                                <!-- Primera hora -->
                                <td class="checkbox-cell">
                                    <input type="checkbox" name="presentePrimera[]" value="<?php echo $datos['alumno_legajo']; ?>" class="custom-checkbox">
                                </td>
                                <td class="checkbox-cell">
                                    <input type="checkbox" name="ausentePrimera[]" value="<?php echo $datos['alumno_legajo']; ?>" class="custom-checkbox">
                                </td>
                                <!-- Segunda hora -->
                                <td class="checkbox-cell">
                                    <input type="checkbox" name="presenteSegunda[]" value="<?php echo $datos['alumno_legajo']; ?>" class="custom-checkbox">
                                </td>
                                <td class="checkbox-cell">
                                    <input type="checkbox" name="ausenteSegunda[]" value="<?php echo $datos['alumno_legajo']; ?>" class="custom-checkbox">
                                </td>
                                <!-- Tercera hora -->
                                <td class="checkbox-cell materiaTercera">
                                    <input type="checkbox" name="presenteTercera[]" value="<?php echo $datos['alumno_legajo']; ?>" class="custom-checkbox">
                                </td>
                                <td class="checkbox-cell materiaTercera">
                                    <input type="checkbox" name="ausenteTercera[]" value="<?php echo $datos['alumno_legajo']; ?>" class="custom-checkbox">
                                </td>
                                <!-- Cuarta hora -->
                                <td class="checkbox-cell materiaCuarta">
                                    <input type="checkbox" name="presenteCuarta[]" value="<?php echo $datos['alumno_legajo']; ?>" class="custom-checkbox">
                                </td>
                                <td class="checkbox-cell materiaCuarta">
                                    <input type="checkbox" name="ausenteCuarta[]" value="<?php echo $datos['alumno_legajo']; ?>" class="custom-checkbox">
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="btn-container">
            <input type="hidden" name="idcarrera" value="<?php echo $comisionId; ?>">
            <input type="submit" name="Enviar" value="Confirmar" class="boton-enviar" style="max-width:250px; margin-bottom: 15px;">
        </div>
    </form>
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

<script>
    function toggleMateria(clase) {
        var elements = document.getElementsByClassName(clase);
        for (var i = 0; i < elements.length; i++) {
            if (elements[i].style.display === "none" || elements[i].style.display === "") {
                elements[i].style.display = "table-cell";
            } else {
                elements[i].style.display = "none";
            }
        }
    }

    // Inicialmente ocultar las columnas de tercera y cuarta materia
    document.addEventListener("DOMContentLoaded", function() {
        var materiaTercera = document.getElementsByClassName('materiaTercera');
        var materiaCuarta = document.getElementsByClassName('materiaCuarta');

        for (var i = 0; i < materiaTercera.length; i++) {
            materiaTercera[i].style.display = "none";
        }

        for (var i = 0; i < materiaCuarta.length; i++) {
            materiaCuarta[i].style.display = "none";
        }
        
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false; // Nuevo: Desmarcar los checkboxes al cargar la página
        });
    });
</script>



<style>
  .form-container {
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
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}
@media (max-width: 768px) {
    .form-container {
        width: 100%;
        left: 0;
    }
 }

.inner-container {
    max-width: 80%;
    margin: 0 auto;
    overflow-x: auto;
    padding: 20px;
    background-color: rgba(255, 0, 0, 0.1);
    border: 2px solid red;
    border-radius: 8px;
}

.table-container {
    overflow-x: auto;
    margin-top: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
}

th, td {
    padding: 10px;
    border: 3px solid white;
    text-align: center;
}

th {
    background-color: #ff4d4d;
    color: #fff;
    
    position: sticky;
    top: 0;
    z-index: 2;
}

.checkbox-cell input[type="checkbox"] {
    margin: 0 auto;
    display: block;
    width: 20px;
    height: 20px;
    border: 2px solid red;
    background-color: white;
    cursor: pointer;
}

.checkbox-cell input[type="checkbox"]:checked {
    background-color: red;
}

.btn-container {
    text-align: center;
    margin-top: 20px;
}

.boton-enviar {
    background-color: #ff4d4d;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

.boton-enviar:hover {
    background-color: #e60000;
}

.form-container__input {
    width: 100%;
    padding: 5px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.button-container {
    text-align: center;
    margin-top: 10px;
}

.toggle-materia-btn {
    background-color: #e60000;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin: 5px;
}

.toggle-materia-btn:hover {
    background-color: #e60000;
}
    </style>
</body>
</html>

