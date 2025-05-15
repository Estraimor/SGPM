<?php
include 'layout.php'; 


// Obtener las carreras disponibles
$rolUsuario = $_SESSION['roles'];
$profesorId = $_SESSION['id'];

if ($rolUsuario == 1) {
    $sql = "SELECT DISTINCT idCarrera, nombre_carrera FROM carreras";
} else {
    $sql = "SELECT DISTINCT c.idCarrera, c.nombre_carrera
            FROM carreras c
            INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
            WHERE m.profesor_idProrfesor = '$profesorId'";
}
$result = mysqli_query($conexion, $sql);
?>

<div class="contenido">
    <div class="contenido-int">
        <div class="instrucciones">
            <p>
                Para cargar el libro de tema, seleccione la carrera, el curso, la comisión, el año y, finalmente,
                la unidad curricular correspondiente. Asegúrese de completar todos los campos.
            </p>
        </div>

        <h2 class="titulo-formulario">Seleccione Carrera, Curso, Comisión, Año y Unidad Curricular</h2>

        <form action="pre_vista_libro_tema.php" method="post" onsubmit="return validarSeleccion();" class="formulario">
            <!-- Seleccionar Carrera -->
            <label for="selectCarrera" class="form-label">Carrera:</label>
            <select id="selectCarrera" name="carrera" class="form-select">
                <option value="">Selecciona una carrera</option>
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <option value="<?php echo $row['idCarrera']; ?>">
                        <?php echo $row['nombre_carrera']; ?>
                    </option>
                <?php } ?>
            </select>
            <br><br>

            <!-- Seleccionar Curso -->
            <label for="selectCurso" class="form-label">Curso:</label>
            <select id="selectCurso" name="curso" class="form-select">
                <option value="">Selecciona un curso</option>
            </select>
            <br><br>

            <!-- Seleccionar Comisión -->
            <label for="selectComision" class="form-label">Comisión:</label>
            <select id="selectComision" name="comision" class="form-select">
                <option value="">Selecciona una comisión</option>
            </select>
            <br><br>

            <!-- Seleccionar Año -->
            <label for="selectAnio" class="form-label">Año:</label>
            <select id="selectAnio" name="anio" class="form-select">
                <option value="">Selecciona un año</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
            </select>
            <br><br>

            <!-- Seleccionar Materia: se mostrará como lista de checkboxes -->
            <label class="form-label">Unidad Curricular:</label>
            <div id="materiaContainer" class="materia-checkbox-list">
                <!-- Aquí se cargarán dinámicamente los checkboxes de materias -->
            </div>
            <br><br>

            <button type="submit" class="btn-submit">Confirmar</button>
        </form>
    </div>
</div>


