<?php
include './layout.php';
session_start();

$rol = $_SESSION['roles'];
$idProfesor = $_SESSION['id'];
?>

<div class="contenido">
    <h2>Cambio de Comisión</h2>

    <form id="form-cambio-comision">
        <label for="carrera">Carrera:</label>
        <select id="carrera" name="carrera" required>
            <option value="">Seleccione una carrera</option>
            <?php
            if ($rol == 1) {
                $res = mysqli_query($conexion, "SELECT DISTINCT idCarrera, nombre_carrera FROM carreras c");
            } else {
                $res = mysqli_query($conexion, "SELECT DISTINCT c.idCarrera, c.nombre_carrera 
                    FROM preceptores p 
                    JOIN carreras c ON c.idCarrera = p.carreras_idCarrera 
                    WHERE p.profesor_idProrfesor = $idProfesor");
            }
            while ($row = mysqli_fetch_assoc($res)) {
                echo "<option value='{$row['idCarrera']}'>{$row['nombre_carrera']}</option>";
            }
            ?>
        </select>

        <label for="busqueda">Buscar estudiante:</label>
        <input type="text" id="busqueda" placeholder="DNI, legajo o apellido/nombre" required>

        <div id="resultado"></div>
    </form>
</div>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Buscar alumno al tipear
$('#busqueda').on('input', function () {
    let texto = $(this).val();
    let carrera = $('#carrera').val();

    if (texto.length >= 3 && carrera !== '') {
        $.ajax({
            url: './config_cambio_comision/buscar_estudiante.php',
            method: 'POST',
            data: {
                busqueda: texto,
                carrera: carrera
            },
            success: function (respuesta) {
                $('#resultado').html(respuesta);
            }
        });
    } else {
        $('#resultado').html('');
    }
});
</script>

<script>
$(document).on('click', '.btn-cambiar', function () {
    const id = $(this).data('id');
    const select = $(`.comision-nueva[data-id='${id}']`);
    const nuevaComision = select.val();
    const legajo = select.data('legajo');

    if (!nuevaComision) {
        alert("Por favor, seleccioná una nueva comisión.");
        return;
    }

    if (confirm("¿Estás seguro de cambiar la comisión del alumno?")) {
        const datos = `idInscripcion=${id}&nuevaComision=${nuevaComision}&legajo=${legajo}`;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "./config_cambio_comision/guardar_cambio_comision.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            // Mostrar el alert y después redirigir
            alert(xhr.responseText);
            // Redirigir luego de que se cierra el alert
            window.location.href = "cambio_comision_matriculacion.php"; // o la página que quieras
        };

        xhr.onerror = function () {
            alert("Error de conexión con el servidor.");
        };

        xhr.send(datos);
    }
});
</script>


