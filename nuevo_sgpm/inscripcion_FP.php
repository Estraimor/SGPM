<?php
include './layout.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro Estudiante FP</title>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
  /* estilos para cada ítem de resultado */
  /* 1) Fuerza disposición vertical y separación */
  /* contenedor vertical para radios y inputs condicionales */
.radio-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1rem;
}

/* cada opción como tarjeta */
.radio-section .form-label-inline {
  display: flex;
  align-items: center;
  padding: 0.5rem 1rem;
  background: #fafafa;
  border: 2px solid #eee;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background 0.3s, border-color 0.3s, color 0.3s;
}

/* ocultamos el radio nativo */
.radio-section .form-label-inline input[type="radio"] {
  appearance: none;
  -webkit-appearance: none;
  width: 1.2rem;
  height: 1.2rem;
  border: 2px solid #f3545d;
  border-radius: 50%;
  margin-right: 0.75rem;
  position: relative;
  cursor: pointer;
  transition: background 0.3s;
}

/* estado marcado con fondo primario */
.radio-section .form-label-inline input[type="radio"]:checked {
  background: #f3545d;
}

/* punto interior cuando está marcado */
.radio-section .form-label-inline input[type="radio"]:checked::after {
  content: "";
  width: 0.6rem;
  height: 0.6rem;
  background: #fff;
  border-radius: 50%;
  position: absolute;
  top: 0.25rem;
  left: 0.25rem;
}

/* hover sobre toda la fila */
.radio-section .form-label-inline:hover {
  background: #f3545d;
  color: #fff;
  border-color: #f3545d;
}
/* ocultar apariencia nativa */
.form-label-inline input[type="radio"] {
  -webkit-appearance: none;
  appearance: none;
  width: 1.4rem;
  height: 1.4rem;
  border: 2px solid #f3545d;
  border-radius: 50%;
  margin-right: 0.75rem;
  position: relative;
  cursor: pointer;
  transition: background 0.3s, border-color 0.3s;
}

/* hover suave en el círculo */
.form-label-inline input[type="radio"]:hover {
  border-color: #d94454;
}

/* fondo primario cuando está seleccionado */
.form-label-inline input[type="radio"]:checked {
  background: #f3545d;
}

/* punto interno cuando está seleccionado */
.form-label-inline input[type="radio"]:checked::after {
  content: "";
  width: 0.65rem;
  height: 0.65rem;
  background: #fff;
  border-radius: 50%;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

/* deshabilitado (opcional) */
.form-label-inline input[type="radio"]:disabled {
  border-color: #ccc;
  cursor: not-allowed;
}

/* inputs condicionales como tarjeta */
#finalizo-si-section,
#finalizo-no-section,
#trabaja-si-section {
  border: 1px solid #eee;
  padding: 1rem;
  border-radius: 0.5rem;
  background: #fff;
  margin-top: 1rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.checkbox-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 1rem;
}

/* 2) Cada checkbox como tarjeta suave */
.checkbox-section .form-label-inline {
  display: flex;
  align-items: center;
  padding: 0.5rem 1rem;
  background: #fafafa;
  border: 2px solid #eee;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background 0.3s, border-color 0.3s;
}

/* 3) Ocultar el checkbox nativo */
.checkbox-section .form-label-inline input[type="checkbox"] {
  appearance: none;
  -webkit-appearance: none;
  width: 1.2rem;
  height: 1.2rem;
  border: 2px solid #f3545d;
  border-radius: 0.25rem;
  margin-right: 0.75rem;
  position: relative;
  cursor: pointer;
  transition: background 0.3s;
}

/* 4) Estado marcado */
.checkbox-section .form-label-inline input[type="checkbox"]:checked {
  background: #f3545d;
}

/* 5) Check ✓ cuando está marcado */
.checkbox-section .form-label-inline input[type="checkbox"]:checked::after {
  content: "✓";
  position: absolute;
  top: 0;
  left: 0.15rem;
  font-size: 1rem;
  color: #fff;
}

