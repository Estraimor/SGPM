<?php
include'layout.php'
?>

<div class="contenido">
<div class="perfil-container">
    <h2>Perfil del Docente</h2>

    <?php 
    $sql_profe = "SELECT * FROM profesor WHERE idProrfesor = '{$_SESSION["id"]}'";
    $query_nombre = mysqli_query($conexion, $sql_profe);

    if (mysqli_num_rows($query_nombre) > 0) {
        $row = mysqli_fetch_assoc($query_nombre);
    ?>

    <form action="guardar_perfil.php" method="POST" enctype="multipart/form-data">
	<?php
// Obtiene el contenido LONGBLOB de la base de datos
$avatarData = base64_encode($row['avatar']);
?>
	<div class="avatar-container">
    <!-- Muestra la imagen de perfil obtenida desde la base de datos en formato base64 -->
    <img id="avatar-preview" 
         src="data:image/jpeg;base64,<?php echo $avatarData; ?>" 
         alt="Avatar" 
         class="avatar-icon" 
         style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
    
    <!-- Botón para cambiar la foto -->
    <button type="button" class="btn-secondary" onclick="document.getElementById('file-input').click()" style="margin-bottom:10px;">Cambiar foto</button>
    
    <!-- Botón para eliminar la foto -->
    <button type="button" class="btn-secondary" onclick="eliminarAvatar()">Eliminar foto</button>

    <!-- Campo oculto para indicar eliminación de avatar -->
    <input type="hidden" name="eliminar_avatar" id="eliminar_avatar" value="0">
    
    <!-- Input de archivo oculto -->
    <input type="file" id="file-input" name="imagen" accept="image/*" style="display: none;" onchange="previewAvatar(event)">
</div>

        <label for="nombre_profe">Nombre:</label>
        <div class="input-group">
            <input type="text" name="nombre_profe" value="<?php echo $row['nombre_profe']; ?>" disabled>
            <i class="fas fa-pencil-alt edit-icon" onclick="habilitarEdicion(this)"></i>
        </div>

        <label for="apellido_profe">Apellido:</label>
        <div class="input-group">
            <input type="text" name="apellido_profe" value="<?php echo $row['apellido_profe']; ?>" disabled>
            <i class="fas fa-pencil-alt edit-icon" onclick="habilitarEdicion(this)"></i>
        </div>

        <label for="dni_profe">DNI:</label>
        <div class="input-group">
            <input type="number" name="dni_profe" value="<?php echo $row['dni_profe']; ?>" disabled>
            <i class="fas fa-pencil-alt edit-icon" onclick="habilitarEdicion(this)"></i>
        </div>

        <label for="celular">Celular:</label>
        <div class="input-group">
            <input type="text" name="celular" value="<?php echo $row['celular']; ?>" disabled>
            <i class="fas fa-pencil-alt edit-icon" onclick="habilitarEdicion(this)"></i>
        </div>

        <label for="email">Email:</label>
        <div class="input-group">
            <input type="email" name="email" value="<?php echo $row['email']; ?>" disabled>
            <i class="fas fa-pencil-alt edit-icon" onclick="habilitarEdicion(this)"></i>
        </div>

        <label for="usuario">Usuario:</label>
        <div class="input-group">
            <input type="text" name="usuario" value="<?php echo $row['usuario']; ?>" disabled>
            <i class="fas fa-pencil-alt edit-icon" onclick="habilitarEdicion(this)"></i>
        </div>

		<!-- Input de contraseña modificado -->
<label for="pass">Contraseña:</label>
<div class="input-group">
    <input type="text" name="pass" id="pass" value="<?php echo $row['pass']; ?>" disabled>
    <i class="fas fa-pencil-alt edit-icon" onclick="habilitarEdicion(this)"></i>
</div>

        <label for="Titulo">Título:</label>
        <div class="input-group">
            <input type="text" name="Titulo" value="<?php echo $row['Titulo']; ?>" disabled>
            <i class="fas fa-pencil-alt edit-icon" onclick="habilitarEdicion(this)"></i>
        </div>

        <label for="Direccion">Dirección:</label>
        <div class="input-group">
            <input type="text" name="Direccion" value="<?php echo $row['Direccion']; ?>" disabled>
            <i class="fas fa-pencil-alt edit-icon" onclick="habilitarEdicion(this)"></i>
        </div>

        <div class="btn-group">
            <button type="button" class="btn-secondary"><a href="./index.php">Menu Principal</a></button>
            <button type="submit" class="btn-primary">Actualizar Datos</button>
        </div>
    </form>

    <?php
    } else {
        echo "No se encontraron datos del profesor.";
    }
    ?>
</div>
</div>

<!-- Estilos CSS -->
<style>
    .perfil-container {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #ffffff;
    }

    .perfil-container h2 {
        color: #333;
        font-size: 24px;
        text-align: left;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .avatar-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }

    .avatar-icon {
        font-size: 60px;
        color: #888;
        margin-bottom: 10px;
    }

    form label {
        font-size: 14px;
        color: #333;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .input-group {
        display: flex;
        align-items: center;
        position: relative;
        margin-bottom: 15px;
    }

    .input-group input[type="text"],
    .input-group input[type="number"],
    .input-group input[type="email"],
    .input-group input[type="password"] {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        outline: none;
        transition: all 0.3s ease;
    }

    .input-group input:disabled {
        background-color: #f5f5f5;
        color: #888;
        cursor: not-allowed;
    }

    .input-group i.edit-icon,
    .input-group i.toggle-password {
        position: absolute;
        right: 10px;
        color: #f3545d;
        cursor: pointer;
        font-size: 16px;
        transition: color 0.3s ease;
    }

    .input-group i.edit-icon {
        right: 35px;
    }

    .input-group i:hover {
        color: #d93c4a;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        justify-content: space-between;
        margin-top: 20px;
    }

    .btn-primary {
        background-color: #f3545d !important;
        color: white !important;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s ease;
        text-align: center;
    }

    .btn-primary:hover {
        background-color: #ff545d !important;
    }

    .btn-secondary {
        background-color: #f3545d !important;
        color: white !important;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s ease;
        text-align: center;
    }

    .btn-secondary a {
        color: white;
        text-decoration: none;
    }

    .btn-secondary:hover {
		background-color: #ff545d !important;
    }
</style>

<!-- JavaScript -->
<script>
      function habilitarEdicion(icon) {
        const input = icon.previousElementSibling;
        // Cambia el estado de deshabilitado a habilitado y viceversa
        input.disabled = !input.disabled;

        // Enfocar el campo para indicar que está listo para ser editado
        if (!input.disabled) {
            input.focus();
        }
    }
	function eliminarAvatar() {
    const preview = document.getElementById('avatar-preview');
    preview.src = ''; // Aquí podrías colocar una imagen predeterminada si lo deseas
    document.getElementById('eliminar_avatar').value = '1'; // Marca para eliminar
}

function previewAvatar(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('avatar-preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);

        // Restablece la bandera de eliminar_avatar si se selecciona una nueva imagen
        document.getElementById('eliminar_avatar').value = '0';
    }
}
</script>



</body>
</html>