<script>
    // Al cambiar la Carrera, se cargan los Cursos asociados
    $("#selectCarrera").change(function(){
        let carreraId = $(this).val();
        // Reiniciar selects y la lista de materias
        $("#selectCurso").html('<option value="">Selecciona un curso</option>');
        $("#selectComision").html('<option value="">Selecciona una comisión</option>');
        $("#materiaContainer").html('');

        if(carreraId != "") {
            $.ajax({
                url: "obtener_datos_profe.php",
                type: "POST",
                data: { tipo: "curso", carrera_id: carreraId },
                dataType: "json",
                success: function(data) {
                    if (data.error) {
                        alert("Error: " + data.error);
                    } else {
                        $.each(data, function(index, item) {
                            $("#selectCurso").append('<option value="'+item.idCursos+'">'+item.curso+'</option>');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert("Error al obtener los cursos.");
                }
            });
        }
    });

    // Al cambiar el Curso, se cargan las Comisiones asociadas
    $("#selectCurso").change(function(){
        let carreraId = $("#selectCarrera").val();
        let cursoId   = $(this).val();
        // Reiniciar comisiones y materias
        $("#selectComision").html('<option value="">Selecciona una comisión</option>');
        $("#materiaContainer").html('');

        if(carreraId != "" && cursoId != "") {
            $.ajax({
                url: "obtener_datos_profe.php",
                type: "POST",
                data: { tipo: "comision", carrera_id: carreraId, curso_id: cursoId },
                dataType: "json",
                success: function(data) {
                    if (data.error) {
                        alert("Error: " + data.error);
                    } else {
                        $.each(data, function(index, item) {
                            $("#selectComision").append('<option value="'+item.idComisiones+'">'+item.comision+'</option>');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert("Error al obtener las comisiones.");
                }
            });
        }
    });

    // Al cambiar la Comisión, se cargan las Materias (Unidades Curriculares) asociadas
    // tomando en cuenta el año seleccionado
    $("#selectComision, #selectAnio").change(function(){
        let carreraId  = $("#selectCarrera").val();
        let cursoId    = $("#selectCurso").val();
        let comisionId = $("#selectComision").val();
        let anio       = $("#selectAnio").val();

        // Si cualquiera está vacío, no hacemos nada todavía
        if(carreraId == "" || cursoId == "" || comisionId == "" || anio == "") {
            return;
        }

        $("#materiaContainer").html('');

        $.ajax({
            url: "obtener_datos_profe.php",
            type: "POST",
            data: { 
                tipo: "materia", 
                carrera_id: carreraId, 
                curso_id: cursoId, 
                comision_id: comisionId,
                anio: anio  // Aquí pasamos el año
            },
            dataType: "json",
            success: function(data) {
                if (data.error) {
                    alert("Error: " + data.error);
                } else {
                    $.each(data, function(index, item) {
                        let checkbox = '<div class="form-checkbox-item">' +
  '<input type="checkbox" class="form-checkbox" name="materias[]" value="'+item.idMaterias+'"> ' +
  '<label>'+item.Nombre+'</label>' +
'</div>';
                        $("#materiaContainer").append(checkbox);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert("Error al obtener las materias.");
            }
        });
    });

    // Función de validación del formulario
    function validarSeleccion() {
        if (
            $("#selectCarrera").val() == "" ||
            $("#selectCurso").val() == "" ||
            $("#selectComision").val() == "" ||
            $("#selectAnio").val() == ""
        ) {
            alert("Por favor, selecciona la carrera, curso, comisión y año.");
            return false;
        }
        
        // Validar que se haya seleccionado al menos una materia
        if ($("#materiaContainer input[type='checkbox']:checked").length === 0) {
            alert("Por favor, selecciona al menos una materia.");
            return false;
        }
        return true;
    }
</script>

<!-- Estilos elegantes y minimalistas -->
<style>
    
    
    /* Estilos generales para el contenedor principal */
    .contenido-int {
        padding: 20px;
        background-color: #ffffff;
        border: 1px solid #f3545d;
        border-radius: 5px;
        max-width: 600px;
        margin: 20px auto;
    }
    
    /* Título del formulario */
    .titulo-formulario {
        color: #333;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        margin-bottom: 15px;
    }
    
    /* Formulario */
    .formulario {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
    
    /* Etiquetas */
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }
    
    /* Selectores */
    .form-select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }
    
    /* Contenedor de la lista de checkboxes para materias */
    .materia-checkbox-list {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #fff;
        max-height: 200px;
        overflow-y: auto;
    }
    
    /* Cada ítem de checkbox */
    .form-checkbox-item {
        margin-bottom: 10px;
    }
    
    /* Checkboxes */
    .form-checkbox {
        margin-right: 5px;
    }
    
    /* Botón */
    .btn-submit {
        padding: 10px 20px;
        background-color: #f3545d;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
    }
    
    .btn-submit:hover {
        background-color: #c54348;
    }
    /* Estilos exclusivos para la sección de Unidad Curricular */
.materia-checkbox-list {
    background-color: #f9f9f9;   /* Fondo suave */
    border: 1px solid #ddd;      /* Borde sutil */
    border-radius: 8px;          /* Bordes redondeados */
    padding: 15px;               /* Espaciado interno */
    max-height: 250px;           /* Altura máxima con scroll si es necesario */
    overflow-y: auto;            /* Scroll vertical */
    box-shadow: 0px 4px 8px rgba(0,0,0,0.05); /* Sombra ligera para dar profundidad */
}

.materia-checkbox-list .form-checkbox-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.materia-checkbox-list .form-checkbox-item:last-child {
    border-bottom: none;
}

.materia-checkbox-list .form-checkbox {
    width: 18px;
    height: 18px;
    margin-right: 10px;
    cursor: pointer;
}

.materia-checkbox-list .form-checkbox-item label {
    font-size: 16px;
    color: #333;
    margin: 0;
}
</style>