/* 6) Hover suave sobre toda la etiqueta */
.checkbox-section .form-label-inline:hover {
  background: #f3545d;
  color: #fff;
  border-color: #f3545d;
}
.search-results .search-item {
  padding: 0.5rem 1rem;
  cursor: pointer;
  border-bottom: 1px solid #eee;
  transition: background 0.3s, color 0.3s;
}
.search-results .search-item:last-child {
  border-bottom: none;
}
.search-results .search-item:hover {
  background: #f3545d;
  color: #fff;
}
.search-results .no-results {
  padding: 0.5rem 1rem;
  color: #666;
}
    /* Container wrapping everything inside .contenido */
    .custom-container {
      max-width: 900px;
      margin: 2rem auto;
      padding: 1rem;
    }
    /* Form card */
    .custom-container .form-wrapper {
      background: #fff;
      padding: 2rem;
      border-radius: .75rem;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    /* Title */
    .custom-container .form-title {
      font-size: 1.75rem;
      margin-bottom: 1.5rem;
      text-align: center;
      color: #f3545d;
    }
    /* Layout rows */
    .custom-container .row {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 1.25rem;
    }
    /* Each input group */
    .custom-container .input-group {
      flex: 1;
      min-width: 200px;
      position: relative;
    }
    /* Labels */
    .custom-container .form-label {
      display: block;
      margin-bottom: .5rem;
      font-weight: 500;
      color: #333;
    }
    .custom-container .form-label-inline {
      display: inline-flex;
      align-items: center;
      margin: 2px;
      font-weight: 500;
      color: #333;
    }
    /* Inputs / selects */
    .custom-container .form-container__input {
      width: 100%;
      padding: .75rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: .5rem;
      transition: border .3s;
    }
    .custom-container .form-container__input:focus {
      outline: none;
      border-color: #f3545d;
    }
    /* Section headings */
    .custom-container .section-title {
      flex-basis: 100%;
      font-size: 1.25rem;
      margin-bottom: .75rem;
      color: #f3545d;
      border-bottom: 2px solid #f3545d;
      padding-bottom: .5rem;
    }
    /* Submit button */
    .custom-container .btn-submit {
      background: #f3545d;
      color: #fff;
      padding: .75rem 1.5rem;
      border: none;
      border-radius: .5rem;
      font-size: 1rem;
      cursor: pointer;
      transition: background .3s;
    }
    .custom-container .btn-submit:hover {
      background: #d94454;
    }
    /* Autocomplete results */
    .custom-container .search-results {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: #fff;
      border: 1px solid #ccc;
      border-radius: .5rem;
      max-height: 200px;
      overflow-y: auto;
      z-index: 10;
    }
    /* Responsive tweaks */
    @media (max-width: 600px) {
      .custom-container .row {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="contenido">
    <div class="custom-container">
      <div class="form-wrapper">
        <h2 class="form-title">Registro de Estudiante FP</h2>
        <form id="estudianteFPForm" action="./config_alumno_fp/guardar_estudiantefp.php" method="post">
          <input type="hidden" name="alumno_existente" id="alumno_existente" value="0" class="form-container__input full">
          
          <div class="row">
            <div class="input-group">
              <label for="buscar_alumno" class="form-label">Buscar alumno existente:</label>
              <input type="text" id="buscar_alumno" name="buscar_alumno" placeholder="Buscar por DNI, nombre o apellido..." autocomplete="off" class="form-container__input full">
              <div id="resultados_busqueda" class="search-results"></div>
            </div>
          </div>

          <!-- Campos de datos personales -->
          <div class="row">
            <div class="input-group">
              <label for="apellido_alu" class="form-label">Apellido:</label>
              <input type="text" id="apellido_alu" name="apellido_alu" placeholder="Ingrese el apellido" autocomplete="off" required class="form-container__input full">
            </div>
            <div class="input-group">
              <label for="nombre_alu" class="form-label">Nombre:</label>
              <input type="text" id="nombre_alu" name="nombre_alu" placeholder="Ingrese el nombre" autocomplete="off" required class="form-container__input full">
            </div>
          </div>

          <div class="row">
            <div class="input-group">
              <label for="dni_alu" class="form-label">DNI:</label>
              <input type="number" id="dni_alu" name="dni_alu" placeholder="Ingrese el DNI" autocomplete="off" required class="form-container__input full">
            </div>
            <div class="input-group">
              <label for="cuil" class="form-label">CUIL:</label>
              <input type="number" id="cuil" name="cuil" placeholder="Ingrese el cuil" autocomplete="off" required class="form-container__input full">
            </div>
          </div>

          <div class="row">
            <div class="input-group">
              <label for="edad" class="form-label">Fecha de nacimiento:</label>
              <input type="date" id="edad" name="edad" autocomplete="off" class="form-container__input full">
            </div>
          </div>

          <?php
          $conexion->begin_transaction();
          $sql_legajo = "SELECT MAX(legajo_afp) AS max_legajo FROM alumnos_fp";
          $resultado_legajo = $conexion->query($sql_legajo);
          $fila_legajo = $resultado_legajo->fetch_assoc();
          $nuevo_legajo = $fila_legajo['max_legajo'] + 1;
          $legajo_existe = true;
          while ($legajo_existe) {
              $sql_check_legajo = "SELECT COUNT(*) AS cantidad FROM alumno WHERE legajo = $nuevo_legajo";
              $resultado_check = $conexion->query($sql_check_legajo);
              $fila_check = $resultado_check->fetch_assoc();
              if ($fila_check['cantidad'] == 0) { $legajo_existe = false; } else { $nuevo_legajo++; }
          }
          $conexion->commit();
          ?>

          <div class="row">
            <div class="input-group">
              <input type="hidden" id="legajo" name="legajo" value="<?php echo $nuevo_legajo; ?>" class="form-container__input full">
            </div>
            <div class="input-group">
              <label for="observaciones" class="form-label">Observaciones:</label>
              <input type="text" id="observaciones" name="observaciones" placeholder="Observaciones" autocomplete="off" required class="form-container__input full">
            </div>
          </div>

          <!-- Ubicación -->
          <div class="row">
            <div class="input-group">
              <label for="pais" class="form-label">País:</label>
              <select id="pais" name="pais" class="form-container__input full">
                <option value="">Seleccione un país</option>
              </select>
            </div>
            <div class="input-group">
              <label for="provincia" class="form-label">Provincia:</label>
              <select id="provincia" name="provincia" class="form-container__input full" disabled>
                <option value="">Seleccione una provincia</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="input-group">
              <label for="ciudad" class="form-label">Ciudad:</label>
              <select id="ciudad" name="ciudad" class="form-container__input full" disabled>
                <option value="">Seleccione una ciudad</option>
              </select>
            </div>
          </div>

          <!-- Discapacidad -->
          <div class="row">
            <div class="input-group">
              <label class="form-label">¿Tiene alguna discapacidad?</label>
              <div>
                <label class="form-label-inline"><input type="radio" name="discapacidad" id="discapacidad-si" style="margin-right: 6px !important;"> Sí</label>
                <label class="form-label-inline"><input type="radio" name="discapacidad" id="discapacidad-no" style="margin-right: 6px !important;"> No</label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="input-group">
              <label for="discapacidad-input" class="form-label">Describa su discapacidad:</label>
              <input type="text" id="discapacidad-input" name="discapacidad" disabled placeholder="Escriba su discapacidad aquí..." class="form-container__input full">
            </div>
          </div>

          <!-- Nivel secundario -->
          <div class="row radio-section">
            <h3 class="section-title">Datos nivel secundario</h3>
            <label class="form-label">¿Finalizó secundario?</label><br>
            <div>
             <label class="form-label-inline">
                <input type="radio" name="finalizo_secundario" id="secundario-si"> Sí
            </label>
            <label class="form-label-inline">
                <input type="radio" name="finalizo_secundario" id="secundario-no"> No
            </label>
            </div>
          </div>

          <div class="row" id="finalizo-si-section" style="display:none;">
            <div class="input-group">
              <label for="titulo-nivel-medio" class="form-label">Título nivel medio/superior:</label>
              <input type="text" id="titulo-nivel-medio" name="titulo_nivel_medio" placeholder="Ingrese el título" class="form-container__input full">
            </div>
            <div class="input-group">
              <label for="otorgado-por-escuela" class="form-label">Otorgado por escuela:</label>
              <input type="text" id="otorgado-por-escuela" name="otorgado_por_escuela" placeholder="Ingrese el nombre de la escuela" class="form-container__input full">
            </div>
          </div>

          <div class="row" id="finalizo-no-section" style="display:none;">
            <div class="input-group">
              <label for="materias-adeudadas" class="form-label">Cantidad de materias adeudadas:</label>
              <input type="number" id="materias-adeudadas" name="materias_adeudadas" placeholder="Ingrese cantidad de materias" class="form-container__input full">
            </div>
            <div class="input-group">
              <label for="fecha_rendicion" class="form-label">Estimativo de fecha que rendirá:</label>
              <input type="date" id="fecha_rendicion" name="fecha_rendicion" class="form-container__input full">
            </div>
          </div>

          <!-- Datos laborales -->
          <div class="row radio-section">
            <h3 class="section-title">Datos laborales</h3>
            <label class="form-label">¿Trabaja?</label>
            <div>
              <label class="form-label-inline"><input type="radio" name="trabaja" id="trabaja-si"> Sí</label>
              <label class="form-label-inline"><input type="radio" name="trabaja" id="trabaja-no"> No</label>
            </div>
          </div>

          <div class="row" id="trabaja-si-section" style="display:none;">
            <div class="input-group">
              <label for="domicilio-laboral" class="form-label">Domicilio laboral:</label>
              <input type="text" id="domicilio-laboral" name="domicilio_laboral" disabled placeholder="Ingrese domicilio laboral" class="form-container__input full">
            </div>
            <div class="input-group">
              <label for="ocupacion" class="form-label">Ocupación:</label>
              <input type="text" id="ocupacion" name="ocupacion" disabled placeholder="Ingrese su ocupación" class="form-container__input full">
            </div>
            <div class="input-group">
              <label class="form-label" style="margin-right: 50px;">Horario laboral:</label>
              <div class="row">  
                <input type="time"  id="horario-laboral-desde" name="horario_laboral_desde" class="form-container__input full" disabled>
                <input type="time" id="horario-laboral-hasta" name="horario_laboral_hasta" class="form-container__input full" disabled>
              </div>
            </div>
          </div>

          <!-- Domicilio particular -->
          <div class="row">
            <h3 class="section-title">Datos de domicilio</h3>
            <div class="input-group">
              <label for="calle" class="form-label">Calle:</label>
              <input type="text" id="calle" name="calle" placeholder="Calle" class="form-container__input full">
            </div>
            <div class="input-group">
              <label for="barrio" class="form-label">Barrio:</label>
              <input type="text" id="barrio" name="barrio" placeholder="Barrio" class="form-container__input full">
            </div>
          </div>

          <div class="row">
            <div class="input-group">
              <label for="numeracion" class="form-label">Numeración:</label>
              <input type="text" id="numeracion" name="numeracion" placeholder="Numeración" class="form-container__input full">
            </div>
            <div class="input-group">
              <label for="pais-domicilio" class="form-label">País:</label>
              <select id="pais-domicilio" name="pais_domicilio" class="form-container__input full">
                <option value="">País</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="input-group">
              <label for="provincia-domicilio" class="form-label">Provincia:</label>
              <select id="provincia-domicilio" name="provincia_domicilio" class="form-container__input full" disabled>
                <option value="">Provincia</option>
              </select>
            </div>
            <div class="input-group">
              <label for="ciudad-domicilio" class="form-label">Ciudad:</label>
              <select id="ciudad-domicilio" name="ciudad_domicilio" class="form-container__input full" disabled>
                <option value="">Ciudad</option>
              </select>
            </div>
          </div>

          <!-- Contacto -->
          <div class="row">
            <div class="input-group">
              <label for="telefono-celular" class="form-label">Tel. Celular:</label>
              <input type="text" id="telefono-celular" name="celular" placeholder="Tel. Celular" class="form-container__input full">
            </div>
            <div class="input-group">
              <label for="telefono-urgencias" class="form-label">Tel. Urgencias:</label>
              <input type="text" id="telefono-urgencias" name="telefono_urgencias" placeholder="Tel. Urgencias" class="form-container__input full">
            </div>
          </div>

          <div class="row">
            <div class="input-group">
              <label for="correo-electronico" class="form-label">Correo Electrónico:</label>
              <input type="email" id="correo-electronico" name="correo_electronico" placeholder="Correo Electrónico" class="form-container__input full">
            </div>
          </div>

          <!-- Selección de FP -->
          <div class="row checkbox-section">
            <h3 class="section-title">Seleccionar Curso/s</h3>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="carreras_fp[]" value="15">
                Instalador y administrador de redes informáticas
              </label>
            </div>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="carreras_fp[]" value="64">
                Programación
              </label>
            </div>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="carreras_fp[]" value="8">
                Programación WEB
              </label>
            </div>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="carreras_fp[]" value="14">
                Operador de Herramientas de Marketing y Venta Digital
              </label>
            </div>
          </div>

          <!-- Requisitos -->
          <div class="row requisitos checkbox-section">
            <h3 class="section-title">Requisitos presentados</h3>
          </div>
          <div class="row requisitos checkbox-section">
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="requisito1" value="titulo-secundario">
                Original y copia del Título Secundario
              </label>
            </div>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="requisito2" value="fotos">
                2 fotos 4x4
              </label>
            </div>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="requisito3" value="folio">
                Folio A4
              </label>
            </div>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="requisito4" value="dni">
                Fotocopia del DNI
              </label>
            </div>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="requisito5" value="partida-nacimiento">
                Fotocopia de la Partida de Nacimiento
              </label>
            </div>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="requisito6" value="Cuil">
                CUIL
              </label>
            </div>
            <div class="input-group">
              <label class="form-label-inline">
                <input type="checkbox" name="requisito7" value="ayuda-economica">
                $15.000 Ayuda económica voluntaria para gastos de limpieza y administrativos
              </label>
            </div>
          </div>

          <div class="row">
            <button type="submit" class="btn-submit">Enviar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // SweetAlert2 confirmation on submit
    document.getElementById('estudianteFPForm').addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        title: '¿Confirmas el envío?',
        text: 'Se enviará el registro de alumno. ¿Deseas continuar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f3545d',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          this.submit();
        }
      });
    });

    // Aquí puedes agregar tus scripts para manejar selects dependientes,
    // mostrar/ocultar secciones, etc., sin tocar .contenido directamente.
  </script>
   <script>
    $(document).ready(function () {
        const apiKey = 'f7237b8272msh30f1d13e0f262d6p10a31djsn565878f12b95';
        const apiHost = 'wft-geo-db.p.rapidapi.com';
        const latinAmericanCountries = [
            "AR","BO","BR","CL","CO","CR","CU","DO","EC","SV","GT","HN","MX","NI","PA","PY","PE","PR","UY","VE"
        ];
        function cargarPaises(url) {
            $.ajax({ url, method: 'GET', headers: { 'X-RapidAPI-Key': apiKey, 'X-RapidAPI-Host': apiHost }, success(response) {
                    let paisSelect = $('#pais');
                    response.data.forEach(c => latinAmericanCountries.includes(c.code) && paisSelect.append(`<option value="${c.code}">${c.name}</option>`));
                    response.links?.next && cargarPaises(`https://${apiHost}${response.links.next.href}`);
                }, error(xhr, status, error) { console.error('Error:', error); }
            });
        }
        cargarPaises(`https://${apiHost}/v1/geo/countries?namePrefixDefaultLangResults=true&limit=5`);
        $('#pais').change(function () {
            let code = $(this).val();
            $('#provincia').html('<option value="">Seleccione una provincia</option>').prop('disabled', true);
            $('#ciudad').html('<option value="">Seleccione una ciudad</option>').prop('disabled', true);
            if (code) {
                $.ajax({ url: `https://${apiHost}/v1/geo/countries/${code}/regions`, method: 'GET', headers: { 'X-RapidAPI-Key': apiKey, 'X-RapidAPI-Host': apiHost }, success(resp) {
                        let sel = $('#provincia'); resp.data.forEach(r => sel.append(`<option value="${r.code}">${r.name}</option>`)); sel.prop('disabled', false);
                    }
                });
            }
        });
        $('#provincia').change(function () {
            let code = $(this).val(); $('#ciudad').html('<option value="">Seleccione una ciudad</option>').prop('disabled', true);
            code && $.ajax({ url: `https://${apiHost}/v1/geo/regions/${code}/cities`, method: 'GET', headers: { 'X-RapidAPI-Key': apiKey, 'X-RapidAPI-Host': apiHost }, success(resp) {
                        let sel = $('#ciudad'); resp.data.forEach(c => sel.append(`<option value="${c.id}">${c.name}</option>`)); sel.prop('disabled', false);
                    }
                });
        });
    });
  </script>
  
