<?php
include '../layout.php'
?>
<div class="contenido">
    <h2>Generar Lista de Asistencias</h2>
    <form action="./PDF_informe_asistencia_tec.php" method="post" class="form-informes">
        <div class="form-dates">
            <div class="date-field">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" class="input-fecha" name="fecha_inicio" required>
            </div>

            <div class="date-field">
                <label for="fecha_fin">Fecha de fin:</label>
                <input type="date" id="fecha_fin" class="input-fecha" name="fecha_fin" required>
            </div>
        </div>

        <?php
        session_start();
        include '../../conexion/conexion.php';
        $idPreceptor = $_SESSION['id'];
        $rolUsuario = $_SESSION["roles"];

        if ($rolUsuario == 1) {
            $sql_mater = "
                SELECT DISTINCT c.idCarrera, c.nombre_carrera
                FROM carreras c
                INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
            ";
        } elseif ($rolUsuario == 5) {
            $sql_mater = "
                SELECT DISTINCT c.idCarrera, c.nombre_carrera
                FROM carreras c
                INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                WHERE c.profesor_idProrfesor = '{$idPreceptor}'
            ";
        } else {
            $sql_mater = "
                SELECT DISTINCT c.idCarrera, c.nombre_carrera
                FROM carreras c
                INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
                INNER JOIN inscripcion_asignatura ia ON ia.carreras_idCarrera = c.idCarrera
                WHERE m.profesor_idProrfesor = '{$idPreceptor}'
            ";
        }

        $peticion = mysqli_query($conexion, $sql_mater);
        ?>

        <div class="form-group">
            <label for="carrera">Carrera:</label>
            <select name="carrera" id="carrera" class="form-input-informes" required>
                <option hidden value="">Selecciona una carrera</option>
                <?php while($informacion = mysqli_fetch_assoc($peticion)) { ?>
                    <option value="<?php echo $informacion['idCarrera'] ?>"><?php echo htmlspecialchars($informacion['nombre_carrera']) ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="curso">Curso:</label>
            <select name="curso" id="curso" class="form-input-informes" required disabled>
                <option hidden value="">Selecciona un curso</option>
            </select>
        </div>

        <div class="form-group">
            <label for="comision">Comisión:</label>
            <select name="comision" id="comision" class="form-input-informes" required disabled>
                <option hidden value="">Selecciona una comisión</option>
            </select>
        </div>

        <div class="form-group">
            <label for="tipo_informe">Tipo de Informe:</label>
            <select name="tipo_informe" id="tipo_informe" class="form-input-informes" required>
                <option hidden value="">Selecciona tipo de informe</option>
                <option value="dia">Por día</option>
                <option value="materia">Por materia</option>
            </select>
        </div>

        <div id="materia_container" class="form-group" style="display:none;">
            <label for="materia">Materia:</label>
            <select name="materia" id="materia" class="form-input-informes" disabled>
                <option hidden value="">Selecciona una materia</option>
            </select>
        </div>

        <div class="form-group">
            <input type="submit" value="Generar Excel" class="boton-submit-informes">
        </div>
    </form>
</div>

<script>
const carreraSelect = document.getElementById('carrera');
const cursoSelect = document.getElementById('curso');
const comisionSelect = document.getElementById('comision');
const tipoInformeSelect = document.getElementById('tipo_informe');
const materiaContainer = document.getElementById('materia_container');
const materiaSelect = document.getElementById('materia');

// Cambiar carrera
carreraSelect.addEventListener('change', function() {
    resetSelect(cursoSelect, 'curso');
    resetSelect(comisionSelect, 'comisión');
    resetSelect(materiaSelect, 'materia');
    materiaContainer.style.display = 'none';

    if (this.value) {
        fetch('./obtener_cursos.php?idCarrera=' + this.value)
            .then(response => response.json())
            .then(data => {
                data.forEach(curso => {
                    const option = document.createElement('option');
                    option.value = curso.idCursos;
                    option.textContent = curso.curso;
                    cursoSelect.appendChild(option);
                });
                cursoSelect.disabled = false;
            });
    }
});

// Cambiar curso
cursoSelect.addEventListener('change', function() {
    resetSelect(comisionSelect, 'comisión');
    resetSelect(materiaSelect, 'materia');
    materiaContainer.style.display = 'none';

    const carreraId = carreraSelect.value;
    if (this.value && carreraId) {
        fetch('./confi_lista_estu/obtener_comisiones.php?idCarrera=' + carreraId + '&idCurso=' + this.value)
            .then(response => response.json())
            .then(data => {
                data.forEach(comision => {
                    const option = document.createElement('option');
                    option.value = comision.idComisiones;
                    option.textContent = comision.comision;
                    comisionSelect.appendChild(option);
                });
                comisionSelect.disabled = false;
            });
    }
});

// Cambiar tipo de informe
tipoInformeSelect.addEventListener('change', function() {
    resetSelect(materiaSelect, 'materia');
    materiaContainer.style.display = 'none';

    if (this.value === 'materia') {
        const carreraId = carreraSelect.value;
        const cursoId = cursoSelect.value;
        const comisionId = comisionSelect.value;

        if (carreraId && cursoId && comisionId) {
            fetch('./obtener_materias.php?idCarrera=' + carreraId + '&idCurso=' + cursoId + '&idComision=' + comisionId)
                .then(response => response.json())
                .then(data => {
                    materiaSelect.innerHTML = '<option hidden value="">Selecciona una materia</option>';
                    data.forEach(materia => {
                        const option = document.createElement('option');
                        option.value = materia.idMaterias;
                        option.textContent = materia.Nombre;
                        materiaSelect.appendChild(option);
                    });
                    materiaSelect.disabled = false;
                    materiaContainer.style.display = 'block';
                });
        } else {
            alert('Debes seleccionar carrera, curso y comisión primero.');
            this.value = '';
        }
    }
});

function resetSelect(selectElement, defaultText) {
    selectElement.innerHTML = `<option hidden value="">Selecciona un ${defaultText}</option>`;
    selectElement.disabled = true;
}
</script>




</body>
</html>

