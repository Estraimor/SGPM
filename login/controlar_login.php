<?php
session_start();
include '../conexion/conexion.php';

// Set inactivity limit in seconds
$inactivity_limit = 1200;

// Set timezone to Buenos Aires
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Define the log file path
$logFile = '../logs/inicio_sesiones.txt'; // Ruta del archivo de registro

// Check if user has submitted the form
if (isset($_POST['enviar'])) {
    if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
        $usuario = $conexion->real_escape_string($_POST['usuario']);
        $pass = $conexion->real_escape_string($_POST['password']);

        // Preparar consulta para evitar inyección SQL para profesor
        $stmt_profesor = $conexion->prepare("SELECT * FROM profesor WHERE usuario = ? AND pass = ?");
        $stmt_profesor->bind_param("ss", $usuario, $pass);
        $stmt_profesor->execute();
        $result_profesor = $stmt_profesor->get_result();

        if ($datos = $result_profesor->fetch_object()) {
            // Almacenar datos del usuario en variables de sesión
            $_SESSION["id"] = $datos->idProrfesor;
            $_SESSION["nombre"] = $datos->nombre_profe;
            $_SESSION["apellido"] = $datos->apellido_profe;
            $_SESSION["dni"] = $datos->dni_profe;
            $_SESSION["celular"] = $datos->celular;
            $_SESSION["usuario"] = $datos->usuario;
            $_SESSION["contraseña"] = $datos->pass;
            $_SESSION["roles"] = $datos->rol;
            $_SESSION['time'] = time();

            // Guardar registro de inicio de sesión en el archivo de texto
            $fechaHora = date("Y-m-d H:i:s"); // Formato de fecha y hora
            $registro = "Fecha y Hora: $fechaHora | ID Profesor: {$datos->idProrfesor}\n";

            file_put_contents($logFile, $registro, FILE_APPEND); // Escribir en el archivo de texto

            // Redireccionar según el rol del usuario usando switch case
            switch ($datos->rol) {
                case 1:
                    header("Location: ../nuevo_sgpm/index.php"); // Rol Administracion
                    break;
                case 2:
                    header("Location: ../nuevo_sgpm/index.php"); // Rol Programadores
                    break;
                case 3:
                    header("Location: ../nuevo_sgpm/index.php"); // Manu
                    break;
                case 4:
                    header("Location: ../nuevo_sgpm/index.php"); // Manu
                    break;
                case 5:
                    header("Location: ../nuevo_sgpm/index.php"); // Manu
                    break;
                default:
                    echo '<div class="alert alert-danger" role="alert">!! ACCESO DENEGADO!!</div>';
            }
        } else {
            // Preparar consulta para evitar inyección SQL para alumno
            $stmt_alumno = $conexion->prepare("SELECT * FROM alumno WHERE usu_alumno = ? AND pass_alumno = ?");
            $stmt_alumno->bind_param("ss", $usuario, $pass);
            $stmt_alumno->execute();
            $result_alumno = $stmt_alumno->get_result();

            if ($datos_alumno = $result_alumno->fetch_object()) {
                // Almacenar datos del usuario en variables de sesión
                $_SESSION["id"] = $datos_alumno->legajo;
                $_SESSION["nombre"] = $datos_alumno->nombre_alumno;
                $_SESSION["apellido"] = $datos_alumno->apellido_alumno;
                $_SESSION["dni"] = $datos_alumno->dni_alumno;
                $_SESSION["celular"] = $datos_alumno->celular;
                $_SESSION["usuario"] = $datos_alumno->usu_alumno;
                $_SESSION["contraseña"] = $datos_alumno->pass_alumno;
                $_SESSION['time'] = time();

                // Consulta para obtener el idCarrera, idCurso y idComision
                $stmt_datos = $conexion->prepare("SELECT carreras_idCarrera, Cursos_idCursos, Comisiones_idComisiones FROM inscripcion_asignatura WHERE alumno_legajo = ?");
                $stmt_datos->bind_param("i", $_SESSION["id"]);
                $stmt_datos->execute();
                $result_datos = $stmt_datos->get_result();

                if ($datos = $result_datos->fetch_object()) {
                    $_SESSION["idCarrera"] = $datos->carreras_idCarrera;
                    $_SESSION["idCurso"] = $datos->Cursos_idCursos;
                    $_SESSION["idComision"] = $datos->Comisiones_idComisiones;
                } else {
                    echo 'No se encontró la carrera, curso o comisión del alumno.';
                    exit();
                }

                header("Location: ../nuevo_sgpm/S_estudiante/cambio_contrasena.php"); // Redirigir al index del estudiante
            } else {
                echo '<div class="alert alert-danger" role="alert">!! DATOS INCORRECTOS!!</div>';
            }
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">!! HAY CAMPOS VACÍOS!!</div>';
    }
}
?>