<script>
    const paisSelect = document.getElementById('pais');
    const provinciaSelect = document.getElementById('provincia');
    const ciudadSelect = document.getElementById('ciudad');
    const discapacidadSi = document.getElementById('discapacidad-si');
    const discapacidadNo = document.getElementById('discapacidad-no');
    const discapacidadInput = document.getElementById('discapacidad-input');
    const secundarioSi = document.getElementById('secundario-si');
    const secundarioNo = document.getElementById('secundario-no');
    const finalizoSiSection = document.getElementById('finalizo-si-section');
    const finalizoNoSection = document.getElementById('finalizo-no-section');
    const trabajaSi = document.getElementById('trabaja-si');
    const trabajaNo = document.getElementById('trabaja-no');
    const trabajaSiSection = document.getElementById('trabaja-si-section');
    const domicilioLaboral = document.getElementById('domicilio-laboral');
    const ocupacion = document.getElementById('ocupacion');
    const horarioLaboralDesde = document.getElementById('horario-laboral-desde');
    const horarioLaboralHasta = document.getElementById('horario-laboral-hasta');   
    // Lista de países de Latinoamérica
    const latinAmericanCountries = [
        'Argentina', 'Bolivia', 'Brazil', 'Chile', 'Colombia', 'Costa Rica',
        'Cuba', 'Dominican Republic', 'Ecuador', 'El Salvador', 'Guatemala',
        'Honduras', 'Mexico', 'Nicaragua', 'Panama', 'Paraguay', 'Peru',
        'Uruguay', 'Venezuela'
    ];

    // Fetch countries and filter to show only Latin American countries
    fetch('https://countriesnow.space/api/v0.1/countries')
    .then(response => response.json())
    .then(data => {
        data.data.forEach(country => {
            if (latinAmericanCountries.includes(country.country)) {
                const option = document.createElement('option');
                option.value = country.country;
                option.textContent = country.country;
                paisSelect.appendChild(option);
            }
        });
    });

    // Fetch states (provinces)
    paisSelect.addEventListener('change', function() {
        const countryName = this.value;
        provinciaSelect.disabled = true;
        ciudadSelect.disabled = true;
        ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
        provinciaSelect.innerHTML = '<option value="">Seleccione una provincia</option>';

        if (countryName) {
            fetch('https://countriesnow.space/api/v0.1/countries/states', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ country: countryName })
            })
            .then(response => response.json())
            .then(data => {
                data.data.states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.name;
                    option.textContent = state.name;
                    provinciaSelect.appendChild(option);
                });
                provinciaSelect.disabled = false;
            });
        }
    });

    // Fetch cities based on selected province
    provinciaSelect.addEventListener('change', function() {
        const countryName = paisSelect.value;
        const stateName = this.value;
        ciudadSelect.disabled = true;
        ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';

        if (stateName) {
            fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ country: countryName, state: stateName })
            })
            .then(response => response.json())
            .then(data => {
                data.data.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    ciudadSelect.appendChild(option);
                });
                ciudadSelect.disabled = false;
            });
        }
    });

 // Manejar la selección de discapacidad
