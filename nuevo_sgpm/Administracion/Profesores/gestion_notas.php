<?php
// materia_desempeno.php

// Iniciar sesión y verificar login
session_start();
if (empty($_SESSION['id'])) {
    header('Location: ../../../login/login.php');
    exit;
}

// Generar o verificar CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Incluir layout (cabecera, CSS global, menú…)
include '../../layout.php';

// Conexión ya proporcionada por layout.php
// $conexion = new mysqli(...);

// Obtener parámetros de forma segura
$idProfesor = (int) ($_SESSION['id'] ?? 0);
$idCarrera  = (int) ($_GET['carreraId']  ?? 0);
$idMateria  = (int) ($_GET['materiaId']   ?? 0);
$comision   = (int) ($_GET['comisionId']  ?? 0);
$curso      = (int) ($_GET['cursoId']     ?? 0);
$anio       = (int) ($_GET['anio']        ?? 0);

// 1) Consulta de notas y agrupación
$sql1 = "
    SELECT 
        a.legajo, a.apellido_alumno, a.nombre_alumno,
        m.año_matriculacion, n.idnotas, n.numero_evaluacion,
        n.nota, n.cuatrimestre, n.tipo_evaluacion,
        n.nota_final, n.condicion
    FROM matriculacion_materias m
    INNER JOIN alumno a 
        ON m.alumno_legajo = a.legajo
    LEFT JOIN notas n 
        ON a.legajo = n.alumno_legajo 
       AND n.materias_idMaterias = ?
    WHERE m.materias_idMaterias = ?
      AND YEAR(m.año_matriculacion) = ?
    
      AND NOT EXISTS (
          SELECT 1
          FROM situacion_extraordinaria se
          INNER JOIN correlatividades c 
              ON se.materias_idMaterias = c.materias_idMaterias
          LEFT JOIN notas nt 
              ON nt.alumno_legajo = se.alumno_legajo
             AND nt.materias_idMaterias = c.materias_idMaterias1
          WHERE se.alumno_legajo = a.legajo
            AND se.materias_idMaterias = m.materias_idMaterias
            AND (
                (c.tipo_correlatividad_idtipo_correlatividad = 1 AND nt.condicion NOT IN ('Regular', 'Promocion')) OR
                (c.tipo_correlatividad_idtipo_correlatividad = 2 AND nt.condicion != 'Promocion') OR
                nt.condicion IS NULL
            )
      )
    ORDER BY a.apellido_alumno
";

$stmt1 = $conexion->prepare($sql1);
$stmt1->bind_param('iii', $idMateria, $idMateria, $anio);
$stmt1->execute();
$result = $stmt1->get_result();

$alumnos = [];
while ($row = $result->fetch_assoc()) {
    $legajo         = $row['legajo'];
    $cuatri         = $row['cuatrimestre'];
    $tipoEvaluacion = $row['tipo_evaluacion'];
    $nota           = $row['nota'];
    $idnotas        = $row['idnotas'];

    if (!isset($alumnos[$legajo])) {
        $alumnos[$legajo] = [
            'apellido'     => $row['apellido_alumno'],
            'nombre'       => $row['nombre_alumno'],
            'primer_cuatri'=> ['tps'=>[], 'parcial'=>'', 'recuperatorio'=>'', 'promedio'=>0],
            'segundo_cuatri'=>['tps'=>[], 'parcial'=>'', 'recuperatorio'=>'', 'promedio'=>0],
            'nota_final'   => null,
            'condicion'    => null,
        ];
    }
    if ($row['nota_final'] !== null) {
        $alumnos[$legajo]['nota_final'] = $row['nota_final'];
    }
    if ($row['condicion'] !== null) {
        $alumnos[$legajo]['condicion'] = $row['condicion'];
    }
    // Clasificar por cuatrimestre y tipo
    $key = $cuatri === 1 ? 'primer_cuatri' : 'segundo_cuatri';
    if ($tipoEvaluacion == 2) {
        $alumnos[$legajo][$key]['parcial'] = $nota;
    } elseif ($tipoEvaluacion == 3) {
        $alumnos[$legajo][$key]['recuperatorio'] = $nota;
    } else {
        $alumnos[$legajo][$key]['tps'][] = ['nota'=>$nota, 'idnotas'=>$idnotas];
    }
}
$stmt1->close();

