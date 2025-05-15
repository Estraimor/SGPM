<?php include './layout.php'; ?>
<div class="contenido">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f8;
    }

    .titulo-equivalencias {
      text-align: center;
      margin-bottom: 30px;
    }

    .titulo-equivalencias h2 {
      color: #2c3e50;
      font-size: 2rem;
    }

    .titulo-equivalencias p {
      color: #666;
      font-size: 1.1rem;
    }

    #busquedaForm {
      margin: auto;
      width: 100%;
      max-width: 450px;
      margin-bottom: 25px;
      display: flex;
      gap: 10px;
      align-items: center;
    }

    #busquedaForm input {
      padding: 14px 18px;
      flex: 1;
      border: none;
      border-radius: 24px;
      transition: all 0.5s ease;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
      background-color: white;
      font-size: 16px;
    }

    #busquedaForm input:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.6);
    }

    .card {
      background: #fff;
      margin: 20px auto;
      padding: 24px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      opacity: 0;
      transform: translateY(20px);
      animation: slideUpFade 0.8s forwards ease-out;
      width: 100%;
      max-width: 1000px;
    }

    @keyframes slideUpFade {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
    }

    @media screen and (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
    }

    .form-group {
      background-color: #fdfdfd;
      border: 1px solid #eee;
      padding: 16px;
      border-radius: 8px;
    }

    .form-group h4 {
      margin-top: 0;
      margin-bottom: 10px;
      color: #dc3545;
    }

    select, input[type="text"] {
      padding: 12px;
      margin-top: 6px;
      margin-bottom: 15px;
      border-radius: 6px;
      width: 100%;
      border: 1px solid #ccc;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
      font-size: 15px;
    }

    select:focus, input:focus {
      border-color: #dc3545;
      outline: none;
      box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.25);
    }

    .btn-guardar {
  background-color: #28a745; /* verde */
  border: none;
  padding: 10px 20px;
  color: white;
  font-weight: bold;
  border-radius: 6px;
  transition: background-color 0.3s ease;
  margin-top: 10px;
  cursor: pointer;
}

.btn-guardar:hover {
  background-color: #218838;
}

.btn-agregar {
  background-color: #007bff; /* azul */
  border: none;
  padding: 10px 20px;
  color: white;
  font-weight: bold;
  border-radius: 6px;
  transition: background-color 0.3s ease;
  margin-top: 10px;
  cursor: pointer;
}

.btn-agregar:hover {
  background-color: #0069d9;
}

.btn-eliminar {
  background-color: #dc3545; /* rojo */
  border: none;
  padding: 8px 18px;
  color: white;
  font-weight: bold;
  border-radius: 6px;
  transition: background-color 0.3s ease;
  margin-top: 10px;
  cursor: pointer;
}

.btn-eliminar:hover {
  background-color: #c82333;
}


    #resultadosBusqueda {
      margin-top: 15px;
      text-align: center;
      transition: all 0.6s ease;
    }

    #resultadosBusqueda div {
      border-bottom: none !important;
      padding: 12px 20px;
      margin: 6px auto;
      background-color: white;
      border-radius: 6px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
      transition: background-color 0.3s ease;
      max-width: 700px;
    }

    #resultadosBusqueda div:hover {
      background-color: #ffdddd;
      cursor: pointer;
    }
    
    .fade-slide {
  animation: aparecer 0.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
  opacity: 0;
  transform: translateY(20px);
}

