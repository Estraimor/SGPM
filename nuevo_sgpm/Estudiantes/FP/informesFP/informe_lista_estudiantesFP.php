<?php
include'../../../layout.php'
?>
<div class="contenido">
<h2 class="titulo-informe">Imprimir Lista de estudiantes de Formación Profesional</h2>
        <form action="../../../../indexs/generar_exel_alumnosFP.php" method="post">
            <?php
            $sql_mater = "SELECT * FROM carreras c
WHERE c.idCarrera IN ('8', '14', '15', '64') ";
            $peticion = mysqli_query($conexion, $sql_mater);
            ?>      
            <select name="carrera" class="seleccionar_carrera"> 
                <option hidden>Selecciona una carrera</option>
                <?php while ($informacion = mysqli_fetch_assoc($peticion)) { ?>
                    <option value="<?php echo $informacion['idCarrera'] ?>"><?php echo $informacion['nombre_carrera'] ?></option>
                <?php } ?>
            </select>
            <input type="submit" value="Generar Lista de Estudiantes" class="Generar-lista" style="width: 25%;">
        </form>
    </div>
</body>
</html>