discapacidadSi.addEventListener('change', function() {
    if (this.checked) {
        discapacidadInput.disabled = false;
        discapacidadInput.readOnly = false;
        discapacidadInput.value = '';  // Limpiar el valor
        discapacidadInput.placeholder = "Escriba su discapacidad aquí...";
    }
});

discapacidadNo.addEventListener('change', function() {
    if (this.checked) {
        discapacidadInput.disabled = false;
        discapacidadInput.readOnly = true;  // Hacer el campo solo de lectura
        discapacidadInput.value = 'No posee';  // Asignar el valor "No posee"
        discapacidadInput.placeholder = "No posee discapacidad";
    }
});
    // Manejar la selección de si finalizó secundario
    secundarioSi.addEventListener('change', function() {
        if (this.checked) {
            finalizoSiSection.style.display = 'block';
            finalizoNoSection.style.display = 'none';
        }
    });

    secundarioNo.addEventListener('change', function() {
        if (this.checked) {
            finalizoSiSection.style.display = 'none';
            finalizoNoSection.style.display = 'block';
        }
    });
    
   // Manejar la selección de si trabaja
trabajaSi.addEventListener('change', function() {
    if (this.checked) {
        trabajaSiSection.style.display = 'block'; // Mostrar la sección
        domicilioLaboral.disabled = false;
        ocupacion.disabled = false;
        horarioLaboralDesde.disabled = false;
        horarioLaboralHasta.disabled = false;

        // Limpiar los valores y placeholders
        domicilioLaboral.value = '';
        ocupacion.value = '';
        horarioLaboralDesde.value = '';
        horarioLaboralHasta.value = '';
        domicilioLaboral.placeholder = 'Ingrese domicilio laboral';
        ocupacion.placeholder = 'Ingrese su ocupación';
    }
});

