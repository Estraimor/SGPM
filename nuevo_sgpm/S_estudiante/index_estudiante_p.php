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
						<li class="nav-item active">
							<a href="#">
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
                </div>
            </li>
            
						
		</div>
		<!-- End Sidebar -->
	</div>
	
</div>
<div class="contenido">
    <br>
    <div class="welcome-box">
        <?php 
        $sql_alu = "SELECT * FROM alumno a WHERE a.legajo = '{$_SESSION['id']}'";
        $query_nombre = mysqli_query($conexion, $sql_alu);
        
        if (mysqli_num_rows($query_nombre) > 0) {
            while ($row = mysqli_fetch_assoc($query_nombre)) { ?>
                <h2>Bienvenido/a </h2>
                <p>¡Estamos encantados de verte de nuevo!</p>
                <p><?php echo "" . $row['nombre_alumno'] . " " . $row['apellido_alumno']; ?></p>
            <?php }
        } else {
            echo "No se encontraron datos del estudiante.";
        }
        ?>
    </div>
    <br>
    <br>

    <div class="table-box">
    <?php
    $sql_materias = "
        SELECT 
            m.Nombre AS materia, 
            t.fecha, 
            t.llamado, 
            t.tanda, 
            mf.idMesas_finales AS mesa_id,
            t.idtandas AS tanda_id
        FROM mesas_finales mf
        JOIN fechas_mesas_finales fmf ON mf.fechas_mesas_finales_idfechas_mesas_finales = fmf.idfechas_mesas_finales
        JOIN materias m ON mf.materias_idMaterias = m.idMaterias
        JOIN tandas t ON fmf.tandas_idtandas = t.idtandas
        WHERE mf.alumno_legajo = '{$_SESSION['id']}'";
    
    $query_materias = mysqli_query($conexion, $sql_materias);
    
    if (mysqli_num_rows($query_materias) > 0) { ?>
        <h3 class="titulo-unidad">Unidades Curriculares Inscriptas</h3>
        <table border="1" cellspacing="0" cellpadding="10">
            <thead class="tablita">
                <tr>
                    <th>Unidad Curricular</th>
                    <th>Fecha</th>
                    <th>Llamado</th>
                    <th>Tanda</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_materia = mysqli_fetch_assoc($query_materias)) { ?>
                    <tr>
                        <td><?php echo $row_materia['materia']; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row_materia['fecha'])); ?></td>
                        <td><?php echo $row_materia['llamado']; ?></td>
                        <td><?php echo $row_materia['tanda']; ?></td>
                        <td>
                            <form action="dar_baja_mesa_final.php" method="post" onsubmit="return confirm('¿Estás seguro de que deseas dar de baja esta inscripción?');">
                                <input type="hidden" name="mesa_id" value="<?php echo $row_materia['mesa_id']; ?>">
                                <input type="hidden" name="tanda_id" value="<?php echo $row_materia['tanda_id']; ?>">
                                <input type="hidden" name="fecha" value="<?php echo $row_materia['fecha']; ?>">
                                <button type="submit" class="button-dar-baja">Anular Inscripción</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <!-- Botón para descargar la constancia de inscripción -->
        <div style="text-align: center; margin-top: 20px;">
            <form action="generar_constancia.php" method="post" target="_blank">
                <button type="submit" class="boton-inscripcion">
                    Descargar Constancia de Inscripción
                </button>
            </form>
        </div>
        
    <?php } else { ?>
        <h3 class="titulo-unidad">¡Aviso!</h3>
        <p style="text-align: center; padding: 20px; font-size: 1.1em; color: black;">
            Una vez que usted se inscriba a las mesas, se mostrarán aquí!
        </p>
    <?php } ?>
</div>
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

