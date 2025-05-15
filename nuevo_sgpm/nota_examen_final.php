<?php
include './layout.php';
?>
<?php
// Materias cuatrimestrales
$materias_cuatrimestrales = ['146','415','426','193','444','453'];

// Obtener variables
$idMateria = $_POST['materia'] ?? $_GET['materia'] ?? null;
$turno_seleccionado = $_POST['turno'] ?? $_GET['turno'] ?? null;
$año = $_POST['año'] ?? $_GET['año'] ?? null;
$idCurso = $_POST['curso'] ?? $_GET['curso'] ?? null;
$idComision = $_POST['comision'] ?? $_GET['comision'] ?? null;
$idCarrera = $_POST['carrera'] ?? $_GET['carrera'] ?? null;

// Validación
if (!$idMateria || !$turno_seleccionado || !$año || !$idCurso || !$idComision || !$idCarrera) {
    echo "<script>alert('Faltan datos para generar la consulta.'); window.location.href='pre_nota_final.php';</script>";
    exit;
}

$es_cuatrimestral = in_array($idMateria, $materias_cuatrimestrales);

// Determinar fechas según el turno
switch ($turno_seleccionado) {
    case 1: $fecha_inicio = $es_cuatrimestral ? "$año-07-01" : "$año-11-01";
            $fecha_fin    = $es_cuatrimestral ? "$año-08-31" : "$año-12-31"; break;
    case 2: $fecha_inicio = $es_cuatrimestral ? "$año-11-01" : "$año-02-01";
            $fecha_fin    = $es_cuatrimestral ? "$año-12-31" : "$año-03-31"; break;
    case 3: $fecha_inicio = $es_cuatrimestral ? "$año-02-01" : "$año-07-01";
            $fecha_fin    = $es_cuatrimestral ? "$año-03-31" : "$año-08-31"; break;
    case 4: $fecha_inicio = $es_cuatrimestral ? "$año-07-01" : "$año-11-01";
            $fecha_fin    = $es_cuatrimestral ? "$año-08-31" : "$año-12-31"; break;
    case 5: $fecha_inicio = $es_cuatrimestral ? "$año-11-01" : "$año-02-01";
            $fecha_fin    = $es_cuatrimestral ? "$año-12-31" : "$año-03-31"; break;
    case 6: $fecha_inicio = $es_cuatrimestral ? "$año-02-01" : "$año-07-01";
            $fecha_fin    = $es_cuatrimestral ? "$año-03-31" : "$año-08-31"; break;
    case 7: $fecha_inicio = $es_cuatrimestral ? "$año-07-01" : "$año-11-01";
            $fecha_fin    = $es_cuatrimestral ? "$año-08-31" : "$año-12-31"; break;
    default:
        echo "<script>alert('Turno no válido.'); window.location.href='pre_nota_final.php';</script>";
        exit;
}

// CONSULTA FINAL USANDO RANGO DE FECHAS (NO FILTRA POR TURNO)
$query = "
SELECT  
    a.nombre_alumno, 
    a.apellido_alumno, 
    a.dni_alumno, 
    mf.alumno_legajo,
    m.Nombre AS nombre_materia,
    t.fecha AS fecha_tanda,
    t.tanda,
    t.llamado,
    n.condicion,
    nef1.nota AS nota_primer_llamado,
    nef1.tomo AS tomo_primer_llamado,
    nef1.folio AS folio_primer_llamado,
    nef2.nota AS nota_segundo_llamado,
    nef2.tomo AS tomo_segundo_llamado,
    nef2.folio AS folio_segundo_llamado
FROM mesas_finales mf
JOIN alumno a ON mf.alumno_legajo = a.legajo
JOIN fechas_mesas_finales fmf ON mf.fechas_mesas_finales_idfechas_mesas_finales = fmf.idfechas_mesas_finales
JOIN tandas t ON fmf.tandas_idtandas = t.idtandas
JOIN materias m ON mf.materias_idMaterias = m.idMaterias
JOIN notas n ON n.alumno_legajo = mf.alumno_legajo AND n.materias_idMaterias = mf.materias_idMaterias
LEFT JOIN nota_examen_final nef1 
    ON mf.alumno_legajo = nef1.alumno_legajo 
    AND nef1.materias_idMaterias = fmf.materias_idMaterias
    AND nef1.llamado = 1
    AND YEAR(nef1.fecha) = ?
