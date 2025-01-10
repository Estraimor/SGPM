
<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: ../../login/login.php');
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
    header("Location: ../../login/login.php");
    exit;
} else {
    $_SESSION['time'] = time();
}

if ($_SESSION['contraseña'] === '0123456789') {header('Location: cambio_contrasena.php');}


?>
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
	

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="../assets/css/demo.css">
</head>
<body>
	<div class="wrapper">
		
		<div class="main-header" data-background-color="red">
			<div class="logo-header">
				
				<a href="./index_estudiante.php" class="logo">
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
							<a href="./index_estudiante.php">
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
						<li class="nav-item active">
							<a data-toggle="collapse" href="#base">
								<i class="fas fa-child"></i>
								<p>Mesas</p>
								<span class="caret"></span>
							</a>
							<div class="collapse show" id="base">
								<ul class="nav nav-collapse">
									<li class="active">
										<a href="#">
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
	
</div>
    <div class="contenido">
    <h1 style="text-align: center; margin-bottom: 20px; color: #f3545d; font-weight: 900;">
        Inscripción a Mesas de Exámenes del ISPM N°1
    </h1>
    <p class="instrucciones">
        Para inscribirse en la mesa de examen, seleccione una materia con cupos disponibles y presione el botón que dice "Inscribirse".<br> 
        <strong>Importante:</strong> la inscripción estará disponible hasta 36 horas antes del inicio de la mesa de examen. Asegúrese de completar el proceso con tiempo suficiente.
    </p>
    <div class="tabla-contenedor">
        <table id="tabla-materias">
            <thead>
                <tr>
                    <th>Unidad Curricular</th>
                    <th>Fecha</th>
                    <th>Tanda</th>
                    <th>Llamado</th>
              
                    <th>Inscribirse</th>
                </tr>
            </thead>
            <tbody id="materias-lista">
                <!-- Las materias se llenarán desde el backend -->
            </tbody>
        </table>
    </div>
</div>

<script>
const fechasInscritas = new Set();  // Mueve la declaración de fechasInscritas al ámbito global

