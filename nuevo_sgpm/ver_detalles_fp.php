<?php
//ver_detalles_fp.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include './layout.php';

$legajo_afp = intval($_GET['legajo'] ?? 0);

if (!$legajo_afp) {
    echo "<p>Legajo inválido.</p>";
    exit;
}

// Buscar datos del alumno en FP
$sql_fp = "SELECT * FROM alumnos_fp WHERE legajo_afp = ?";
$stmt = $conexion->prepare($sql_fp);
$stmt->bind_param("i", $legajo_afp);
$stmt->execute();
$result_fp = $stmt->get_result();
$alumno_fp = $result_fp->fetch_assoc();

if (!$alumno_fp) {
    echo "<p>Alumno no encontrado.</p>";
    exit;
}

// Verificar si es alumno de tecnicatura
$es_tecnicatura = !empty($alumno_fp['alumno_legajo']);
$alumno_tecnicatura = [];

if ($es_tecnicatura) {
    $stmt_tec = $conexion->prepare("SELECT * FROM alumno WHERE legajo = ?");
    $stmt_tec->bind_param("i", $alumno_fp['alumno_legajo']);
    $stmt_tec->execute();
    $res_tec = $stmt_tec->get_result();
    $alumno_tecnicatura = $res_tec->fetch_assoc() ?: [];
}

