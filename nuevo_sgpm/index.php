<?php
include './layout.php'
?>
<div class="contenido">
    <div class="welcome-box">
        <?php 
             $sql_profe = "SELECT p.idProrfesor, p.nombre_profe, p.apellido_profe FROM profesor p
                WHERE p.idProrfesor = '{$_SESSION["id"]}'";
                $query_nombre = mysqli_query($conexion, $sql_profe);
                // Comprobar si la consulta devolvió algún resultado
                if (mysqli_num_rows($query_nombre) > 0) {
                    // Recorrer los resultados y hacer echo del nombre y apellido del profesor
                    while ($row = mysqli_fetch_assoc($query_nombre)) { ?>
                    <h2>Bienvenido/a al Sistema de Gestión del Politecnico Misiones N°1</h2>
                    <p>¡Estamos encantados de verte de nuevo!</p>
	                <p><?php echo "" . $row['nombre_profe'] . " " . $row['apellido_profe']; } ?></p>
        <?php
        } else {
            echo "No se encontraron datos del profesor.";
        }
?>
    </div>
</div>
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('../sw.js')
      .then(() => console.log('Service Worker registrado'))
      .catch((error) => console.error('Error al registrar el Service Worker', error));
  }
</script>