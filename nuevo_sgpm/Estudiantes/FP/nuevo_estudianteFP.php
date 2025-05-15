<?php
include '../../layout.php'
?>
<div class="contenido">
<?php
    $sql_carreras = "SELECT * FROM carreras c WHERE c.idCarrera IN ('8','14','15','64','65')";
    $resultado_carreras = mysqli_query($conexion, $sql_carreras);
    $carreras = array(); // Almacenar las carreras en un array

    while ($informacion_carrera = mysqli_fetch_assoc($resultado_carreras)) {
        $carreras[] = $informacion_carrera; // Añadir cada carrera al array
    }
?>
    <form action="guardar_alumnofp.php" method="post" class="form-container">
    <h2>Registro de FP</h2>
        <?php
        // Consulta para obtener el último número de legajo
        $sql_legajo = "SELECT MAX(legajo_afp) AS max_legajo FROM alumnos_fp";
        $resultado_legajo = $conexion->query($sql_legajo);
        $fila_legajo = $resultado_legajo->fetch_assoc();
        $nuevo_legajo = $fila_legajo['max_legajo'] + 1; // Nuevo legajo es el último más uno
    ?>
    <!-- Campo de legajo con el valor obtenido de la base de datos -->
	<div class="row">
		<input type="text" name="apellido" placeholder="Apellido" class="form-container__input half" autocomplete="off">
        <input type="text" name="nombre" placeholder="Nombre" class="form-container__input half" autocomplete="off">
	</div>
	<div class="row">
		<input type="number" name="dni" placeholder="DNI" class="form-container__input half" autocomplete="off">
		<input type="date" name="edad" placeholder="Fecha de Nacimiento" class="form-container__input half" autocomplete="off">
	</div>
	<div class="row">
		<input type="number" name="celular" placeholder="Celular" class="form-container__input half" autocomplete="off">
		<input type="text" name="legajo" placeholder="N° Legajo"  value="<?php echo $nuevo_legajo; ?>" class="form-container__input half" >
	</div>
	<div class="row">
	<input type="text" name="trabaja" placeholder="Trabaja" class="form-container__input half" autocomplete="off">
	<input type="text" name="observaciones" placeholder="Observaciones" class="form-container__input half" autocomplete="off">
	</div>
        <select name="FP1" class="form-container__input half">
        <?php foreach ($carreras as $carrera) { ?>
        <option value="65" hidden selected>Selecciona un curso</option>
            <option value="<?php echo $carrera['idCarrera'] ?>"><?php echo $carrera['nombre_carrera'] ?></option>
            <?php } ?>
        </select>
        <select name="FP2" class="form-container__input half">
        <?php foreach ($carreras as $carrera) { ?>
            <option value="65" hidden selected>Selecciona un curso</option>
            <option value="<?php echo $carrera['idCarrera'] ?>"><?php echo $carrera['nombre_carrera'] ?></option>
            <?php } ?>
        </select>
        <select name="FP3" class="form-container__input half">
        <?php foreach ($carreras as $carrera) { ?>
            <option value="65" hidden selected>Selecciona un curso</option>
            <option value="<?php echo $carrera['idCarrera'] ?>"><?php echo $carrera['nombre_carrera'] ?></option>
            <?php } ?>
        </select>
        <select name="FP4" class="form-container__input half">
        <?php foreach ($carreras as $carrera) { ?>
            <option value="65" hidden selected>Selecciona un curso</option>
            <option value="<?php echo $carrera['idCarrera'] ?>"><?php echo $carrera['nombre_carrera'] ?></option>
            <?php } ?>
        </select>
        <input type="submit" name="enviar" value="Enviar" class="form-container__input_FP">
    </form>
    </div>
</body>
</html>