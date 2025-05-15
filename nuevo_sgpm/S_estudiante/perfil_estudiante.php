<?php
include'layout_estudiante.php'
?>

        <div class="contenido">
<div class="perfil-container">
    <h2>Perfil del Estudiante</h2>

    <?php 
    $sql_alumno = "SELECT * FROM alumno WHERE legajo = '{$_SESSION["id"]}'";
    $query_alumno = mysqli_query($conexion, $sql_alumno);

    if (mysqli_num_rows($query_alumno) > 0) {
        $row = mysqli_fetch_assoc($query_alumno);
    ?>

    <form action="./perfil_estudiante/actualizar_perfil_estu.php" method="POST">
        <label for="nombre_alumno">Nombre:</label>
        <div class="input-group">
            <input type="text" name="nombre_alumno" value="<?php echo $row['nombre_alumno']; ?>" required>
        </div>

        <label for="apellido_alumno">Apellido:</label>
        <div class="input-group">
            <input type="text" name="apellido_alumno" value="<?php echo $row['apellido_alumno']; ?>" required>
        </div>

        <label for="dni_alumno">DNI:</label>
        <div class="input-group">
            <input type="number" name="dni_alumno" value="<?php echo $row['dni_alumno']; ?>" required>
        </div>

        <label for="celular">Celular:</label>
        <div class="input-group">
            <input type="text" name="celular" value="<?php echo $row['celular']; ?>" required>
        </div>

        <label for="email">Email:</label>
        <div class="input-group">
            <input type="email" name="email" value="<?php echo $row['correo']; ?>" required>
        </div>

        <label for="usuario">Usuario:</label>
        <div class="input-group">
            <input type="text" name="usuario" value="<?php echo $row['usu_alumno']; ?>" required>
        </div>

        <label for="password">Contraseña:</label>
        <div class="input-group">
            <input type="text" name="password" value="<?php echo $row['pass_alumno']; ?>" required>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Actualizar Datos</button>
        </div>
    </form>

    <?php
    } else {
        echo "No se encontraron datos del alumno.";
    }
    ?>
</div>
</div>

<!-- Estilos CSS (se mantienen similares) -->
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

    form label {
        font-size: 14px;
        color: #333;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
    }

    .input-group input {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        outline: none;
        transition: all 0.3s ease;
    }

    .btn-group {
        display: flex;
        justify-content: center;
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
    }

    .btn-primary:hover {
        background-color: #ff545d !important;
    }
</style>
    
	


</body>
</html>

