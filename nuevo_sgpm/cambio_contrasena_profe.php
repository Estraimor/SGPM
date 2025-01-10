<?php
session_start();
if (empty($_SESSION["id"])) {
    header('Location: login_es.php');
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
    header("Location: login_es.php");
    exit;
} else {
    $_SESSION['time'] = time();
}

if ($_SESSION['contraseña'] === '0123456789') {
    // Mostrar formulario de cambio de contraseña
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="assets/img/Logo ISPM 2 transparante.png" type="image/x-icon"/>
 <title>Cambiar Contraseña</title>
 <link rel="stylesheet" href="./S_estudiante/s_estudiante.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
	
<div class="contenido">
        <img src="assets/img/Logo ISPM 2 transparante.png">
        <h2>Cambiar Contraseña</h2>
        <form method="POST" action="guardar_cambio_contrasena_profe.php" onsubmit="return validatePasswords()">
            <div class="form-group">
                <label for="nueva">Nueva Contraseña</label>
                <input type="password" id="nueva" name="nueva" required>
                <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('nueva', this)"></i>
            </div>
            <div class="form-group">
                <label for="confirmar">Confirmar Nueva Contraseña</label>
                <input type="password" id="confirmar" name="confirmar" required>
                <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('confirmar', this)"></i>
                <span class="error-message" id="error-message">Las contraseñas no coinciden</span>
            </div>
            <div class="form-group">
                <button type="submit" name="cambiar">Confirmar</button>
            </div>
        </form>
    </div>
<script>
  function validatePasswords() {
            var nueva = document.getElementById('nueva').value;
            var confirmar = document.getElementById('confirmar').value;
            var errorMessage = document.getElementById('error-message');

            if (nueva !== confirmar) {
                errorMessage.style.display = 'block';
                return false;
            } else {
                errorMessage.style.display = 'none';
                return true;
            }
        }

        function togglePassword(inputId, toggleIcon) {
            var input = document.getElementById(inputId);
            var inputType = input.type;
            input.type = inputType === 'password' ? 'text' : 'password';
            toggleIcon.classList.toggle('fa-eye');
            toggleIcon.classList.toggle('fa-eye-slash');
            
            // Vuelve a enfocar el input después de cambiar el tipo
            input.focus();
        }
</script>


</body>
</html>
<?php }else{header('Location: index.php');} ?>
