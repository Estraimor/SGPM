<?php
include './layout.php';
include '../conexion/conexion.php';
session_start();

$idProfesor = $_SESSION['id'];
$roles = $_SESSION['roles'];
?>


<div class="contenido">
    <div class="input-container">
        <h2 class="form-container__h2">Completar Motivos de Faltas Justificadas</h2>

        <!-- Filtros de selección -->
        <form id="filtroForm" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
            <!-- Carrera -->
            <select name="carrera" id="selectCarrera" required>
                <option value="">Carrera</option>
                <?php
                $res = ($roles == 1)
                    ? mysqli_query($conexion, "SELECT * FROM carreras")
                    : mysqli_query($conexion, "SELECT DISTINCT c.idCarrera, c.nombre_carrera 
                                                FROM preceptores p 
                                                INNER JOIN carreras c ON p.carreras_idCarrera = c.idCarrera 
                                                WHERE p.profesor_idProfesor = $idProfesor");

                while ($c = mysqli_fetch_assoc($res)) {
                    echo "<option value='{$c['idCarrera']}'>{$c['nombre_carrera']}</option>";
                }
                ?>
            </select>

            <!-- Curso -->
            <select name="curso" id="selectCurso" required>
                <option value="">Curso</option>
            </select>

            <!-- Comisión -->
            <select name="comision" id="selectComision" required>
                <option value="">Comisión</option>
            </select>

            <!-- Fecha -->
            <input type="date" name="fecha" id="inputFecha" required>
        </form>

        <!-- Tabla cargada por AJAX -->
        <div id="tablaResultado">
            <form action="./config_asistencia_tec/guardar_falta_justificada.php" method="post">
    <input type="text" id="buscador" placeholder="Buscar por nombre o legajo..." style="margin-bottom: 10px; width:100%; padding:5px;">
    
    <table id="tablaFaltas" class="styled-table">
        <thead>
            <tr>
                <th>Legajo</th>
                <th>Apellido</th>
                <th>Nombre</th>
                <th>Materia</th>
                <th>Fecha</th>
                <th>Motivo</th>
                <th>Otro</th>
            </tr>
        </thead>
        <tbody id="tbodyFaltas">
            <!-- Se rellena por AJAX -->
        </tbody>
    </table>

    <input type="submit" value="Guardar Justificaciones" class="form-container__input" style="margin-top: 20px;">
</form>
        </div>
    </div>
</div>


<script>
// Mostrar/ocultar campo "Otro" según la opción seleccionada
function toggleOtroInput(select, inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.style.display = (select.value === "Otro") ? "block" : "none";
    if (select.value === "Otro") input.focus();
}

// Buscador dentro de la tabla de alumnos
function filtrarTablaPorTexto() {
    const input = document.getElementById("buscador");
    if (!input) return;
    const filtro = input.value.toLowerCase();
    const filas = document.querySelectorAll("#tablaFaltas tbody tr");
    filas.forEach(fila => {
        fila.style.display = fila.innerText.toLowerCase().includes(filtro) ? "" : "none";
    });
}

// Cargar tbody vía AJAX
function cargarTablaJustificados() {
    const carrera = document.getElementById('selectCarrera')?.value;
    const curso = document.getElementById('selectCurso')?.value;
    const comision = document.getElementById('selectComision')?.value;
    const fecha = document.getElementById('inputFecha')?.value;
    const tbody = document.getElementById('tbodyFaltas');

    if (carrera && curso && comision && fecha && tbody) {
        $.ajax({
            url: 'config_asistencia_tec/ajax_tabla_faltas.php',
            type: 'GET',
            data: { carrera, curso, comision, fecha },
            success: function (data) {
                tbody.innerHTML = data;
                filtrarTablaPorTexto(); // Aplicar filtro si hay texto
            },
            error: function () {
                tbody.innerHTML = '<tr><td colspan="8" style="color:red;">Error al cargar datos.</td></tr>';
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const carrera = document.getElementById('selectCarrera');
    const curso = document.getElementById('selectCurso');
    const comision = document.getElementById('selectComision');
    const fecha = document.getElementById('inputFecha');

    // Reset selects y tabla
    function ocultarYResetearDesde(nivel) {
        if (nivel <= 1 && curso) curso.innerHTML = '<option value="">Curso</option>';
        if (nivel <= 2 && comision) comision.innerHTML = '<option value="">Comisión</option>';
        if (nivel <= 3 && fecha) fecha.value = '';
        const tbody = document.getElementById('tbodyFaltas');
        if (tbody) tbody.innerHTML = '';
    }

    // Cargar cursos al seleccionar carrera
    carrera?.addEventListener('change', () => {
        ocultarYResetearDesde(1);
        const idCarrera = carrera.value;
        if (idCarrera) {
            $.ajax({
                url: 'config_asistencia_tec/ajax_cursos.php',
                type: 'GET',
                data: { carrera: idCarrera },
                success: function (data) {
                    curso.innerHTML += data;
                }
            });
        }
    });

    // Cargar comisiones al seleccionar curso
    curso?.addEventListener('change', () => {
        ocultarYResetearDesde(2);
        const idCarrera = carrera.value;
        const idCurso = curso.value;
        if (idCarrera && idCurso) {
            $.ajax({
                url: 'config_asistencia_tec/ajax_comisiones.php',
                type: 'GET',
                data: { carrera: idCarrera, curso: idCurso },
                success: function (data) {
                    comision.innerHTML += data;
                }
            });
        }
    });

    // Cargar tabla cuando se cambia la fecha
    fecha?.addEventListener('change', cargarTablaJustificados);
    comision?.addEventListener('change', cargarTablaJustificados);

    // Buscador en vivo
    document.addEventListener('input', function (e) {
        if (e.target.id === 'buscador') {
            filtrarTablaPorTexto();
        }
    });
});
</script>






<style>
.input-container {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 95%;
    margin: auto;
}

#buscador {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 2px solid #d32f2f;
    border-radius: 6px;
    font-size: 16px;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.styled-table thead {
    background-color: #d32f2f;
    color: #fff;
}

.styled-table th, .styled-table td {
    padding: 12px 10px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.styled-table tbody tr:hover {
    background-color: #ffeaea;
}

input[type="text"], select {
    padding: 6px;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 100%;
}

input[type="submit"] {
    background-color: #d32f2f;
    color: #fff;
    font-weight: bold;
    font-size: 16px;
    border: none;
    padding: 12px;
    border-radius: 6px;
    margin-top: 20px;
    width: 100%;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #b71c1c;
}

input[type="checkbox"] {
    transform: scale(1.3);
}
</style>