// Obtener nombres de carreras
function nombreCarrera($id, $conexion) {
    if (!$id) return null;
    $stmt = $conexion->prepare("SELECT nombre_carrera FROM carreras WHERE idCarrera = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    return $row['nombre_carrera'] ?? null;
}

$carreras = [];
foreach (['carreras_idCarrera', 'carreras_idCarrera1', 'carreras_idCarrera2', 'carreras_idCarrera3'] as $col) {
    $carreras[] = nombreCarrera($alumno_fp[$col], $conexion);
}

// Total de asistencias
$stmtAsistencia = $conexion->prepare("
    SELECT COUNT(*) AS total_asistencias
    FROM asistencia_fp
    WHERE alumnos_fp_legajo_afp = ?
");
$stmtAsistencia->bind_param("i", $legajo_afp);
$stmtAsistencia->execute();
$resAsistencia = $stmtAsistencia->get_result();
$asistencias = $resAsistencia->fetch_assoc()['total_asistencias'] ?? 0;
?>

<div class="contenido">
    <h2>Detalles del alumno FP</h2>

    <a href="./config_alumno_fp/generar_pdf_fp.php?legajo=<?= $alumno_fp['legajo_afp'] ?>" class="btn btn-danger mt-3" target="_blank">
        🖨️ Imprimir ficha de inscripción FP
    </a>

    <p><strong>Legajo FP:</strong> <?= $alumno_fp['legajo_afp']; ?></p>

    <?php if ($es_tecnicatura): ?>
        <p><strong>Nombre:</strong> <?= $alumno_tecnicatura['nombre_alumno']; ?> <?= $alumno_tecnicatura['apellido_alumno']; ?></p>
        <p><strong>DNI:</strong> <?= $alumno_tecnicatura['dni_alumno']; ?></p>
        <p><strong>Celular:</strong> <?= $alumno_tecnicatura['celular']; ?></p>
        <p><strong>Edad:</strong> <?= $alumno_tecnicatura['edad']; ?></p>
    <?php else: ?>
        <p><strong>Nombre:</strong> <?= $alumno_fp['nombre_afp']; ?> <?= $alumno_fp['apellido_afp']; ?></p>
        <p><strong>DNI:</strong> <?= $alumno_fp['dni_afp']; ?></p>
        <p><strong>Celular:</strong> <?= $alumno_fp['celular_afp']; ?></p>
    <?php endif; ?>

    <form action="./config_alumno_fp/update_datos_fp.php" method="POST">
        <input type="hidden" name="legajo_fp" value="<?= $alumno_fp['legajo_afp']; ?>">

        <div id="carreras-wrapper">
        <?php
        $stmtInsc = $conexion->prepare("
            SELECT i.idincripcion_fp, i.carreras_idCarrera, i.estado, i.fecha_inscripcion, i.fecha_finalizacion, i.corte, c.nombre_carrera
            FROM inscripcion_fp i
            INNER JOIN carreras c ON c.idCarrera = i.carreras_idCarrera
            WHERE i.alumnos_fp_legajo_afp = ?
        ");
        $stmtInsc->bind_param("i", $legajo_afp);
        $stmtInsc->execute();
        $resInsc = $stmtInsc->get_result();

        $estadoClase = function($estado) {
            switch ($estado) {
                case 1: return 'background-color:#d4edda;';
                case 2: return 'background-color:#f8d7da;';
                case 3: return 'background-color:#d1ecf1;';
                default: return '';
            }
        };

        while ($row = $resInsc->fetch_assoc()) {
            $style = $estadoClase($row['estado']);
        ?>
            <div class="p-3 mb-3 border rounded carrera-item" style="<?= $style ?>">
                <input type="hidden" name="idincripcion_fp[]" value="<?= $row['idincripcion_fp'] ?>">
                <label>Carrera:</label>
                <select name="carreras_idCarrera[]" class="form-control" required>
                    <option value="">-- Seleccionar --</option>
                    <?php
                    $qCarreras = mysqli_query($conexion, "SELECT idCarrera, nombre_carrera FROM carreras");
                    while ($c = mysqli_fetch_assoc($qCarreras)) {
                        $selected = $c['idCarrera'] == $row['carreras_idCarrera'] ? 'selected' : '';
                        echo "<option value='{$c['idCarrera']}' $selected>{$c['nombre_carrera']}</option>";
                    }
                    ?>
                </select>

                <label>Estado:</label>
                <select name="estado[]" class="form-control" required>
                    <option value="1" <?= $row['estado'] == 1 ? 'selected' : '' ?>>Activo</option>
                    <option value="2" <?= $row['estado'] == 2 ? 'selected' : '' ?>>Inactivo</option>
                    <option value="3" <?= $row['estado'] == 3 ? 'selected' : '' ?>>Finalizó</option>
                </select>

                <label>Fecha inscripción:</label>
                <input type="date" name="fecha_inscripcion[]" class="form-control" value="<?= $row['fecha_inscripcion'] ?>">

                <label>Fecha finalización:</label>
                <input type="date" name="fecha_finalizacion[]" class="form-control" value="<?= $row['fecha_finalizacion'] ?>">

                <label>Corte:</label>
                <input type="number" name="corte[]" class="form-control" value="<?= $row['corte'] ?>">

                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="quitarCarrera(this, <?= $row['idincripcion_fp'] ?>)">❌ Quitar carrera</button>
            </div>
        <?php } ?>
        </div>

        <!-- Template para añadir carrera -->
        <template id="carrera-template">
            <div class="p-3 mb-3 border rounded carrera-item" style="background-color: #fff;">
                <input type="hidden" name="idincripcion_fp[]" value="new">
                <label>Carrera:</label>
                <select name="carreras_idCarrera[]" class="form-control" required>
                    <option value="">-- Seleccionar --</option>
                    <?php
                    $qCarreras = mysqli_query($conexion, "SELECT idCarrera, nombre_carrera FROM carreras");
                    while ($c = mysqli_fetch_assoc($qCarreras)) {
                        echo "<option value='{$c['idCarrera']}'>{$c['nombre_carrera']}</option>";
                    }
                    ?>
                </select>

                <label>Estado:</label>
                <select name="estado[]" class="form-control" required>
                    <option value="1">Activo</option>
                    <option value="2">Inactivo</option>
                    <option value="3">Finalizó</option>
                </select>

                <label>Fecha inscripción:</label>
                <input type="date" name="fecha_inscripcion[]" class="form-control">

                <label>Fecha finalización:</label>
                <input type="date" name="fecha_finalizacion[]" class="form-control">

                <label>Corte:</label>
                <input type="number" name="corte[]" class="form-control">

                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="quitarCarrera(this, 'new')">❌ Quitar carrera</button>
            </div>
        </template>

        <button type="button" class="btn btn-outline-primary mt-2" onclick="agregarCarrera()">+ Añadir carrera</button>

        <h3>Requisitos Presentados</h3>
        <?php
        $requisitos = [
            'original_titulo' => 'Título original',
            'fotos' => 'Fotos',
            'folio' => 'Folio',
            'fotocopia_dni' => 'Fotocopia DNI',
            'fotocopia_partida_nacimiento' => 'Partida de nacimiento',
            'constancia_cuil' => 'Constancia de CUIL',
            'Pago' => 'Pago'
        ];
        foreach ($requisitos as $campo => $label) {
            $valor = $alumno_fp[$campo] ? 1 : 0;
            echo "<label>$label:</label>";
            echo "<select name='$campo' class='form-control'>";
            echo "<option value='1'" . ($valor == 1 ? ' selected' : '') . ">Sí</option>";
            echo "<option value='0'" . ($valor == 0 ? ' selected' : '') . ">No</option>";
            echo "</select><br>";
        }
        ?>

        <h3>Resumen de asistencia</h3>
        <p>Total de asistencias: <?= $asistencias; ?></p>

        <button type="submit" class="btn btn-success mt-3">Guardar Cambios</button>
        <a href="lista_fp.php" class="btn btn-secondary mt-3">Volver a la lista</a>
    </form>
</div>

<script>
function agregarCarrera() {
    const template = document.getElementById('carrera-template');
    const wrapper = document.getElementById('carreras-wrapper');
    const clone = template.content.cloneNode(true);
    wrapper.appendChild(clone);
}

function quitarCarrera(button, idInscripcion) {
    if (!confirm("¿Estás seguro de que querés quitar esta carrera?")) return;

    const item = button.closest('.carrera-item');
    if (idInscripcion !== "new") {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "eliminar_inscripcion_fp[]";
        input.value = idInscripcion;
        document.querySelector("form").appendChild(input);
    }
    item.remove();
}
</script>