// 2) Consulta de asistencias
$sql2 = "
    SELECT 
        m.alumno_legajo AS legajo,
        SUM(a.asistencia = 1) AS asistencias,
        SUM(a.asistencia = 0) AS ausencias,
        COUNT(*) AS total_clases,
        (SELECT COUNT(*) FROM alumnos_justificados aj
         WHERE aj.inscripcion_asignatura_alumno_legajo = m.alumno_legajo
           AND aj.materias_idMaterias = ?
        ) AS justificaciones
    FROM asistencia a
    JOIN matriculacion_materias m 
      ON a.materias_idMaterias = m.materias_idMaterias
    WHERE a.materias_idMaterias = ?
    GROUP BY m.alumno_legajo
";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param('ii', $idMateria, $idMateria);
$stmt2->execute();
$res2 = $stmt2->get_result();

$asistencias_alumnos = [];
while ($r = $res2->fetch_assoc()) {
    $legajo       = $r['legajo'];
    $total        = $r['total_clases'];
    $justif       = $r['justificaciones'];
    $ajAusencias  = floor($justif / 2);
    $ausenciasAdj = $r['ausencias'] + $ajAusencias;
    $asisAdj      = max(0, $r['asistencias'] - $ajAusencias);

    $asistencias_alumnos[$legajo] = [
        'por_asistencia'=> ($asisAdj  / $total)*100,
        'por_ausencia'  => ($ausenciasAdj/$total)*100,
        'justificaciones'=> $justif,
    ];
}
$stmt2->close();

// 3) Nombre de carrera y materia
$sql3 = "
    SELECT 
      c.nombre_carrera AS carreraNombre,
      m.Nombre           AS materiaNombre,
      co.comision, cu.curso
    FROM materias m
    INNER JOIN carreras c ON m.carreras_idCarrera = c.idCarrera
    INNER JOIN comisiones co ON m.comisiones_idComisiones = co.idComisiones
    INNER JOIN cursos cu ON m.cursos_idCursos = cu.idCursos
    WHERE m.idMaterias = ?
