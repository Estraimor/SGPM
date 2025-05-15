<?php
include'../../layout.php'
?>

<div class="contenido">
    <style>
        .contadores {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .contadores p {
            margin: 0 15px;
            padding: 5px;
            font-size: 16px;
            color: #f3545d; /* Color rojo */
            font-weight: bold;
        }
    </style>

    <?php
    $profesor_id = $_SESSION["id"];
    $sql1 = "SELECT a.*, c.nombre_carrera
         FROM inscripcion_asignatura ia
         INNER JOIN alumno a ON ia.alumno_legajo = a.legajo
         INNER JOIN preceptores p ON p.carreras_idCarrera = ia.carreras_idCarrera
         INNER JOIN carreras c ON ia.carreras_idCarrera = c.idCarrera
         WHERE ia.año_inscripcion = year(now())
         GROUP BY ia.alumno_legajo"; 
    
    $query1 = mysqli_query($conexion, $sql1);

    // Inicializamos los contadores
    $contadorAcompañanteTerapeutico = 0;
    $contadorEnfermeria = 0;
    $contadorComercializacionMarketing = 0;
    $contadorAutomatizacionRobotica = 0;
    $totalEstudiantes = 0;

    // Recorremos los resultados y calculamos los contadores
    while ($datos = mysqli_fetch_assoc($query1)) {
        $nombre_carrera = trim($datos['nombre_carrera']); // Eliminar posibles espacios en blanco

        // Condicionales para incrementar los contadores según la carrera
        if (preg_match('/acomp/i', $nombre_carrera)) {
            $contadorAcompañanteTerapeutico++;
        } elseif (preg_match('/enfer/i', $nombre_carrera)) {
            $contadorEnfermeria++;
        } elseif (preg_match('/comercial/i', $nombre_carrera) || preg_match('/marketing/i', $nombre_carrera)) {
            $contadorComercializacionMarketing++;
        } elseif (preg_match('/auto/i', $nombre_carrera) || preg_match('/robot/i', $nombre_carrera)) {
            $contadorAutomatizacionRobotica++;
        }

        // Incrementamos el contador total de estudiantes
        $totalEstudiantes++;
    }
    ?>

    <!-- Contadores arriba de la tabla con los nuevos estilos -->
    <div class="contadores">
        <p>Acompañante Terapéutico: <?php echo $contadorAcompañanteTerapeutico; ?></p>
        <p>Enfermería: <?php echo $contadorEnfermeria; ?></p>
        <p>Comercialización Marketing: <?php echo $contadorComercializacionMarketing; ?></p>
        <p>Automatización Robótica: <?php echo $contadorAutomatizacionRobotica; ?></p>
        <p>Total estudiantes: <?php echo $totalEstudiantes; ?></p>
    </div>

    <!-- Tabla con los estudiantes -->
    <div id="tablaContainerEstudiantes">
        <table id="tabla">
            <thead>
                <tr>
                    <th class="legajo">Legajo</th>
                    <th class="apellido">Apellido</th>
                    <th class="nombre">Nombre</th>
                    <th class="dni">DNI</th>
                    <th class="celular">Celular</th>
                    <th class="carrera">Carrera</th>
                    <th class="acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Reseteamos el query y volvemos a iterar para llenar la tabla
                $query1 = mysqli_query($conexion, $sql1);
                while ($datos = mysqli_fetch_assoc($query1)) {
                    ?>
                    <tr>
                        <td><?php echo $datos['legajo']; ?></td>
                        <td><?php echo $datos['apellido_alumno']; ?></td>
                        <td><?php echo $datos['nombre_alumno']; ?></td>
                        <td><?php echo $datos['dni_alumno']; ?></td>
                        <td><?php echo $datos['celular']; ?></td>
                        <td><?php echo $datos['nombre_carrera']; ?></td>
                        <td>
                            <a href="./ABM_estudiante/modificar_estudianteT.php?legajo=<?php echo $datos['legajo']; ?>" class="modificar-button">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a href="#" onclick="return confirmarBorrado('<?php echo $datos['legajo']; ?>')" class="borrar-button">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <a href="info_alumnoT.php?legajo=<?php echo $datos['legajo']; ?>" class="accion-button">
                                <i class="fas fa-exclamation"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    
// dataTables de Alumnos //
var myTable = document.querySelector("#tabla");
var dataTable = new DataTable(tabla);
    function confirmarBorrado(legajo) {
    var respuesta = confirm("¿Estás seguro de que quieres borrar este alumno?");
    if (respuesta) {
        // Realizar el borrado lógico directamente sin redirección previa
        window.location.href = "./ABM_estudiante/Borrado_logico_alumno.php?legajo=" + legajo;
    }
    return false; // Evita que el navegador siga el enlace en caso de cancelar la confirmación
}
</script>

</body>
</html>

