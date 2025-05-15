<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: https://politecnicomisiones.edu.ar/SGPM2/login/login.php');
    exit;
}

// Verificación de la contraseña específica "0123456789"
if (isset($_SESSION["contraseña"]) && $_SESSION["contraseña"] === "0123456789") {
    header('Location: https://politecnicomisiones.edu.ar/SGPM2/cambio_contrasena_profe.php');
    exit;
}

// Asumimos que también almacenas el rol en la sesión.
$rolUsuario = $_SESSION["roles"];

// Definimos los roles permitidos para esta página.
$rolesPermitidos = ['1', '2', '3', '4', '5'];

if (!in_array($rolUsuario, $rolesPermitidos)) {
    echo "<script>alert('Acceso restringido a esta página.');</script>";
    exit;
}

$idPreceptor = $_SESSION['id'];
include __DIR__ . '/../conexion/conexion.php';

// Consulta para obtener las carreras asociadas al preceptor
$queryCarreras = "SELECT p.carreras_idCarrera, c.nombre_carrera FROM preceptores p
                  INNER JOIN carreras c ON c.idCarrera = p.carreras_idCarrera
                  WHERE p.profesor_idProrfesor = $idPreceptor";
$resultCarreras = mysqli_query($conexion, $queryCarreras);
$carreras = mysqli_fetch_all($resultCarreras, MYSQLI_ASSOC);

// Set inactivity limit in seconds
$inactivity_limit = 1200;
if (isset($_SESSION['time']) && (time() - $_SESSION['time'] > $inactivity_limit)) {
    session_unset();
    session_destroy();
    header("Location: ../login/login.php");
    exit;
} else {
    $_SESSION['time'] = time();
}

// Definir base URL
$base_url = "https://politecnicomisiones.edu.ar/SGPM2/nuevo_sgpm/";

// Determinar la página activa. Si no se define en la página que incluye este header, se toma el nombre del script actual.
if (!isset($activePage)) {
    $activePage = basename($_SERVER['PHP_SELF']);
}

