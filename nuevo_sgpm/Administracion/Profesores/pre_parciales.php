<?php
include '../../layout.php';

$idUsuario = $_SESSION['id'];
$rolUsuario = $_SESSION['roles'];
$anioActual = date("Y");

$queryCarreras = ($rolUsuario == 1) ?
    "SELECT DISTINCT c.idCarrera, c.nombre_carrera FROM carreras c" :
    "SELECT DISTINCT c.idCarrera, c.nombre_carrera
     FROM carreras c
     INNER JOIN materias m ON c.idCarrera = m.carreras_idCarrera
     WHERE m.profesor_idProrfesor = '{$idUsuario}'";

$resultCarreras = mysqli_query($conexion, $queryCarreras);
?>

<div class="contenido">
    <div class="contenedor-filtro">
        <h2 class="titulo">Control de Desempeño Académico</h2>
        <p class="Instrucciones">
  Siga estos pasos para generar el Control de Desempeño Académico:
  <ol>
    <li>Seleccione primero el <strong>Año</strong>.</li>
    <li>Elija la <strong>Carrera</strong> correspondiente.</li>
    <li>Espere a que carguen y seleccione el <strong>Curso</strong>.</li>
    <li>Espere a que carguen y seleccione la <strong>Comisión</strong>.</li>
    <li>Revise la tabla resultante para analizar el desempeño de sus estudiantes.</li>
  </ol>
</p>

        <label for="anio">Seleccione un Año:</label>
        <select id="anio">
            <option value="">Seleccione año</option>
            <?php for ($anio = 2023; $anio <= $anioActual; $anio++) { ?>
                <option value="<?= $anio ?>"><?= $anio ?></option>
            <?php } ?>
        </select>

        <label for="carrera">Seleccione una Carrera:</label>
        <select id="carrera">
            <option value="">Seleccione carrera</option>
            <?php while ($row = mysqli_fetch_assoc($resultCarreras)) { ?>
                <option value="<?= $row['idCarrera'] ?>"><?= $row['nombre_carrera'] ?></option>
            <?php } ?>
        </select>

        <label for="curso">Seleccione un Curso:</label>
        <select id="curso" disabled>
            <option value="">Primero seleccione carrera</option>
        </select>

        <label for="comision">Seleccione una Comisión:</label>
        <select id="comision" disabled>
            <option value="">Primero seleccione curso</option>
        </select>

        <div id="tablaMaterias" style="margin-top: 20px;"></div>
    </div>
</div>

<script>
    document.getElementById("carrera").addEventListener("change", function () {
        let carreraId = this.value;
        document.getElementById("curso").disabled = true;
        document.getElementById("comision").disabled = true;
        document.getElementById("curso").innerHTML = '<option value="">Cargando cursos...</option>';

        fetch('filtroMateriasAjax.php?action=getCursos&carrera=' + carreraId)
            .then(res => res.text())
            .then(data => {
                document.getElementById("curso").innerHTML = data;
                document.getElementById("curso").disabled = false;
            });
    });

    document.getElementById("curso").addEventListener("change", function () {
        let carreraId = document.getElementById("carrera").value;
        let cursoId = this.value;
        document.getElementById("comision").disabled = true;
        document.getElementById("comision").innerHTML = '<option value="">Cargando comisiones...</option>';

        fetch(`filtroMateriasAjax.php?action=getComisiones&carrera=${carreraId}&curso=${cursoId}`)
            .then(res => res.text())
            .then(data => {
                document.getElementById("comision").innerHTML = data;
                document.getElementById("comision").disabled = false;
            });
    });

    document.getElementById("comision").addEventListener("change", function () {
        let carreraId = document.getElementById("carrera").value;
        let cursoId = document.getElementById("curso").value;
        let comisionId = this.value;
        let anio = document.getElementById("anio").value;

        if (!anio) {
            alert("⚠️ Debes seleccionar el año antes.");
            return;
        }

        fetch(`filtroMateriasAjax.php?action=getMaterias&carrera=${carreraId}&curso=${cursoId}&comision=${comisionId}&anio=${anio}`)
            .then(res => res.text())
            .then(data => {
                document.getElementById("tablaMaterias").innerHTML = data;
            });
    });
</script>
<style>
  /* Contenedor interno */
  .contenedor-filtro {
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 32px;
    max-width: 800px;
    margin: 0 auto 40px;
  }

  /* Título principal */
  .titulo {
    font-size: 1.8rem;
    font-weight: 600;
    color: #f3545d;
    margin-bottom: 16px;
    text-align: center;
  }

  /* Instrucciones para el docente */
  .Instrucciones {
    font-size: 1rem;
    color: #333;
    background-color: #fef1f0;
    border-left: 4px solid #f3545d;
    padding: 12px 16px;
    margin-bottom: 24px;
    line-height: 1.5;
  }

  /* Estilos para etiquetas */
  .contenedor-filtro label {
    display: block;
    margin-top: 16px;
    font-weight: 500;
    color: #555;
  }

  /* Selects personalizados */
  .contenedor-filtro select {
    width: 100%;
    padding: 10px 12px;
    margin-top: 6px;
    border: 1px solid #ddd;
    border-radius: 6px;
    appearance: none;
    background-color: #fff;
    transition: border-color 0.3s, box-shadow 0.3s;
  }
  .contenedor-filtro select:focus {
    border-color: #f3545d;
    outline: none;
    box-shadow: 0 0 0 3px rgba(243,84,93,0.2);
  }

  /* Tabla de materias */
  #tablaMaterias {
    margin-top: 24px;
  }
  /* Estilos para la tabla de materias */
.tabla-materias {
  width: 100%;
  border-collapse: collapse;
  margin-top: 24px;
  font-family: sans-serif;
}
.tabla-materias thead tr {
  background-color: #f3545d;
}
.tabla-materias th {
  color: #ffffff;
  padding: 12px 16px;
  text-align: left;
  font-weight: 500;
}
.tabla-materias td {
  padding: 12px 16px;
  border-bottom: 1px solid #eee;
  color: #333;
}
.tabla-materias tr:hover {
  background-color: #fef1f0;
}

/* Estilo para el botón “Seleccionar” */
.btn-seleccionar {
  display: inline-block;
  padding: 8px 14px;
  background-color: #f3545d;
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  border-radius: 6px;
  transition: background-color 0.3s, transform 0.2s;
}
.btn-seleccionar:hover {
  background-color: #d8434a;
  transform: translateY(-1px);
}
/* Asegúrate de que el enlace siempre sea blanco y sin subrayado */
.btn-seleccionar,
.btn-seleccionar:visited {
  color: #ffffff;
  text-decoration: none;
}

/* Mantener el color blanco al hover/focus y retirar subrayado */
.btn-seleccionar:hover,
.btn-seleccionar:focus {
  color: #ffffff;
  text-decoration: none;
  background-color: #d8434a; /* tu tono ligeramente más oscuro */
  transform: translateY(-1px);
}
</style>