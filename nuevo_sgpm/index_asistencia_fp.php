<?php
include './layout.php';

// Consulta según el rol
$excluidas = "(18, 27, 55, 46)";
    $queryDatos = "
        SELECT DISTINCT 
            p.carreras_idCarrera, 
            c.nombre_carrera
        FROM preceptores p
        INNER JOIN carreras c ON c.idCarrera = p.carreras_idCarrera
        WHERE p.carreras_idCarrera NOT IN $excluidas
        ORDER BY c.nombre_carrera
    ";


$result = mysqli_query($conexion, $queryDatos);
$asignaciones = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Manejo de sesión e inactividad (igual que antes)
$inactivity_limit = 1200;
if (isset($_SESSION['time']) && (time() - $_SESSION['time'] > $inactivity_limit)) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php");
    exit;
} else {
    $_SESSION['time'] = time();
}
?>
<div class="contenido">
  <div class="filtros-container">
    <h2 class="titulo">Seleccionar Asignatura</h2>

    <div class="filtro">
      <label for="select-carrera">Carrera</label>
      <select id="select-carrera">
        <option value="">-- Elige carrera --</option>
        <?php
          $carrerasUnicas = [];
          foreach ($asignaciones as $a) {
            $id = $a['carreras_idCarrera'];
            if (!isset($carrerasUnicas[$id])) {
              $carrerasUnicas[$id] = $a['nombre_carrera'];
              echo "<option value=\"$id\">".htmlspecialchars($a['nombre_carrera'])."</option>";
            }
          }
        ?>
      </select>
    </div>
    <button id="btn-ir" class="btn-accion" disabled>Ir a Asistencia</button>
  </div>
</div>


<script>
$(function(){
  $('#select-carrera').on('change', function(){
    $('#btn-ir').prop('disabled', !this.value);
  });

  $('#btn-ir').on('click', function(){
    const carrera = $('#select-carrera').val();
    window.location.href = `asistencia.php?carrera=${carrera}`;
  });
});
</script>

<style>
.contenido {
  display: flex;
  justify-content: center;
  padding: 20px;
}
.filtros-container {
  background: rgba(255,255,255,0.9);
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  max-width: 500px;
  width: 100%;
}
.titulo {
  color: #f3545d;
  text-align: center;
  margin-bottom: 25px;
  font-size: 1.5rem;
}
.filtro {
  margin-bottom: 20px;
}
.filtro label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
}
.filtro select {
  width: 100%;
  padding: 10px;
  border: 1px solid #f3545d;
  border-radius: 6px;
  font-size: 1rem;
  background: #fff;
}
.filtro select:disabled {
  opacity: 0.6;
}
.btn-accion {
  width: 100%;
  padding: 12px;
  background: #f3545d;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background .2s;
}
.btn-accion:disabled {
  background: #f3545d80;
  cursor: default;
}
.btn-accion:not(:disabled):hover {
  background: #d32f2c;
}

/* Responsive */
@media (max-width: 600px) {
  .filtros-container {
    padding: 20px;
  }
  .titulo {
    font-size: 1.3rem;
  }
}
</style>