trabajaNo.addEventListener('change', function() {
    if (this.checked) {
        trabajaSiSection.style.display = 'block'; // Puedes mantenerlo visible o no
        domicilioLaboral.value = 'no trabaja';  // Mostrar "no trabaja"
        ocupacion.value = 'no trabaja';  // Mostrar "no trabaja"
        horarioLaboralDesde.value = '00:00';  // Establecer hora en 00:00
        horarioLaboralHasta.value = '00:00';  // Establecer hora en 00:00

        // Cambiar a solo lectura en lugar de deshabilitar
        domicilioLaboral.readOnly = true;
        ocupacion.readOnly = true;
        horarioLaboralDesde.readOnly = true;
        horarioLaboralHasta.readOnly = true;
    }
});

     const paisDomicilioSelect = document.getElementById('pais-domicilio');
    const provinciaDomicilioSelect = document.getElementById('provincia-domicilio');
    const ciudadDomicilioSelect = document.getElementById('ciudad-domicilio');

    // Fetch countries
    fetch('https://countriesnow.space/api/v0.1/countries')
    .then(response => response.json())
    .then(data => {
        data.data.forEach(country => {
            const option = document.createElement('option');
            option.value = country.country;
            option.textContent = country.country;
            paisDomicilioSelect.appendChild(option);
        });
    });

    // Fetch provinces based on selected country
    paisDomicilioSelect.addEventListener('change', function() {
        const countryName = this.value;
        provinciaDomicilioSelect.disabled = true;
        ciudadDomicilioSelect.disabled = true;
        ciudadDomicilioSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
        provinciaDomicilioSelect.innerHTML = '<option value="">Seleccione una provincia</option>';

        if (countryName) {
            fetch('https://countriesnow.space/api/v0.1/countries/states', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ country: countryName })
            })
            .then(response => response.json())
            .then(data => {
                data.data.states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.name;
                    option.textContent = state.name;
                    provinciaDomicilioSelect.appendChild(option);
                });
                provinciaDomicilioSelect.disabled = false;
            });
        }
    });
    // Fetch cities based on selected province
    provinciaDomicilioSelect.addEventListener('change', function() {
        const countryName = paisDomicilioSelect.value;
        const stateName = this.value;
        ciudadDomicilioSelect.disabled = true;
        ciudadDomicilioSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';

        if (stateName) {
            fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ country: countryName, state: stateName })
            })
            .then(response => response.json())
            .then(data => {
                data.data.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    ciudadDomicilioSelect.appendChild(option);
                });
                ciudadDomicilioSelect.disabled = false;
            });
        }
    });

    const telefonoCelularInput = document.getElementById('telefono-celular');
    const telefonoUrgenciasInput = document.getElementById('telefono-urgencias');
    const correoElectronicoInput = document.getElementById('correo-electronico');
    
    // Validar inputs cuando se pierda el foco
    telefonoCelularInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.value = '0';
        }
    });

    telefonoUrgenciasInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.value = '0';
        }
    });

    correoElectronicoInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.value = 'no se relleno';
        }
    });
    
    
    document.querySelector('form').addEventListener('submit', function(event) {
    const requisitos = [
        document.querySelector('input[name="requisito1"]'),
        document.querySelector('input[name="requisito2"]'),
        document.querySelector('input[name="requisito3"]'),
        document.querySelector('input[name="requisito4"]'),
        document.querySelector('input[name="requisito5"]'),
        document.querySelector('input[name="requisito6"]'),
        document.querySelector('input[name="requisito7"]')
    ];

    let presentoRequisitos = false;

    requisitos.forEach(function(requisito) {
        if (requisito && requisito.checked) {
            presentoRequisitos = true;
        }
    });

    if (presentoRequisitos) {
        alert("Presentó todos los requisitos.");
    } else {
        alert("No presentó ningún requisito.");
    }
});