";
$stmt3 = $conexion->prepare($sql3);
$stmt3->bind_param('i', $idMateria);
$stmt3->execute();
$row3 = $stmt3->get_result()->fetch_assoc() ?: [];
$stmt3->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Control de Desempeño Académico</title>
  <style>
    :root {
      --color-principal: #f3545d;
      --color-principal-hover: #ff4a3a;
      --bg-indicaciones: #fff8b0;
      --text-dark: #333;
    }
    .contenido { padding: 1rem; max-width: 1200px; margin: auto; }
    .indicaciones {
      background: var(--bg-indicaciones);
      padding: 1rem; border-radius: 5px;
      color: var(--text-dark); margin-bottom: 2rem;
    }
    table { width: 100%; border-collapse: collapse; overflow-x: auto; display: block; }
    thead th { position: sticky; top: 0; background: var(--color-principal); color: white; padding: 0.5rem; }
    th, td { border: 1px solid #ccc; text-align: center; padding: 0.5rem; }
    input[type=number], select {
      width: 100%; padding: 0.3rem; border-radius: 4px; border: 1px solid #ccc;
      box-sizing: border-box; margin: 0;
    }
    button.tp-btn {
      background: var(--color-principal); border:none; color:white;
      padding:0.3rem 0.6rem; border-radius:50%; cursor:pointer;
    }
    button.tp-btn:hover { background: var(--color-principal-hover); }
    .fixed-buttons {
      position: fixed; bottom: 1rem; right:1rem; display:flex; gap:1rem;
    }
    .fixed-buttons .btn {
      background: var(--color-principal); color:white; padding:0.8rem 1.5rem;
      border:none; border-radius:5px; text-decoration:none; font-weight:bold;
    }
    .fixed-buttons .btn:hover { background: var(--color-principal-hover); }
    @media (max-width: 768px) {
      thead th { font-size: 0.8rem; padding:0.3rem; }
      input[type=number], select { font-size: 0.9rem; }
    }
  </style>
</head>
<body>
<div class="contenido">
  <h1>
    Carrera: <?= htmlspecialchars($row3['carreraNombre'] ?? '—') ?>
    | Materia: <?= htmlspecialchars($row3['materiaNombre'] ?? '—') ?>
    | Curso: <?= htmlspecialchars($row3['curso'] ?? '—') ?>
    | Comisión: <?= htmlspecialchars($row3['comision'] ?? '—') ?>
    | Año: <?= $anio ?>
  </h1>
  <h2>Control de Desempeño Académico</h2>
  <p class="indicaciones">
    <strong>Uso de botones y cálculo de promedios:</strong><br>
    <button class="tp-btn" aria-label="Eliminar último TP">−</button> Elimina el último TP<br>
    <button class="tp-btn" aria-label="Añadir nuevo TP">+</button> Añade un TP<br>
    <strong>Nota final:</strong> Al cambiarla, se ajusta automáticamente la condición académica.
  </p>

  <form action="guardar_notas.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="idCarrera" value="<?= $idCarrera ?>">
    <input type="hidden" name="idMateria" value="<?= $idMateria ?>">
    <input type="hidden" name="idProfesor" value="<?= $idProfesor ?>">

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Apellido</th><th>Nombre</th>
          <th colspan="4">1° Cuatr.</th>
          <th colspan="4">2° Cuatr.</th>
          <th>Asis. %</th><th>Aus. %</th>
          <th>Nota Final</th><th>Condición</th>
        </tr>
        <tr>
          <th></th><th></th><th></th>
          <th><button type="button" class="tp-btn" data-action="remove" data-cuatr="primer">−</button> TPs <button type="button" class="tp-btn" data-action="add" data-cuatr="primer">+</button></th>
          <th>Parcial</th><th>Recup.</th><th>Promedio</th>
          <th><button type="button" class="tp-btn" data-action="remove" data-cuatr="segundo">−</button> TPs <button type="button" class="tp-btn" data-action="add" data-cuatr="segundo">+</button></th>
          <th>Parcial</th><th>Recup.</th><th>Promedio</th>
          <th></th><th></th><th></th><th></th>
        </tr>
      </thead>
      <tbody>
      <?php $cnt = 1; ?>
      <?php foreach ($alumnos as $legajo => $alumo): ?>
        <?php
          // Ordenar TPs por idnotas
          usort($alumnos[$legajo]['primer_cuatri']['tps'], function($a,$b){ return $a['idnotas'] <=> $b['idnotas']; });
          usort($alumnos[$legajo]['segundo_cuatri']['tps'],function($a,$b){ return $a['idnotas'] <=> $b['idnotas']; });
        ?>
        <tr data-legajo="<?= $legajo ?>">
          <td><?= $cnt++ ?></td>
          <td><?= htmlspecialchars($alumo['apellido']) ?></td>
          <td><?= htmlspecialchars($alumo['nombre']) ?></td>

          <!-- 1° cuatrimestre -->
          <td class="tp-cell" data-cuatr="primer">
            <?php foreach($alumo['primer_cuatri']['tps'] as $i=>$tp): ?>
              <input type="hidden" name="idnotas_primer_<?= $legajo ?>[]" value="<?= $tp['idnotas'] ?>">
              <input type="number" name="tp_primer_<?= $legajo ?>[]" value="<?= $tp['nota'] ?>" step="0.1" min="0" max="10" data-legajo="<?= $legajo ?>">
            <?php endforeach ?>
          </td>
          <td><input type="number" name="parcial_primer_<?= $legajo ?>" value="<?= $alumo['primer_cuatri']['parcial'] ?>" step="0.1" min="0" max="10"></td>
          <td><input type="number" name="recuperatorio_primer_<?= $legajo ?>" value="<?= $alumo['primer_cuatri']['recuperatorio'] ?>" step="0.1" min="0" max="10"></td>
          <td><input type="number" readonly class="promedio" id="promedio-primer-<?= $legajo ?>" value="0"></td>

          <!-- 2° cuatrimestre -->
          <td class="tp-cell" data-cuatr="segundo">
            <?php foreach($alumo['segundo_cuatri']['tps'] as $i=>$tp): ?>
              <input type="hidden" name="idnotas_segundo_<?= $legajo ?>[]" value="<?= $tp['idnotas'] ?>">
              <input type="number" name="tp_segundo_<?= $legajo ?>[]" value="<?= $tp['nota'] ?>" step="0.1" min="0" max="10" data-legajo="<?= $legajo ?>">
            <?php endforeach ?>
          </td>
          <td><input type="number" name="parcial_segundo_<?= $legajo ?>" value="<?= $alumo['segundo_cuatri']['parcial'] ?>" step="0.1" min="0" max="10"></td>
          <td><input type="number" name="recuperatorio_segundo_<?= $legajo ?>" value="<?= $alumo['segundo_cuatri']['recuperatorio'] ?>" step="0.1" min="0" max="10"></td>
          <td><input type="number" readonly class="promedio" id="promedio-segundo-<?= $legajo ?>" value="0"></td>

          <td><?= isset($asistencias_alumnos[$legajo]) ? number_format($asistencias_alumnos[$legajo]['por_asistencia'],2).'%':'—' ?></td>
          <td><?= isset($asistencias_alumnos[$legajo]) ? number_format($asistencias_alumnos[$legajo]['por_ausencia'],2).'%':'—' ?></td>

          <!-- Nota final y condición -->
          <td>
            <input type="number" name="nota_final_<?= $legajo ?>" id="nota_final_<?= $legajo ?>"
                   value="<?= htmlspecialchars($alumo['nota_final'] ?? '') ?>"
                   step="0.1" min="0" max="10" oninput="updateCondition(<?= $legajo ?>)">
          </td>
          <td>
            <select name="condicion_<?= $legajo ?>" id="condicion_<?= $legajo ?>">
              <option hidden>Seleccionar</option>
              <?php foreach (['Libre','Regular','Promocion'=>'Promoción','Abandono'=>'Abandonó','Recursa'] as $val=>$label): ?>
                <?php 
                  $optVal = is_int($val)? $label: $val;
                  $optLabel = is_int($val)? $label: $label;
                  $sel = ($alumo['condicion'] ?? '')== $optVal ? 'selected':''; 
                ?>
                <option value="<?= $optVal ?>" <?= $sel ?>><?= $optLabel ?></option>
              <?php endforeach ?>
            </select>
          </td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>

    <div class="fixed-buttons">
      <a href="PDF_general_notas.php?idCarrera=<?= $idCarrera ?>&idMateria=<?= $idMateria ?>" class="btn">Descargar PDF</a>
      <button type="submit" class="btn">Guardar Cambios</button>
    </div>
  </form>
</div>

<script>
(function(){
  // Recalcula promedio para un legajo y cuatrimestre
  function recalc(cuatr, legajo) {
    const inputs = document.querySelectorAll(`[data-legajo="${legajo}"][name^="tp_${cuatr}_${legajo}"]`);
    let sum=0, cnt=0;
    inputs.forEach(i=>{ let v=parseFloat(i.value); if(!isNaN(v)){ sum+=v; cnt++; }});
    document.getElementById(`promedio-${cuatr}-${legajo}`).value = cnt? (sum/cnt).toFixed(2): 0;
  }

  // Añadir o eliminar TP en toda la columna
  document.querySelectorAll('button.tp-btn').forEach(btn=>{
    btn.addEventListener('click', ()=> {
      const action = btn.dataset.action;
      const cuatr  = btn.dataset.cuatr;
      document.querySelectorAll(`.tp-cell[data-cuatr="${cuatr}"]`).forEach(cell=>{
        const leg = cell.closest('tr').dataset.legajo;
        if (action==='add') {
          const inp = document.createElement('input');
          inp.type='number'; inp.step='0.1'; inp.min=0; inp.max=10;
          inp.setAttribute('data-legajo', leg);
          inp.name = `tp_${cuatr}_${leg}[]`;
          inp.addEventListener('input', ()=> recalc(cuatr, leg));
          cell.appendChild(inp);
          recalc(cuatr, leg);
        } else {
          const all = cell.querySelectorAll('input[data-legajo]');
          if (all.length){
            cell.removeChild(all[all.length-1]);
            recalc(cuatr, leg);
          }
        }
      });
    });
  });

  // Inicializar promedios al cargar
  document.querySelectorAll('.tp-cell').forEach(cell=>{
    const cuatr = cell.dataset.cuatr;
    const leg  = cell.closest('tr').dataset.legajo;
    recalc(cuatr, leg);
  });

  // Actualiza select de condición según nota final
  window.updateCondition = function(legajo){
    const val = parseFloat(document.getElementById(`nota_final_${legajo}`).value)||0;
    const sel = document.getElementById(`condicion_${legajo}`);
    Array.from(sel.options).forEach(o=>o.disabled=false);
    if (val<6) {
      sel.value='Libre';
      ['Regular','Promocion'].forEach(v=> sel.querySelector(`option[value="${v}"]`).disabled=true);
    } else if (val<8) {
      if (!['Libre','Regular'].includes(sel.value)) sel.value='Regular';
      sel.querySelector(`option[value="Promocion"]`).disabled=true;
    } // ≥8: todo permitido
  };
})();
</script>
</body>
</html>
