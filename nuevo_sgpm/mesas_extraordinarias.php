<?php
include './layout.php';

$sql = "
SELECT 
    n.alumno_legajo,
    a.nombre_alumno,
    a.apellido_alumno,
    n.materias_idMaterias,
    m.Nombre AS nombre_materia,
    COUNT(ef.idnota_examen_final) AS intentos,
    GROUP_CONCAT(DATE_FORMAT(ef.fecha, '%d-%m-%Y') ORDER BY ef.fecha SEPARATOR ', ') AS fechas_fallidas,
    EXISTS (
        SELECT 1 
        FROM situacion_extraordinaria se
        WHERE se.alumno_legajo = n.alumno_legajo 
          AND se.materias_idMaterias = n.materias_idMaterias
    ) AS en_situacion
FROM notas n
JOIN nota_examen_final ef 
    ON ef.alumno_legajo = n.alumno_legajo 
   AND ef.materias_idMaterias = n.materias_idMaterias 
   AND ef.nota < 6
JOIN alumno a ON a.legajo = n.alumno_legajo
JOIN materias m ON m.idMaterias = n.materias_idMaterias
WHERE n.condicion = 'Libre'
  AND NOT EXISTS (
      SELECT 1 
      FROM nota_examen_final aprob
      WHERE aprob.alumno_legajo = n.alumno_legajo
        AND aprob.materias_idMaterias = n.materias_idMaterias
        AND aprob.nota >= 6
  )
  AND NOT EXISTS (
      SELECT 1 
      FROM matriculacion_materias mm
      WHERE mm.alumno_legajo = n.alumno_legajo
        AND mm.materias_idMaterias = n.materias_idMaterias
        AND YEAR(mm.año_matriculacion) > YEAR(CURDATE())
  )
GROUP BY n.alumno_legajo, n.materias_idMaterias
HAVING intentos >= 2
ORDER BY a.apellido_alumno;

";

$resultado = $conexion->query($sql);
?>

<style>
h3 {
    background-color: #ef5350;
    color: white;
    padding: 12px 20px;
    border-radius: 6px;
    font-size: 18px;
    margin-bottom: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    border-radius: 8px;
    overflow: hidden;
}
th, td {
    padding: 10px 12px;
    border-bottom: 1px solid #e0e0e0;
    text-align: left;
}
th {
    background-color: #e3f2fd;
    color: #333;
    font-weight: 600;
}
tr:hover {
    background-color: #f5f5f5;
}
.toggle-btn {
    border: none;
    padding: 6px 14px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 13px;
    color: white;
    transition: background 0.3s ease;
}
.btn-azul {
    background-color: #1976D2;
}
.btn-azul:hover {
    background-color: #1565c0;
}
.btn-rojo {
    background-color: #d32f2f;
}
.btn-rojo:hover {
    background-color: #b71c1c;
}

.btn-recursar {
    background-color: #43a047;
    color: white;
    border: none;
    padding: 6px 12px;
    margin-left: 8px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 13px;
    transition: background 0.3s ease;
}
.btn-recursar:hover {
    background-color: #2e7d32;
}
</style>

<div class="contenido">
    <h3>Alumnos en condición Libre con múltiples intentos fallidos</h3>
    <table>
        <thead>
            <tr>
                <th>Legajo</th>
                <th>Apellido</th>
                <th>Nombre</th>
                <th>Materia</th>
                <th>Intentos</th>
                <th>Fechas</th>
                <th>Situación</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['alumno_legajo'] ?></td>
                    <td><?= $row['apellido_alumno'] ?></td>
                    <td><?= $row['nombre_alumno'] ?></td>
                    <td><?= $row['nombre_materia'] ?></td>
                    <td><?= $row['intentos'] ?></td>
                    <td><?= $row['fechas_fallidas'] ?></td>
                    <td>
                    <?php
                    $enSituacion = $row['en_situacion'];
                    $textoBtn = $enSituacion ? 'Sacar de situación extraordinaria' : 'Colocar en situación extraordinaria';
                    $colorClase = $enSituacion ? 'btn-rojo' : 'btn-azul';
                    ?>
                    <button 
                        class="toggle-btn <?= $colorClase ?>"
                        data-legajo="<?= $row['alumno_legajo'] ?>"
                        data-materia="<?= $row['materias_idMaterias'] ?>"
                        data-situacion="<?= $enSituacion ? '1' : '0' ?>"
                        onclick="toggleSituacion(this)"
                    >
                        <?= $textoBtn ?>
                    </button>
                    <button 
                        class="btn-recursar"
                        data-legajo="<?= $row['alumno_legajo'] ?>"
                        data-materia="<?= $row['materias_idMaterias'] ?>"
                        onclick="recursar(this)"
                    >
                        Recursar
                    </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function toggleSituacion(button) {
    const legajo = button.dataset.legajo;
    const materia = button.dataset.materia;
    const enSituacion = button.dataset.situacion === '1';

    const mensaje = enSituacion 
        ? "¿Seguro que querés quitar al alumno de situación extraordinaria para esta materia?"
        : "¿Seguro que querés colocar al alumno en situación extraordinaria para esta materia?";
    if (!confirm(mensaje)) return;

    fetch('./config_situacion_extraordinaria/toggle_situacion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ legajo, materia })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const nuevoTexto = enSituacion 
                ? 'Colocar en situación extraordinaria'
                : 'Sacar de situación extraordinaria';

            const nuevaClase = enSituacion ? 'btn-azul' : 'btn-rojo';
            const viejaClase = enSituacion ? 'btn-rojo' : 'btn-azul';

            button.textContent = nuevoTexto;
            button.classList.remove(viejaClase);
            button.classList.add(nuevaClase);
            button.dataset.situacion = enSituacion ? '0' : '1';
        } else {
            alert("Hubo un error al actualizar la situación.");
        }
    });
}

function recursar(button) {
    const legajo = button.dataset.legajo;
    const materia = button.dataset.materia;

    if (!confirm("¿Confirmás que este alumno recursa la materia el próximo año?")) return;

    const añoSiguiente = new Date().getFullYear() + 1;

    fetch('./config_situacion_extraordinaria/recursar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ legajo, materia, año: añoSiguiente })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Materia recursada con éxito.");
            button.closest('tr').remove(); // Elimina la fila visualmente
        } else {
            alert("Error al recursar.");
        }
    });
}
</script>