@keyframes aparecer {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-out {
  animation: desaparecer 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes desaparecer {
  to {
    opacity: 0;
    transform: translateY(20px);
    max-height: 0;
    padding: 0;
    margin: 0;
  }
}
  </style>

  <div class="titulo-equivalencias">
  <h2>🧾 Aprobación de Unidades Curriculares por Equivalencia</h2>
  <p>Seleccioná los cursos, comisiones y materias de origen y destino para cargar una equivalencia</p>
</div>

<div class="contenedor-form">
  <form id="busquedaForm">
    <input type="text" id="busqueda" placeholder="Buscar por DNI, nombre o apellido..." autocomplete="off">
  </form>
</div>

<div id="resultadosBusqueda"></div>
<div id="datosAlumno" class="card" style="display:none;"></div>

<div id="formularioEquivalencia" class="card" style="display:none;">
  <div class="form-grid">
    <!-- Origen -->
    <div class="form-group">
      <h4>Datos de materia aprobada</h4>
      <div id="contenedorAprobadas"></div>
      <button type="button" class="btn-agregar" onclick="agregarAprobada()">➕ Agregar otra materia aprobada</button>
    </div>

    <!-- Destino -->
    <div class="form-group">
      <h4>Datos de inscripción</h4>
      <div id="contenedorInscripcion"></div>
      <button type="button" class="btn-agregar" onclick="agregarInscripcion()">➕ Agregar otra materia a cursar</button>
    </div>
  </div>

  <button class="btn-guardar" onclick="guardarEquivalencia()">Guardar equivalencia</button>
</div>

<input type="hidden" id="idCarrera" value="">
</div>

<script>
const cont = document.createElement("div");
cont.classList.add("fade-slide");
cont.style.overflow = "hidden"; // para que colapse con max-height
cont.style.maxHeight = "1000px"; // valor grande para permitir expansión
let legajoSeleccionado = 0;
let countAprobadas = 0;
let countInscripciones = 0;

document.getElementById("busqueda").addEventListener("input", function () {
  const valor = this.value.trim();
  if (valor.length < 3) {
    document.getElementById("resultadosBusqueda").innerHTML = "";
    return;
  }

  fetch("./config_aprobado_quivalencias/buscar_alumno.php?query=" + encodeURIComponent(valor))
    .then(res => res.text())
    .then(html => {
      document.getElementById("resultadosBusqueda").innerHTML = html;
    });
});

function seleccionarAlumno(legajo) {
  legajoSeleccionado = legajo;

  const resultados = document.getElementById("resultadosBusqueda");
  resultados.style.opacity = "0";
  resultados.style.transform = "translateY(-20px)";
  setTimeout(() => {
    resultados.innerHTML = "";
    resultados.style.opacity = "";
    resultados.style.transform = "";
  }, 600);

  fetch("./config_aprobado_quivalencias/cargar_datos_alumno.php?legajo=" + legajo)
    .then(res => res.text())
    .then(html => {
      const div = document.getElementById("datosAlumno");
      div.innerHTML = html;
      div.style.display = "block";

      const carrera = div.querySelector("#idCarrera")?.value;
      document.getElementById("idCarrera").value = carrera;

      document.getElementById("formularioEquivalencia").style.display = "block";

      agregarAprobada();
      agregarInscripcion();
    });
}

function cargarCursos(selectId) {
  fetch("./config_aprobado_quivalencias/select_cursos.php")
    .then(res => res.text())
    .then(html => {
      document.getElementById(selectId).innerHTML = html;
    });
}

function actualizarComisionesPara(cursoId, comisionId) {
  const curso = document.getElementById(cursoId).value;
  fetch(`./config_aprobado_quivalencias/select_comisiones.php?curso=${curso}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById(comisionId).innerHTML = html;
    });
}

function actualizarMateriasPara(cursoId, comisionId, materiaId) {
  const curso = document.getElementById(cursoId).value;
  const comision = document.getElementById(comisionId).value;
  const carrera = document.getElementById("idCarrera").value;

  if (!curso || !comision || !carrera) return;

  fetch(`./config_aprobado_quivalencias/listar_materias.php?curso=${curso}&comision=${comision}&carrera=${carrera}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById(materiaId).innerHTML = html;
    });
}

function agregarAprobada() {
  const id = `aprobada_${countAprobadas++}`;
  const cont = document.createElement("div");
  cont.classList.add("fade-slide"); // transición al aparecer
  cont.innerHTML = `
    <div style="border: 1px solid #ccc; padding:10px; margin-bottom:10px; border-radius:6px; background:#fff;">
      <label>Curso:</label>
      <select id="curso_${id}" onchange="actualizarComisionesPara('curso_${id}','comision_${id}')"></select>

      <label>Comisión:</label>
      <select id="comision_${id}" onchange="actualizarMateriasPara('curso_${id}','comision_${id}','materia_${id}')"></select>

      <label>Materia aprobada:</label>
      <select id="materia_${id}"></select>

      <label>% aprobado:</label>
      <input type="text" placeholder="% aprobado">

      <button type="button" class="btn-eliminar" onclick="eliminarConAnimacion(this)">🗑️ Eliminar</button>
    </div>`;
  document.getElementById("contenedorAprobadas").appendChild(cont);
  cargarCursos(`curso_${id}`);
}

function agregarInscripcion() {
  const id = `inscripcion_${countInscripciones++}`;
  const cont = document.createElement("div");
  cont.classList.add("fade-slide"); // transición al aparecer
  cont.innerHTML = `
    <div style="border: 1px solid #ccc; padding:10px; margin-bottom:10px; border-radius:6px; background:#fff;">
      <label>Curso:</label>
      <select id="curso_${id}" onchange="actualizarComisionesPara('curso_${id}','comision_${id}')"></select>

      <label>Comisión:</label>
      <select id="comision_${id}" onchange="actualizarMateriasPara('curso_${id}','comision_${id}','materia_${id}')"></select>

      <label>Materia a cursar:</label>
      <select id="materia_${id}"></select>

      <label>Motivo de equivalencia:</label>
      <input type="text" placeholder="Ej. contenido similar, resolución rectoral...">

      <button type="button" class="btn-eliminar" onclick="eliminarConAnimacion(this)">🗑️ Eliminar</button>
    </div>`;
  document.getElementById("contenedorInscripcion").appendChild(cont);
  cargarCursos(`curso_${id}`);
}

function eliminarConAnimacion(btn) {
  const bloque = btn.closest(".fade-slide"); // usa closest para encontrar el div con clase animada
  if (!bloque) return;

  bloque.classList.add("fade-out");
  bloque.addEventListener("animationend", () => bloque.remove());
}

function guardarEquivalencia() {
  const legajo = legajoSeleccionado;
  const carrera = document.getElementById("idCarrera").value;

  const aprobadas = [];
  document.querySelectorAll("#contenedorAprobadas > div").forEach(div => {
    const materia = div.querySelector("select[id^='materia_']")?.value;
    const porcentaje = div.querySelector("input")?.value;

    if (materia) {
      aprobadas.push({
        materia_id: materia,
        porcentaje: porcentaje
      });
    }
  });

  const inscripciones = [];
  document.querySelectorAll("#contenedorInscripcion > div").forEach(div => {
    const materia = div.querySelector("select[id^='materia_']")?.value;
    const motivo = div.querySelector("input")?.value;

    if (materia) {
      inscripciones.push({
        materia_id: materia,
        motivo: motivo
      });
    }
  });

  if (aprobadas.length === 0 && inscripciones.length === 0) {
    alert("No hay datos para guardar.");
    return;
  }

  // Enviar al backend
  fetch("./config_aprobado_quivalencias/guardar_equivalencia.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      legajo,
      carrera,
      aprobadas,
      inscripciones
    })
  })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      // Opcional: limpiar la interfaz si todo se guardó
    })
    .catch(err => alert("Error al guardar: " + err));
}

</script>