LEFT JOIN nota_examen_final nef2 
    ON mf.alumno_legajo = nef2.alumno_legajo 
    AND nef2.materias_idMaterias = fmf.materias_idMaterias
    AND nef2.llamado = 2
    AND YEAR(nef2.fecha) = ?
WHERE 
    fmf.materias_idMaterias = ?
    AND m.carreras_idCarrera = ?
    AND m.cursos_idCursos = ?
    AND m.comisiones_idComisiones = ?
    AND t.fecha BETWEEN ? AND ?
    AND n.condicion IN ('Regular', 'Libre')
GROUP BY a.legajo
ORDER BY a.apellido_alumno ASC
";

$stmt = $conexion->prepare($query);
$stmt->bind_param(
     "iiiiisss",
    $año, // año notas llamado 1
    $año, // año notas llamado 2
    $idMateria, $idCarrera, $idCurso, $idComision,
    $fecha_inicio, $fecha_fin
);
$stmt->execute();
$result = $stmt->get_result();

// Nombre de la materia
$query_materia = "SELECT Nombre FROM materias WHERE idMaterias = ?";
$stmt_materia = $conexion->prepare($query_materia);
$stmt_materia->bind_param("i", $idMateria);
$stmt_materia->execute();
$result_materia = $stmt_materia->get_result();
$materia_nombre = $result_materia->fetch_assoc()['Nombre'] ?? 'Materia desconocida';
$stmt_materia->close();

// Meses visibles
$meses_turno = $es_cuatrimestral
    ? ["1" => "Julio - Agosto", "2" => "Noviembre - Diciembre", "3" => "Febrero - Marzo", "4" => "Julio - Agosto", "5" => "Noviembre - Diciembre", "6" => "Febrero - Marzo", "7" => "Julio - Agosto"]
    : ["1" => "Noviembre - Diciembre", "2" => "Febrero - Marzo", "3" => "Julio - Agosto", "4" => "Noviembre - Diciembre", "5" => "Febrero - Marzo", "6" => "Julio - Agosto", "7" => "Noviembre - Diciembre"];

$meses = $meses_turno[$turno_seleccionado] ?? "Meses no definidos";

// Aquí ya se incluye automáticamente el HTML con las notas que corresponden
?>




