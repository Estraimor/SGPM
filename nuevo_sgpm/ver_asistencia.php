<?php
include './layout.php';
$idCarrera = isset($_GET['carrera']) ? intval($_GET['carrera']) : 0;
$idCurso = isset($_GET['curso']) ? intval($_GET['curso']) : 0;
$idComision = isset($_GET['comision']) ? intval($_GET['comision']) : 0;
?>

<style>
    body {
        background: #f3f3f3;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        max-width: 1000px;
        margin: 30px auto;
        background: #fff;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    h2 {
        margin-bottom: 25px;
        color: #2d2d2d;
        font-weight: 600;
        text-align: center;
    }

    .date-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .date-container label {
        font-weight: 600;
        color: #333;
        font-size: 0.95rem;
    }

    .form-select,
    input[type="date"] {
        width: 100%;
        max-width: 400px;
        padding: 8px 12px;
        font-size: 0.95rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #fdfdfd;
        transition: border 0.2s ease-in-out;
    }

    .form-select:focus,
    input[type="date"]:focus {
        border-color: #e74c3c;
        outline: none;
    }

    #verDetalle {
        margin-top: 10px;
        background: #e74c3c;
        color: white;
        padding: 10px 22px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    #verDetalle:hover {
        background: #c0392b;
    }

    .table-comision-a {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
        font-size: 0.95rem;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    }

    .table-comision-a th,
    .table-comision-a td {
        padding: 14px;
        text-align: left;
        border: 1px solid #f1f1f1;
    }

    .table-comision-a th {
        background-color: #e74c3c;
        color: white;
        font-weight: bold;
    }

    .table-comision-a tr:nth-child(even) {
        background-color: #fff5f5;
    }

    .table-comision-a tr:hover {
        background-color: #ffecec;
    }

    #cuadrosResumen {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 25px;
        margin-bottom: 25px;
        justify-content: center;
    }

    #cuadrosResumen div {
        padding: 15px 22px;
        border-radius: 12px;
        font-weight: 600;
        color: #2b2b2b;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .resumen-verde {
    background-color: #d4edda;
}

.resumen-rojo {
    background-color: #f8d7da;
}

.resumen-amarillo {
    background-color: #fff3cd;
}

    /* Para hacer más visible cuando no hay datos */
    .table-comision-a td[colspan='5'] {
        text-align: center;
        font-style: italic;
        color: #666;
    }
</style>


<div class="contenido">
    <div class="container">
        <h2>Control de asistencia</h2>

        <div class="date-container">
            <label for="materia">Seleccione una materia:</label>
            <select id="materia" name="materia" class="form-select">
                <option value="">Seleccione una materia</option>
                <?php
                $sqlMaterias = "SELECT idMaterias, Nombre FROM materias WHERE carreras_idCarrera = $idCarrera AND cursos_idCursos = $idCurso AND comisiones_idComisiones = $idComision";
                $materias = mysqli_query($conexion, $sqlMaterias);
                while ($m = mysqli_fetch_assoc($materias)) {
                    echo "<option value='{$m['idMaterias']}'>{$m['Nombre']}</option>";
                }
                ?>
            </select>

            <label for="fecha">Seleccione una fecha:</label>
            <input type="date" id="fecha" name="fecha" disabled>
            <button id="verDetalle" style="display:none;" onclick="mostrarTabla()">Ver detalladamente</button>
        </div>

        <div id="cuadrosResumen"></div>

        <div class="table-container" id="tablaDetalle" style="display: none;">
            <table class="table-comision-a">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Apellido</th>
                        <th>Nombre</th>
                        <th>Materia</th>
                        <th>Asistencia</th>
                    </tr>
                </thead>
                <tbody id="asistenciaBody">
                    <!-- Se carga vía AJAX -->
                </tbody>
            </table>
        </div>

        <input type="hidden" id="materiaId" value="">
        <input type="hidden" id="carreraId" value="<?= $idCarrera ?>">
        <input type="hidden" id="cursoId" value="<?= $idCurso ?>">
        <input type="hidden" id="comisionId" value="<?= $idComision ?>">
    </div>
</div>

<script src="assets/js/core/jquery.3.2.1.min.js"></script>
<script>
   $("#materia").on("change", function () {
    const materiaId = $(this).val();

    if (materiaId) {
        $("#fecha").prop("disabled", false);

        // Si ya hay una fecha seleccionada, cargar resumen automáticamente
        const fechaSeleccionada = $("#fecha").val();
        if (fechaSeleccionada) {
            $("#fecha").trigger("change");
        }

    } else {
        $("#fecha").val("").prop("disabled", true);
        $("#cuadrosResumen").html("");
        $("#verDetalle").hide();
        $("#tablaDetalle").slideUp(); // Oculta tabla
    }
});


$("#fecha").on("change", function () {
    const carreraId = $("#carreraId").val();
    const cursoId = $("#cursoId").val();
    const comisionId = $("#comisionId").val();
    const materiaId = $("#materia").val();
    const fecha = $(this).val();

    if (!materiaId || !fecha) return;

    $.ajax({
        url: "./config_asistencia_tec/obtener_asistencia_ajax.php",
        method: "GET",
        data: { carreraId, cursoId, comisionId, materiaId, fecha },
        success: function (res) {
            const html = $('<div>').html(res);

            // Filas normales
            const filas = html.find("tr").filter(function () {
                const text = $(this).text().toLowerCase();
                return !text.includes("resumen de asistencia");
            });

            // Extraer resumen
            const resumenRow = html.find("tr:contains('Resumen de asistencia')");
            const resumenCuadros = resumenRow.next().find("div");

            resumenCuadros.eq(0).addClass("resumen-verde");
            resumenCuadros.eq(1).addClass("resumen-rojo");
            resumenCuadros.eq(2).addClass("resumen-amarillo");

            // Mostrar solo resumen y botón, no la tabla aún
            $("#cuadrosResumen").html(resumenCuadros);
            $("#verDetalle").show();
            $("#tablaDetalle").slideUp();
        },
        error: function () {
            alert("Error al cargar resumen de asistencia");
        }
    });
});

function mostrarTabla() {
    const carreraId = $("#carreraId").val();
    const cursoId = $("#cursoId").val();
    const comisionId = $("#comisionId").val();
    const materiaId = $("#materia").val();
    const fecha = $("#fecha").val();

    $.ajax({
        url: "./config_asistencia_tec/obtener_asistencia_ajax.php",
        method: "GET",
        data: { carreraId, cursoId, comisionId, materiaId, fecha },
        success: function (res) {
            const html = $('<div>').html(res);

            const filas = html.find("tr").filter(function () {
                const text = $(this).text().toLowerCase();
                return !text.includes("resumen de asistencia");
            });

            const resumenRow = html.find("tr:contains('Resumen de asistencia')");
            const resumenCuadros = resumenRow.next().find("div");

            resumenCuadros.eq(0).addClass("resumen-verde");
            resumenCuadros.eq(1).addClass("resumen-rojo");
            resumenCuadros.eq(2).addClass("resumen-amarillo");

            $("#asistenciaBody").html(filas);
            $("#cuadrosResumen").html(resumenCuadros);
            $("#tablaDetalle").slideToggle(); // Muestra u oculta con efecto
        },
        error: function () {
            alert("Error al cargar asistencia detallada");
        }
    });
}

</script>