// Arrays de páginas para cada menú (usa basename de cada archivo, aunque en los links se muestran sus rutas completas)
$estudiantesPages = array("nuevo_estudiante.php", "inscripcion_FP.php", "lista_estudiantes.php", "lista_estudiantes_2025.php", "lista_estudianteFP.php", "informe_asistencia_tecnicaturas.php", "informe_lista_estudiantes.php", "informe_lista_estudiantesFP.php", "falta_justificada.php", "estudiantes_retirados.php");
$asistenciaPages    = array("index_asistencia.php", "ver_FPS.php");
$verAsistenciaPages = array("ver_carreras.php", "ver_asistenciaFPS.php");
$utilidadesAdminPages = array("gestionar_mesas_finales.php", "ver_inscriptos_mesas.php", "alta_docente.php", "lista_profesores.php", "materia_profesor.php", "pagos.php","asignar_preceptor_panel.php", "asignar_cargo.php");
$utilidadesDocentePages = array("pre_parciales.php", "pre_libro.php");
$utilidadesPreceptorPages = array("pre_lista_promocionados.php", "actas_volante_estudiantes_regulares.php", "actas_volante_estudiantes_libres.php", "pre_nota_final.php", "carga_notas_pendientes.php", "tabla_notas_pendientes.php", "tabla_mesas_pendientes.php");
$preinscriptosPages = array("lista_pre_inscriptos.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>SGPM</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="<?php echo $base_url; ?>assets/img/Logo ISPM 2 transparante.png" type="image/x-icon"/>
   
    <!-- Fonts and icons -->
    <script src="<?php echo $base_url; ?>assets/js/plugin/webfont/webfont.min.js"></script>
    <link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
    <script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>
    <script>
        WebFont.load({
            google: {"families":["Open+Sans:300,400,600,700"]},
            custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"], urls: ['<?php echo $base_url; ?>assets/css/fonts.css']},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/azzara.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/estilos.css">
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/demo.css">
</head>
<body>
    <div class="wrapper">
        <div class="main-header" data-background-color="red">
            <div class="logo-header">
                <a href="<?php echo $base_url; ?>index.php" class="logo">
                    <img src="<?php echo $base_url; ?>assets/img/Logo ISPM 2 transparante.png" width="45px" alt="navbar brand" class="navbar-brand">
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
                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <!-- Se eliminó el bloque de búsqueda vacío -->
                        <li class="nav-item dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                                <div class="avatar-sm">
                                    <?php 
                                    $idProfesor = $_SESSION["id"];
                                    $sql = "SELECT avatar FROM profesor WHERE idProrfesor = '$idProfesor'";
                                    $result = mysqli_query($conexion, $sql);
                                    $imagenBase64 = "";
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        $row = mysqli_fetch_assoc($result);
                                        $avatar = $row['avatar'];
                                        if ($avatar) {
                                            $imagenBase64 = 'data:image/jpeg;base64,' . base64_encode($avatar);
                                        } else {
                                            $imagenBase64 = $base_url.'assets/img/1361728.png';
                                        }
                                    } else {
                                        echo "Error al cargar la imagen.";
                                    }
                                    ?>
                                    <?php if ($imagenBase64 === $base_url.'assets/img/1361728.png') : ?>
                                        <img src="<?php echo $base_url; ?>assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;">
                                    <?php else : ?>
                                        <img src="<?php echo $imagenBase64; ?>" alt="Avatar" class="avatar-img rounded-circle">
                                    <?php endif; ?>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <li>
                                    <div class="user-box">
                                        <div class="avatar-lg">
                                            <?php 
                                            $sql = "SELECT avatar FROM profesor WHERE idProrfesor = '$idProfesor'";
                                            $result = mysqli_query($conexion, $sql);
                                            $imagenBase64 = "";
                                            if ($result && mysqli_num_rows($result) > 0) {
                                                $row = mysqli_fetch_assoc($result);
                                                $avatar = $row['avatar'];
                                                if ($avatar) {
                                                    $imagenBase64 = 'data:image/jpeg;base64,' . base64_encode($avatar);
                                                } else {
                                                    $imagenBase64 = $base_url.'assets/img/1361728.png';
                                                }
                                            } else {
                                                echo "Error al cargar la imagen.";
                                            }
                                            ?>
                                            <?php if ($imagenBase64 === $base_url.'assets/img/1361728.png') : ?>
                                                <img src="<?php echo $base_url; ?>assets/img/1361728.png" alt="image profile" class="avatar-img rounded" style="width: 100%; height: auto;">
                                            <?php else : ?>
                                                <img src="<?php echo $imagenBase64; ?>" alt="Avatar" class="avatar-img rounded-circle">
                                            <?php endif; ?>
                                        </div>
                                        <div class="u-text">
                                            <?php 
                                            $sql_profe = "SELECT p.idProrfesor, p.nombre_profe, p.apellido_profe, p.email FROM profesor p WHERE p.idProrfesor = '{$_SESSION["id"]}'";
                                            $query_nombre = mysqli_query($conexion, $sql_profe);
                                            if (mysqli_num_rows($query_nombre) > 0) {
                                                while ($row = mysqli_fetch_assoc($query_nombre)) { ?>
                                                    <h4><?php echo $row['nombre_profe'] . " " . $row['apellido_profe']; ?></h4>
                                                    <p class="text-muted email-text"><?php echo $row['email']; ?></p>
                                            <?php }
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
                                    <a class="dropdown-item" href="<?php echo $base_url; ?>Perfil.php">Mi Perfil</a>
                                    <a class="dropdown-item" href="<?php echo $base_url; ?>../login/cerrar_sesion.php">Cerrar Sesión</a>
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
                    <li class="nav-item <?php echo ($activePage == 'index.php' ? 'active' : ''); ?>">
                        <a href="<?php echo $base_url; ?>index.php">
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
                    <?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
                    <li class="nav-item <?php echo (in_array($activePage, $estudiantesPages) ? 'active' : ''); ?>">
                        <a data-toggle="collapse" href="#base">
                            <i class="fas fa-child"></i>
                            <p>Estudiantes</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse <?php echo (in_array($activePage, $estudiantesPages) ? 'show' : ''); ?>" id="base">
                            <ul class="nav nav-collapse">
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                                <li class="<?php echo ($activePage == 'nuevo_estudiante.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Estudiantes/Tecnicatura/ABM_estudiante/nuevo_estudiante.php">
                                        <span class="sub-item">Nuevo Estudiante</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                                <li class="<?php echo ($activePage == 'inscripcion_FP.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>inscripcion_FP.php">
                                        <span class="sub-item">Nuevo Estudiante FP</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="<?php echo ($activePage == 'lista_estudiantes.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Estudiantes/Tecnicatura/lista_estudiantes.php">
                                        <span class="sub-item">Lista Estudiantes</span>
                                    </a>
                                </li>
                               
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                                <li class="<?php echo ($activePage == 'lista_fp.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>lista_fp.php">
                                        <span class="sub-item">Lista Estudiantes FP</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="<?php echo ($activePage == 'informe_asistencia_tecnicaturas.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Informes/informe_asistencia_tecnicaturas.php">
                                        <span class="sub-item">Informe de Asistencias Técnicaturas</span>
                                    </a>
                                </li>
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                                <li class="<?php echo ($activePage == 'proximamente.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>proximamente.php">
                                        <span class="sub-item">Informe de Asistencias FP</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="<?php echo ($activePage == 'informe_lista_estudiantes.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>.//Informes/informe_lista_estudiantes.php">
                                        <span class="sub-item">Imprimir Lista de Estudiantes Técnicaturas</span>
                                    </a>
                                </li>
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                                <li class="<?php echo ($activePage == 'informe_lista_estudiantesFP.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Estudiantes/FP/informesFP/informe_lista_estudiantesFP.php">
                                        <span class="sub-item">Imprimir Lista de Estudiantes FP</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
                                <li class="<?php echo ($activePage == 'falta_justificada.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>falta_justificada.php">
                                        <span class="sub-item">Justificar Falta</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="<?php echo ($activePage == 'estudiantes_retirados.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Estudiantes/Tecnicatura/Retirados/estudiantes_retirados.php">
                                        <span class="sub-item">Retirados Antes de Tiempo</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
                    <li class="nav-item <?php echo (in_array($activePage, $asistenciaPages) ? 'active' : ''); ?>">
                        <a data-toggle="collapse" href="#takeAttendance">
                            <i class="fas fa-pen-square"></i>
                            <p>Tomar Asistencia</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse <?php echo (in_array($activePage, $asistenciaPages) ? 'show' : ''); ?>" id="takeAttendance">
                            <ul class="nav nav-collapse">
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
                                <li class="<?php echo ($activePage == 'index_asistencia.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>index_asistencia.php">
                                        <span class="sub-item">Estudiantes Técnicaturas</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                                <li class="<?php echo ($activePage == 'ver_FPS.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Estudiantes/FP/ver_FPS.php">
                                        <span class="sub-item">Estudiantes FP</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
                    <li class="nav-item <?php echo (in_array($activePage, $verAsistenciaPages) ? 'active' : ''); ?>">
                        <a data-toggle="collapse" href="#viewAttendance">
                            <i class="fas fa-clipboard-list"></i>
                            <p>Ver Asistencia</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse <?php echo (in_array($activePage, $verAsistenciaPages) ? 'show' : ''); ?>" id="viewAttendance">
                            <ul class="nav nav-collapse">
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
                                <li class="<?php echo ($activePage == 'ver_carreras.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>ver_carreras.php">
                                        <span class="sub-item">Estudiantes Técnicaturas</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                                <li class="<?php echo ($activePage == 'ver_asistenciaFPS.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Estudiantes/FP/ver_asistenciaFPS.php">
                                        <span class="sub-item">Estudiantes FP</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                    <li class="nav-item <?php echo (in_array($activePage, $utilidadesAdminPages) ? 'active' : ''); ?>">
                        <a data-toggle="collapse" href="#alumnos">
                            <i class="fas fa-file-alt"></i>
                            <p>Utilidades<br> Administrativas</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse <?php echo (in_array($activePage, $utilidadesAdminPages) ? 'show' : ''); ?>" id="alumnos">
                            <ul class="nav nav-collapse">
                                <?php if ($rolUsuario == '1'): ?>
                                 <li class="<?php echo ($activePage == 'asignar_cargo.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>asignar_cargo.php">
                                        <span class="sub-item">Asignar Cargo a Personal</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if ($rolUsuario == '1'): ?>
                                <li class="<?php echo ($activePage == 'gestionar_mesas_finales.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>gestionar_mesas_finales.php">
                                        <span class="sub-item">Gestión de Mesas</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'ver_inscriptos_mesas.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>/ver_inscriptos_mesas.php">
                                        <span class="sub-item">Contador de Inscriptos a Mesas</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'proximamente.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>proximamente.php">
                                        <span class="sub-item">Nuevo Preceptor</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'proximamente.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>proximamente.php">
                                        <span class="sub-item">Lista de Preceptores</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'asignar_preceptor_panel.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Administracion/Preceptores/asignar_preceptor_panel.php">
                                        <span class="sub-item">Asignar carrera a Preceptor</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="<?php echo ($activePage == 'alta_docente.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Administracion/Profesores/alta_docente.php">
                                        <span class="sub-item">Alta Docentes</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'lista_profesores.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Administracion/Profesores/lista_profesores.php">
                                        <span class="sub-item">Lista de Docentes</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'materia_profesor.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Administracion/Profesores/materia_profesor.php">
                                        <span class="sub-item">Asignar Unidad Curricular a Docente</span>
                                    </a>
                                </li>
                                <?php if ($rolUsuario == '1'): ?>
                                <li class="<?php echo ($activePage == 'pagos.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Estudiantes/Tecnicatura/pagos.php">
                                        <span class="sub-item">Estado de Pagos de Estudiantes</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if ($rolUsuario == '1'): ?>
                                <li class="<?php echo ($activePage == 'pagos.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>aprobado_por_equivalencias.php">
                                        <span class="sub-item">Aprobado por equivalencias </span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if ($rolUsuario == '1'): ?>
                                <li class="<?php echo ($activePage == 'pagos.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>mesas_extraordinarias.php">
                                        <span class="sub-item">Situacion Extraordinaria</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if ($rolUsuario == '4' || $rolUsuario == '1' || $rolUsuario == '5'): ?>
                    <li class="nav-item <?php echo (in_array($activePage, $utilidadesDocentePages) ? 'active' : ''); ?>">
                        <a data-toggle="collapse" href="#newMenu">
                            <i class="fas fa-book"></i>
                            <p>Utilidades<br> del Docentes</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse <?php echo (in_array($activePage, $utilidadesDocentePages) ? 'show' : ''); ?>" id="newMenu">
                            <ul class="nav nav-collapse">
                                <li class="<?php echo ($activePage == 'pre_parciales.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>proximamente.php">
                                        <span class="sub-item">Gestión de Notas</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'pre_libro.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>pre_libro.php">
                                        <span class="sub-item">Libro de Temas</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if ($rolUsuario == '1' || $rolUsuario == '2' || $rolUsuario == '3'): ?>
                    <li class="nav-item <?php echo (in_array($activePage, $utilidadesPreceptorPages) ? 'active' : ''); ?>">
                        <a data-toggle="collapse" href="#preceptorUtilities">
                            <i class="fas fa-toolbox"></i>
                            <p>Utilidades<br> del Preceptor</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse <?php echo (in_array($activePage, $utilidadesPreceptorPages) ? 'show' : ''); ?>" id="preceptorUtilities">
                            <ul class="nav nav-collapse">
                                <li class="<?php echo ($activePage == 'pre_lista_promocionados.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>Administracion/Profesores/pre_lista_promocionados.php">
                                        <span class="sub-item">Actas Volantes Promocionados</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'actas_volante_estudiantes_regulares.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>actas_volante_estudiantes_regulares.php">
                                        <span class="sub-item">Actas Volantes Regulares</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'actas_volante_estudiantes_libres.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>actas_volante_estudiantes_libres.php">
                                        <span class="sub-item">Actas Volantes Libres</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'pre_nota_final.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>pre_nota_final.php">
                                        <span class="sub-item">Cargar Notas de Mesas</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'carga_notas_pendientes.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>carga_notas_pendientes.php">
                                        <span class="sub-item">Notas Pendientes (Individual)</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'tabla_notas_pendientes.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>tabla_notas_pendientes.php">
                                        <span class="sub-item">Tabla Notas Pendientes</span>
                                    </a>
                                </li>
                                <li class="<?php echo ($activePage == 'tabla_mesas_pendientes.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>tabla_mesas_pendientes.php">
                                        <span class="sub-item">Tabla Mesas Pendientes</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if ($rolUsuario == '1' || $rolUsuario == '2'): ?>
                    <li class="nav-item <?php echo (in_array($activePage, $preinscriptosPages) ? 'active' : ''); ?>">
                        <a data-toggle="collapse" href="#preinscriptos">
                            <i class="fas fa-user-plus"></i>
                            <p>Preinscriptos</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse <?php echo (in_array($activePage, $preinscriptosPages) ? 'show' : ''); ?>" id="preinscriptos">
                            <ul class="nav nav-collapse">
                                <li class="<?php echo ($activePage == 'lista_pre_inscriptos.php' ? 'active' : ''); ?>">
                                    <a href="<?php echo $base_url; ?>lista_pre_inscriptos.php">
                                        <span class="sub-item">Lista de Preinscriptos</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <!-- End Sidebar -->
    </div>
    
    <!--   Core JS Files   -->
<script src="<?php echo $base_url; ?>assets/js/core/jquery.3.2.1.min.js"></script>

<script src="<?php echo $base_url; ?>assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="<?php echo $base_url; ?>assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>


<!-- jQuery Scrollbar -->
<script src="<?php echo $base_url; ?>assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Azzara JS -->
<script src="<?php echo $base_url; ?>assets/js/ready.min.js"></script>
</body>
</html>