//     Auto completado de Estuidiante TEcnicatura Y Serch 

// ======================== BUSCADOR DE ALUMNO ========================

document.getElementById("buscar_alumno").addEventListener("keyup", function () {
  const query = this.value.trim();
  const resultados = document.getElementById("resultados_busqueda");

  if (query.length < 3) {
    resultados.innerHTML = "";
    return;
  }

  fetch("./config_alumno_fp/buscar_alumno.php?q=" + encodeURIComponent(query))
    .then(res => res.json())
    .then(data => {
      resultados.innerHTML = "";
      if (data.length === 0) {
        const noRes = document.createElement("div");
        noRes.textContent = "No se encontraron resultados";
        noRes.classList.add("no-results");
        resultados.appendChild(noRes);
      } else {
        data.forEach(alumno => {
          const div = document.createElement("div");
          div.textContent = `${alumno.apellido_alumno}, ${alumno.nombre_alumno} (${alumno.dni_alumno})`;
          div.classList.add("search-item");
          div.addEventListener("click", function () {
            completarFormulario(alumno);
            resultados.innerHTML = "";
            document.getElementById("buscar_alumno").value =
              `${alumno.apellido_alumno}, ${alumno.nombre_alumno}`;
          });
          resultados.appendChild(div);
        });
      }
    });
});