<div class="contenido">
    <div class="form-container">
        <h2>Mesa Final: Lista de Inscriptos: 
            <?php echo htmlspecialchars(strval($materia_nombre), ENT_QUOTES, 'UTF-8'); ?> 
            - Mesas del período actual - Turno 
            <?php echo htmlspecialchars(strval($turno_seleccionado), ENT_QUOTES, 'UTF-8'); ?> 
            (<?php echo htmlspecialchars(strval($meses), ENT_QUOTES, 'UTF-8'); ?>) 
        </h2>

        <?php if ($result->num_rows > 0) { ?>
            <form action="guardar_nota_examen_final.php" method="POST">
                <div class="table-responsive">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Apellido</th>
                                <th>Nombre</th>
                                <th>DNI</th>
                                <th colspan="4" class="llamados">Primer Llamado</th>
                                <th colspan="4" class="llamados">Segundo Llamado</th>
                            </tr>
                            <tr>
                                <th></th><th></th><th></th><th></th>
                                <th>Nota</th><th>Tomo</th><th>Folio</th><th>Ausente</th>
                                <th>Nota</th><th>Tomo</th><th>Folio</th><th>Ausente</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $contador = 1;
                        while ($row = $result->fetch_assoc()) { 
                            $nota1 = $row['nota_primer_llamado'];
                            $nota2 = $row['nota_segundo_llamado'];
                        
                            $tomo1 = $row['tomo_primer_llamado'];
                            $folio1 = $row['folio_primer_llamado'];
                            $tomo2 = $row['tomo_segundo_llamado'];
                            $folio2 = $row['folio_segundo_llamado'];
                        
                            $nota1_guardada = (!is_null($tomo1) && $tomo1 !== '') || (!is_null($folio1) && $folio1 !== '');
                            $nota2_guardada = (!is_null($tomo2) && $tomo2 !== '') || (!is_null($folio2) && $folio2 !== '');
                        
                            $esAusente1 = $nota1_guardada && (is_null($nota1) || $nota1 == 0 || $nota1 === '0.00');
                            $esAusente2 = $nota2_guardada && (is_null($nota2) || $nota2 == 0 || $nota2 === '0.00');
                        
                            $habilitarSegundoLlamado = false;
                        ?>
                        <tr>
                            <td><?php echo $contador; ?></td>
                            <td><?php echo htmlspecialchars($row['apellido_alumno'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_alumno'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['dni_alumno'], ENT_QUOTES, 'UTF-8'); ?></td>
                        
                            <input type="hidden" name="alumno_legajo[<?php echo $contador; ?>]" 
                                   value="<?php echo htmlspecialchars($row['alumno_legajo'], ENT_QUOTES, 'UTF-8'); ?>">
                        
                            <!-- PRIMER LLAMADO -->
                            <td colspan="4" id="primer-llamado-<?php echo $contador; ?>">
                                <div style="display: flex; gap: 5px; align-items: center;">
                                    <div class="nota-contenedor" style="position: relative;">
                                        <?php if ($esAusente1): ?>
                                            <span class="ausente-label" style="font-weight: bold; color: red;">A</span>
                                            <span class="editar-ausente" title="Editar nota" style="cursor: pointer; margin-left: 4px; color: #888;" onclick="mostrarInputNota(this)">✖</span>
                                            <input type="number" 
                                                name="nota_final_1[<?php echo $contador; ?>]" 
                                                class="input-nota nota-1" 
                                                min="0" max="10" step="0.1"
                                                value=""
                                                data-segundo-llamado="segundo-llamado-<?php echo $contador; ?>"
                                                style="display: none;"
                                                onchange="evaluarNota(this)">
                                        <?php else: ?>
                                            <input type="number" 
                                                name="nota_final_1[<?php echo $contador; ?>]" 
                                                class="input-nota nota-1" 
                                                min="0" max="10" step="0.1"
                                                value="<?php echo htmlspecialchars($nota1 ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-segundo-llamado="segundo-llamado-<?php echo $contador; ?>"
                                                onchange="evaluarNota(this)">
                                        <?php endif; ?>
                                    </div>
                        
                                    <input type="number" 
                                        name="tomo_1[<?php echo $contador; ?>]" 
                                        class="input-nota nota-1"
                                        value="<?php echo htmlspecialchars($tomo1 ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        
                                    <input type="number" 
                                        name="folio_1[<?php echo $contador; ?>]" 
                                        class="input-nota nota-1"
                                        value="<?php echo htmlspecialchars($folio1 ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        
                                    <input 
                                        type="checkbox" 
                                        name="ausente_1[<?php echo $contador; ?>]" 
                                        value="1"
                                        data-llamado="primer-llamado-<?php echo $contador; ?>"
                                        data-segundo-llamado="segundo-llamado-<?php echo $contador; ?>"
                                        onchange="toggleAusente(this)"
                                        <?php echo $esAusente1 ? 'checked' : ''; ?>>
                                </div>
                            </td>
                        
                            <!-- SEGUNDO LLAMADO -->
                            <td colspan="4" id="segundo-llamado-<?php echo $contador; ?>">
                                <div style="display: flex; gap: 5px; align-items: center;">
                                    <div class="nota-contenedor" style="position: relative;">
                                        <?php if ($esAusente2): ?>
                                            <span class="ausente-label" style="font-weight: bold; color: red;">A</span>
                                            <span class="editar-ausente" title="Editar nota" style="cursor: pointer; margin-left: 4px; color: #888;" onclick="mostrarInputNota(this)">✖</span>
                                            <input type="number" 
                                                name="nota_final_2[<?php echo $contador; ?>]" 
                                                class="input-nota nota-2" 
                                                min="0" max="10" step="0.1"
                                                value=""
                                                style="display: none;"
                                                <?php echo !$habilitarSegundoLlamado ? 'disabled' : ''; ?>>
                                        <?php else: ?>
                                            <input type="number" 
                                                name="nota_final_2[<?php echo $contador; ?>]" 
                                                class="input-nota nota-2" 
                                                min="0" max="10" step="0.1"
                                                value="<?php echo htmlspecialchars($nota2 ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                <?php echo !$habilitarSegundoLlamado ? 'disabled' : ''; ?>>
                                        <?php endif; ?>
                                    </div>
                        
                                    <input type="number" 
                                        name="tomo_2[<?php echo $contador; ?>]" 
                                        class="input-nota nota-2"
                                        value="<?php echo htmlspecialchars($tomo2 ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo !$habilitarSegundoLlamado ? 'disabled' : ''; ?>>
                        
                                    <input type="number" 
                                        name="folio_2[<?php echo $contador; ?>]" 
                                        class="input-nota nota-2"
                                        value="<?php echo htmlspecialchars($folio2 ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo !$habilitarSegundoLlamado ? 'disabled' : ''; ?>>
                        
                                    <input 
                                        type="checkbox" 
                                        name="ausente_2[<?php echo $contador; ?>]" 
                                        value="1"
                                        <?php echo !$habilitarSegundoLlamado ? 'disabled' : ''; ?>
                                        <?php echo $esAusente2 ? 'checked' : ''; ?>>
                                </div>
                            </td>
                        </tr>
                        <?php $contador++; } ?>
                        </tbody>

                    </table>
                </div>
                <input type="hidden" name="materia" value="<?php echo htmlspecialchars(strval($idMateria), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="turno" value="<?php echo htmlspecialchars(strval($turno_seleccionado), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="año" value="<?php echo htmlspecialchars(strval($año), ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="btn-submit">Guardar Notas</button>
            </form>
        <?php } else { ?>
            <p>No hay estudiantes inscriptos en la mesa final para el período actual sin nota registrada.</p>
        <?php } ?>
    </div>
</div>


<script>
    function evaluarNota(input) {
    const index = input.name.match(/\[(\d+)\]/)[1];
    const segundoLlamado = document.getElementById(`segundo-llamado-${index}`);

    if (!segundoLlamado) return;

    const notaInput = segundoLlamado.querySelector(`input[name="nota_final_2[${index}]"]`);
    const tomoInput = segundoLlamado.querySelector(`input[name="tomo_2[${index}]"]`);
    const folioInput = segundoLlamado.querySelector(`input[name="folio_2[${index}]"]`);
    const ausenteCheckbox = segundoLlamado.querySelector(`input[name="ausente_2[${index}]"]`);

    const nota = parseFloat(input.value);

    if (!isNaN(nota) && nota >= 6) {
        notaInput.disabled = true; notaInput.value = '';
        tomoInput.disabled = true; tomoInput.value = '';
        folioInput.disabled = true; folioInput.value = '';
        ausenteCheckbox.disabled = true; ausenteCheckbox.checked = false;
    } else {
        notaInput.disabled = false;
        tomoInput.disabled = false;
        folioInput.disabled = false;
        ausenteCheckbox.disabled = false;
    }
}

function toggleAusente(checkbox) {
    const index = checkbox.name.match(/\[(\d+)\]/)[1];
    const primerLlamado = document.getElementById(`primer-llamado-${index}`);
    const segundoLlamado = document.getElementById(`segundo-llamado-${index}`);

    if (!primerLlamado || !segundoLlamado) return;

    const notaInput = primerLlamado.querySelector(`input[name="nota_final_1[${index}]"]`);

    const notaInput2 = segundoLlamado.querySelector(`input[name="nota_final_2[${index}]"]`);
    const tomoInput2 = segundoLlamado.querySelector(`input[name="tomo_2[${index}]"]`);
    const folioInput2 = segundoLlamado.querySelector(`input[name="folio_2[${index}]"]`);
    const ausenteCheckbox2 = segundoLlamado.querySelector(`input[name="ausente_2[${index}]"]`);

    if (checkbox.checked) {
        notaInput.value = '';
        notaInput.disabled = true;

        notaInput2.disabled = false;
        tomoInput2.disabled = false;
        folioInput2.disabled = false;
        ausenteCheckbox2.disabled = false;
    } else {
        notaInput.disabled = false;

        notaInput2.disabled = true; notaInput2.value = '';
        tomoInput2.disabled = true; tomoInput2.value = '';
        folioInput2.disabled = true; folioInput2.value = '';
        ausenteCheckbox2.checked = false;
        ausenteCheckbox2.disabled = true;
    }
}

</script>

<script>
function mostrarInputNota(span) {
    const contenedor = span.parentElement;
    const label = contenedor.querySelector(".ausente-label");
    const input = contenedor.querySelector("input[type='number']");

    // Buscar el checkbox hermano más cercano dentro del mismo <td>
    const parentRow = contenedor.closest("td");
    const checkbox = parentRow.querySelector("input[type='checkbox']");

    if (label) label.remove();
    span.remove();

    if (input) {
        input.style.display = "inline-block";
        input.focus();
    }

    if (checkbox) {
        checkbox.checked = false;
        checkbox.disabled = false;
    }
}
</script>


<?php
$stmt->close();
$conexion->close();
?>




<style>
      .llamados {
        text-align: center; /* Centrar horizontalmente */
        vertical-align: middle; /* Centrar verticalmente */
    }
    /* Para navegadores con motor WebKit (Chrome, Safari, Edge) */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0; /* Asegura que no queden espacios adicionales */
}

/* Para Firefox */
input[type="number"] {
    -moz-appearance: textfield; /* Cambia el estilo para que parezca un input de texto */
}
    /* Estilo general para el checkbox */
input[type="checkbox"] {
    appearance: none; /* Elimina el diseño predeterminado del navegador */
    width: 20px;
    height: 20px;
    border: 2px solid #ccc; /* Borde minimalista */
    border-radius: 4px; /* Bordes ligeramente redondeados */
    cursor: pointer;
    transition: all 0.3s ease; /* Suaviza los cambios de estilo */
}

/* Estilo para el estado seleccionado */
input[type="checkbox"]:checked {
    background-color: #f3545d; /* Color rojo al estar marcado */
    border-color: #f3545d; /* El borde también cambia a rojo */
}

/* Efecto hover para indicar interactividad */
input[type="checkbox"]:hover {
    border-color: #999; /* Cambia el color del borde al pasar el mouse */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2); /* Añade un pequeño sombreado */
}
	.contenido {
    position: absolute;
    top: 55px;
    left: 270px;
    width: calc(100% - 270px);
    background-color: #ffffff;
    background-image: url(./assets/img/fondo.png);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: local;
    padding: 20px;
    min-height: 100%;
    background-attachment: local; /* Hace que el fondo se mueva con el contenido */
}
@media (max-width: 768px) {
    .contenido {
        width: 100%;
        left: 0;
    }
 }

 .form-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 30px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .table-responsive {
        overflow-x: auto;
        margin-top: 20px;
    }

    .styled-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 16px;
        text-align: left;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        min-width: 700px;
    }

    .styled-table thead tr {
        background-color: #f3545d;
        color: #ffffff;
    }

    .styled-table th, .styled-table td {
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    .styled-table tbody tr:nth-of-type(even) {
        background-color: #f9f9f9;
    }

    .styled-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .styled-table td input.input-nota {
        width: 100%;
        max-width: 80px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: center;
    }

    button.btn-submit {
        background-color: #f3545d;
        color: #fff;
        padding: 10px 20px;
        margin-top: 15px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button.btn-submit:hover {
        background-color: #d8444a;
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 15px;
        }

        .styled-table th, .styled-table td {
            padding: 8px 10px;
        }
    }

</style>

</body>
</html>