document.addEventListener("DOMContentLoaded", function () {
    fetch('obtener_materias_mesas_finales.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            let materiasLista = document.getElementById("materias-lista");
            materiasLista.innerHTML = '';  

            if (data.length === 0) {
                materiasLista.innerHTML = '<tr><td colspan="6">No hay materias disponibles</td></tr>';
                return;
            }

            const calcularTurno = (fecha) => {
                const year = new Date(fecha).getFullYear();
                const parsedDate = new Date(fecha);

                if (parsedDate >= new Date(`${year}-07-01`) && parsedDate <= new Date(`${year}-08-31`)) return 1;
                if (parsedDate >= new Date(`${year}-11-01`) && parsedDate <= new Date(`${year}-12-31`)) return 2;
                if (parsedDate >= new Date(`${year}-02-01`) && parsedDate <= new Date(`${year}-03-31`)) return 3;
                return null;
            };

            const turnoActual = (() => {
                const now = new Date();
                const year = now.getFullYear();
                if (now >= new Date(`${year}-07-01`) && now < new Date(`${year}-11-01`)) return 1;
                if (now >= new Date(`${year}-11-01`) && now <= new Date(`${year}-12-31`)) return 2;
                if (now >= new Date(`${year}-02-01`) && now <= new Date(`${year}-03-31`)) return 3;
                return null;
            })();

            data.forEach(materia => {
                if (!materia.disponible) return;

                let turnoMateria = calcularTurno(materia.fecha);
                if (turnoMateria !== turnoActual) return;

                let row = document.createElement("tr");
                let cupoDisponible = parseInt(materia.cupo, 10);
                let llamadoDisplay = (materia.llamado == 1) ? 'Primer Llamado' : 'Segundo Llamado';
                let inscribirseEnlace = '';

                if (fechasInscritas.has(materia.fecha)) {
                    inscribirseEnlace = '<span style="color:red;">Solo puedes rendir una unidad curricular por día</span>';
                } else if (materia.inscrito && turnoMateria === turnoActual) {
                    inscribirseEnlace = '<span style="color:blue;">Ya estás inscripto en esta mesa en este turno</span>';
                } else if (materia.nota_final >= 6) {
                    inscribirseEnlace = '<span style="color:green;">Ya aprobaste esta unidad curricular</span>';
                } else if (materia.inscrito_mismo_llamado == 1 && turnoMateria === turnoActual) {
                    inscribirseEnlace = '<span style="color:red;">Ya estás inscrpito en esta unidad curricular para este llamado y turno</span>';
                } else if (cupoDisponible > 0) {
                    inscribirseEnlace = `<a href="#" onclick="inscribirse(${materia.idfechas_mesas_finales}, '${materia.fecha}', ${materia.tanda}, ${cupoDisponible}); return false;">Inscribirse</a>`;
                } else {
                    inscribirseEnlace = '<span style="color:red;">Cupo agotado</span>';
                }

                row.innerHTML = `
                    <td>${materia.materia}</td>
                    <td>${materia.fecha}</td>
                    <td>${materia.tanda}</td>
                    <td>${llamadoDisplay}</td>
                    <td>${inscribirseEnlace}</td>
                `;
                materiasLista.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            let materiasLista = document.getElementById("materias-lista");
            materiasLista.innerHTML = '<tr><td colspan="6">Error al cargar las materias</td></tr>';
        });
});

function inscribirse(idFecha, fecha, tanda, cupoActual) {
    if (cupoActual <= 0) {
        alert("Cupo agotado para esta mesa.");
        return;
    }

    // Verificación para evitar inscribir en la misma fecha
    if (fechasInscritas.has(fecha)) {
        alert("Solo puedes rendir una materia por día.");
        return;
    }

    fetch('inscribir_mesa.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ idFecha: idFecha, tanda: tanda })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Inscripción exitosa en la Tanda " + tanda);
            fechasInscritas.add(fecha);  // Marca la fecha como inscrita después de una inscripción exitosa
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>






<style>
  .contenido {
    position: absolute;
    top: 55px;
    left: 270px;
    width: calc(100% - 270px);
    background-color: #ffffff;
    background-image: url(../assets/img/fondo.png);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: local;
    padding: 20px;
    min-height: 100%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
  }

  @media (max-width: 768px) {
    .contenido {
      width: 100%;
      left: 0;
      padding: 15px;
    }
  }
  .instrucciones {
        background-color: #fff8b0; /* Fondo amarillo claro */
        padding: 10px;
        border-radius: 5px;
        font-size: 16px;
        margin-bottom: 20px;
    }
  .tabla-contenedor {
    width: 100%;
    margin-top: 10px;
    overflow-x: auto;
  }

  #tabla-materias {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    background-color: #ffffff;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  #tabla-materias thead {
    background-color: #f3545d;
    color: #ffffff;
    font-weight: bold;
    position: sticky;
    top: 0;
    z-index: 1;
  }

  #tabla-materias th, #tabla-materias td {
    padding: 10px 8px; /* Reducción del padding */
    text-align: left;
    border-bottom: 1px solid #eaeaea;
    word-wrap: break-word; /* Permite el ajuste de línea */
    white-space: normal; /* Ajuste de línea activado */
  }

  #tabla-materias tbody tr {
    transition: background-color 0.3s, transform 0.1s;
  }

  #tabla-materias tbody tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  #tabla-materias tbody tr:hover {
    background-color: #ffe4e8;
    transform: scale(1.01);
  }

  #tabla-materias td {
    overflow: visible;
  }

  #tabla-materias td a {
    color: #f3545d;
    text-decoration: none;
    font-weight: bold;
  }

  #tabla-materias td a:hover {
    color: #c13d4a;
    text-decoration: underline;
  }

  #tabla-materias td span {
    font-weight: bold;
    color: #c13d4a;
  }

  @media (max-width: 480px) {
    .contenido {
      padding: 10px;
    }

    #tabla-materias {
      font-size: 12px; /* Fuente más pequeña */
    }

    #tabla-materias th, #tabla-materias td {
      padding: 8px 5px; /* Más compacto */
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