function completarFormulario(a) {
    document.getElementById("apellido_alu").value = a.apellido_alumno || "";
    document.getElementById("nombre_alu").value = a.nombre_alumno || "";
    document.getElementById("dni_alu").value = a.dni_alumno || "";
    document.getElementById("legajo").value = a.legajo || "";
    document.getElementById("edad").value = a.fecha_nacimiento || "";
    document.getElementById("cuil").value = a.cuil || "";
    document.getElementById("telefono-celular").value = a.celular || "";
    document.getElementById("telefono-urgencias").value = a.telefono_urgencias || "";
    document.getElementById("correo-electronico").value = a.correo || "";

    // Datos laborales
    document.getElementById("ocupacion").value = a.ocupacion || "";
    document.getElementById("domicilio-laboral").value = a.domicilio_laboral || "";
    document.getElementById("horario-laboral-desde").value = a.horario_laboral_desde || "";
    document.getElementById("horario-laboral-hasta").value = a.horario_laboral_hasta || "";

    // Domicilio
    document.getElementById("calle").value = a.calle_domicilio || "";
    document.getElementById("barrio").value = a.barrio_domicilio || "";
    document.getElementById("numeracion").value = a.numeracion_domicilio || "";

    // Secundario
    document.getElementById("titulo-nivel-medio").value = a.Titulo_secundario || "";
    document.getElementById("otorgado-por-escuela").value = a.escuela_secundaria || "";
    document.getElementById("materias-adeudadas").value = a.materias_adeuda || "";
    document.getElementById("fecha_rendicion").value = a.fecha_estimacion || "";

    // Discapacidad
    const discapacidadInput = document.getElementById("discapacidad-input");
    if (a.discapacidad && a.discapacidad.toLowerCase() !== 'no posee') {
        document.getElementById("discapacidad-si").checked = true;
        discapacidadInput.disabled = false;
        discapacidadInput.readOnly = false;
        discapacidadInput.value = a.discapacidad;
    } else {
        document.getElementById("discapacidad-no").checked = true;
        discapacidadInput.disabled = false;
        discapacidadInput.readOnly = true;
        discapacidadInput.value = "No posee";
    }

    // ¿Trabaja?
    if (a.Trabaja_Horario && a.Trabaja_Horario.toLowerCase() !== 'no trabaja') {
        document.getElementById("trabaja-si").checked = true;
        trabajaSiSection.style.display = 'block';
        domicilioLaboral.disabled = false;
        ocupacion.disabled = false;
        horarioLaboralDesde.disabled = false;
        horarioLaboralHasta.disabled = false;
    } else {
        document.getElementById("trabaja-no").checked = true;
        trabajaSiSection.style.display = 'block';
        domicilioLaboral.readOnly = true;
        ocupacion.readOnly = true;
        horarioLaboralDesde.readOnly = true;
        horarioLaboralHasta.readOnly = true;
        domicilioLaboral.value = "no trabaja";
        ocupacion.value = "no trabaja";
        horarioLaboralDesde.value = "00:00";
        horarioLaboralHasta.value = "00:00";
    }
    document.getElementById("alumno_existente").value = "1";
}

</script>
