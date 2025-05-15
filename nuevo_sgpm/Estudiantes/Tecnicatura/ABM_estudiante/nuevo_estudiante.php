<?php
include'../../../layout.php'
?>
<script>
    $(document).ready(function () {
        const apiKey = 'f7237b8272msh30f1d13e0f262d6p10a31djsn565878f12b95';  // Tu clave de API
        const apiHost = 'wft-geo-db.p.rapidapi.com';
        const latinAmericanCountries = [
            "AR", "BO", "BR", "CL", "CO", "CR", "CU", "DO", "EC", 
            "SV", "GT", "HN", "MX", "NI", "PA", "PY", "PE", 
            "PR", "UY", "VE"
        ];

        // Función para cargar todos los países con paginación
        function cargarPaises(url) {
            $.ajax({
                url: url,
                method: 'GET',
                headers: {
                    'X-RapidAPI-Key': apiKey,
                    'X-RapidAPI-Host': apiHost
                },
                success: function (response) {
                    let paisSelect = $('#pais');
                    response.data.forEach(country => {
                        if (latinAmericanCountries.includes(country.code)) {
                            paisSelect.append(`<option value="${country.code}">${country.name}</option>`);
                        }
                    });

                    // Si hay más páginas, hacer la siguiente solicitud
                    if (response.links && response.links.next) {
                        cargarPaises(`https://${apiHost}${response.links.next.href}`);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                }
            });
        }

        // Iniciar la carga de países desde la primera página con un límite de 5
        cargarPaises(`https://${apiHost}/v1/geo/countries?namePrefixDefaultLangResults=true&limit=5`);

        // Cuando se selecciona un país, cargar las provincias
        $('#pais').change(function () {
            var countryCode = $(this).val();
            $('#provincia').html('<option value="">Seleccione una provincia</option>');
            $('#ciudad').html('<option value="">Seleccione una ciudad</option>');

            if (countryCode) {
                $.ajax({
                    url: `https://${apiHost}/v1/geo/countries/${countryCode}/regions`,
                    method: 'GET',
                    headers: {
                        'X-RapidAPI-Key': apiKey,
                        'X-RapidAPI-Host': apiHost
                    },
                    success: function (response) {
                        let provinciaSelect = $('#provincia');
                        response.data.forEach(region => {
                            provinciaSelect.append(`<option value="${region.code}">${region.name}</option>`);
                        });
                        $('#provincia').prop('disabled', false);
                    }
                });
            }
        });
        $('#provincia').change(function () {
            var regionCode = $(this).val();
            $('#ciudad').html('<option value="">Seleccione una ciudad</option>');

            if (regionCode) {
                $.ajax({
                    url: `https://${apiHost}/v1/geo/regions/${regionCode}/cities`,
                    method: 'GET',
                    headers: {
                        'X-RapidAPI-Key': apiKey,
                        'X-RapidAPI-Host': apiHost
                    },
                    success: function (response) {
                        let ciudadSelect = $('#ciudad');
                        response.data.forEach(city => {
                            ciudadSelect.append(`<option value="${city.id}">${city.name}</option>`);
                        });
                        $('#ciudad').prop('disabled', false);
                    }
                });
            }
        });
    });
</script>
</head>
<body>
<div class="contenido">
    <h2 class="form-container__h2">Registro de Estudiante</h2>
    <form action="guardar_estudiante.php" method="post" class="form-container">
        <div class="row">
            <div class="input-group">
                <label for="apellido_alu">Apellido:</label>
                <input type="text" class="form-container__input full" id="apellido_alu" name="apellido_alu" placeholder="Ingrese el apellido" autocomplete="off" required>
            </div>
            <div class="input-group">
                <label for="nombre_alu">Nombre:</label>
                <input type="text" class="form-container__input full" id="nombre_alu" name="nombre_alu" placeholder="Ingrese el nombre" autocomplete="off" required>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="dni_alu">DNI:</label>
                <input type="number" class="form-container__input full" id="dni_alu" name="dni_alu" placeholder="Ingrese el DNI" autocomplete="off" required>
            </div>
            <div class="input-group">
                <label for="cuil">CUIL:</label>
                <input type="number" class="form-container__input full" id="cuil" name="cuil" placeholder="Ingrese el cuil" autocomplete="off" required>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="edad">Fecha de nacimiento:</label>
                <input type="date" class="form-container__input full" id="edad" name="edad" autocomplete="off">
            </div>
        </div>
        <?php
// Iniciar transacción para asegurar consistencia
$conexion->begin_transaction();
// Obtener el número máximo de legajo actual
$sql_legajo = "SELECT MAX(legajo) AS max_legajo FROM alumno";
$resultado_legajo = $conexion->query($sql_legajo);
$fila_legajo = $resultado_legajo->fetch_assoc();
$nuevo_legajo = $fila_legajo['max_legajo'] + 1;
// Verificar si el número de legajo ya existe
$legajo_existe = true;
while ($legajo_existe) {
    // Verificar si el legajo ya existe en la base de datos
    $sql_check_legajo = "SELECT COUNT(*) AS cantidad FROM alumno WHERE legajo = $nuevo_legajo";
    $resultado_check = $conexion->query($sql_check_legajo);
    $fila_check = $resultado_check->fetch_assoc();

    if ($fila_check['cantidad'] == 0) {
        // Si el legajo no existe, lo utilizamos
        $legajo_existe = false;
    } else {
        // Si el legajo ya existe, incrementar y verificar nuevamente
        $nuevo_legajo++;
    }
}
$conexion->commit();
?>
        <div class="row">
            <div class="input-group">
                <input type="hidden" id="legajo" name="legajo" value="<?php echo $nuevo_legajo; ?>" class="form-container__input full">
            </div>
            <div class="input-group">
                <label for="observaciones">Observaciones:</label>
                <input type="text" class="form-container__input full" id="observaciones" name="observaciones" placeholder="Observaciones" autocomplete="off" required>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="pais">País:</label>
                <select id="pais" name="pais" class="form-container__input full">
                    <option value="">Seleccione un país</option>
                </select>
            </div>
            <div class="input-group">
                <label for="provincia">Provincia:</label>
                <select id="provincia" name="provincia" class="form-container__input full" disabled>
                    <option value="">Seleccione una provincia</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="ciudad">Ciudad:</label>
                <select id="ciudad" name="ciudad" class="form-container__input full" disabled>
                    <option value="">Seleccione una ciudad</option>
                </select>
            </div>
        </div>
        <div class="row">
            <label>¿Tiene alguna discapacidad?</label>
            <div>
                <label>
                    <input type="radio" name="discapacidad" id="discapacidad-si"> Sí
                </label>
                <label>
                    <input type="radio" name="discapacidad" id="discapacidad-no"> No
                </label>
            </div>
        </div>
        <div class="row">
            <label for="discapacidad">Describa su discapacidad:</label>
            <input type="text" id="discapacidad-input" name="discapacidad" class="form-container__input full" disabled placeholder="Escriba su discapacidad aquí...">
        </div>
        <div class="row">
            <h3>Datos nivel secundario</h3>
            <label>¿Finalizó secundario?</label>
            <div>
                <label>
                    <input type="radio" name="finalizo_secundario" id="secundario-si"> Sí
                </label>
                <label>
                    <input type="radio" name="finalizo_secundario" id="secundario-no"> No
                </label>
            </div>
        </div>
        <div class="row" id="finalizo-si-section" style="display:none;">
            <div class="input-group">
                <label for="titulo-nivel-medio">Título nivel medio/superior:</label>
                <input type="text" id="titulo-nivel-medio" name="titulo_nivel_medio" class="form-container__input full" placeholder="Ingrese el título">
            </div>
            <div class="input-group">
                <label for="otorgado-por-escuela">Otorgado por escuela:</label>
                <input type="text" id="otorgado-por-escuela" name="otorgado_por_escuela" class="form-container__input full" placeholder="Ingrese el nombre de la escuela">
            </div>
        </div>
        <div class="row" id="finalizo-no-section" style="display:none;">
            <div class="input-group">
                <label for="materias-adeudadas">Cantidad de materias adeudadas:</label>
                <input type="number" id="materias-adeudadas" name="materias_adeudadas" class="form-container__input full" placeholder="Ingrese cantidad de materias">
            </div>
            <div class="input-group">
                <label for="fecha-rendicion">Estimativo de fecha que rendirá:</label>
                <input type="date" id="fecha_rendicion" name="fecha_rendicion" class="form-container__input full">
            </div>
        </div>
       <div class="row">
    <h3>Datos laborales</h3>
    <label>¿Trabaja?</label>
    <div>
        <label>
            <input type="radio" name="trabaja" id="trabaja-si"> Sí
        </label>
        <label>
            <input type="radio" name="trabaja" id="trabaja-no"> No
        </label>
    </div>
</div>
<div class="row" id="trabaja-si-section" style="display:none;">
    <div class="input-group">
        <label for="domicilio-laboral">Domicilio laboral:</label>
        <input type="text" id="domicilio-laboral" name="domicilio_laboral" class="form-container__input full" placeholder="Ingrese domicilio laboral" disabled>
    </div>
    <div class="input-group">
        <label for="ocupacion">Ocupación:</label>
        <input type="text" id="ocupacion" name="ocupacion" class="form-container__input full" placeholder="Ingrese su ocupación" disabled>
    </div>
    <div class="input-group">
        <label for="horario-laboral">Horario laboral:</label>
        <div>
            <input type="time" id="horario-laboral-desde" name="horario_laboral_desde" class="form-container__input full" placeholder="Desde" disabled>
            <input type="time" id="horario-laboral-hasta" name="horario_laboral_hasta" class="form-container__input full" placeholder="Hasta" disabled>
        </div>
    </div>
</div>
        <div class="row">
            <h3>Datos de domicilio</h3>
            <div class="input-group">
                <label for="calle">Calle:</label>
                <input type="text" id="calle" name="calle" class="form-container__input full" placeholder="Calle">
            </div>
            <div class="input-group">
                <label for="barrio">Barrio:</label>
                <input type="text" id="barrio" name="barrio" class="form-container__input full" placeholder="Barrio">
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="numeracion">Numeración:</label>
                <input type="text" id="numeracion" name="numeracion" class="form-container__input full" placeholder="Numeración">
            </div>
            <div class="input-group">
                <label for="pais-domicilio">País:</label>
                <select id="pais-domicilio" class="form-container__input full">
                    <option value="">País</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="provincia-domicilio">Provincia:</label>
                <select id="provincia-domicilio" name="provincia_domicilio" class="form-container__input full" disabled>
                    <option value="">Provincia</option>
                </select>
            </div>
            <div class="input-group">
                <label for="ciudad-domicilio">Ciudad:</label>
                <select id="ciudad-domicilio" name="ciudad_domicilio" class="form-container__input full" disabled>
                    <option value="">Ciudad</option>
                </select>
            </div>
        </div>
        <div class="row">
            <h3>Datos de contacto</h3>
            <div class="input-group">
                <label for="telefono-celular">Tel. Celular:</label>
                <input type="text" id="telefono-celular" name="celular" class="form-container__input full" placeholder="Tel. Celular">
            </div>
            <div class="input-group">
                <label for="telefono-urgencias">Tel. Urgencias:</label>
                <input type="text" id="telefono-urgencias" name="telefono_urgencias" class="form-container__input full" placeholder="Tel. Urgencias">
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="correo-electronico">Correo Electrónico:</label>
                <input type="email" id="correo-electronico" name="correo_electronico" class="form-container__input full" placeholder="Correo Electrónico">
            </div>
        </div>
        <div class="row">
    <h3>Matriculación</h3>
    <label>Carrera:</label>
    <div>
        <label>
            <input type="radio" name="carrera" value="Técnico Superior en Enfermería"> Técnico Superior en Enfermería
        </label>
        <label>
            <input type="radio" name="carrera" value="Técnico Superior en Acompañamiento Terapéutico"> Técnico Superior en Acompañamiento Terapéutico
        </label>
        <label>
            <input type="radio" name="carrera" value="Técnico Superior en Comercialización y Marketing"> Técnico Superior en Comercialización y Marketing
        </label>
        <label>
            <input type="radio" name="carrera" value="Técnico Superior en Automatización y Robótica"> Técnico Superior en Automatización y Robótica
        </label>
    </div>
</div>
        <div class="row">
    <h3>Requisitos presentados</h3>
</div>
<div class="row requisitos">
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito1" value="titulo-secundario"> Original y copia del Título Secundario
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito2" value="fotos"> 2 fotos 4x4
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito3" value="folio"> Folio A4
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito4" value="dni"> Fotocopia del DNI
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito5" value="partida-nacimiento"> Fotocopia de la Partida de Nacimiento
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito6" value="Cuil"> CUIL
        </label>
    </div>
    <div class="input-group">
        <label>
            <input type="checkbox" name="requisito7" value="ayuda-economica"> $15.000 Ayuda económica voluntaria para gastos de limpieza (lavandina, cera, trapos de piso, etc.) y gastos administrativos (hojas, carpetas, tóner, etc.).
        </label>
    </div>
    
</div>
<?php
$sql_mater = "SELECT * FROM carreras WHERE idCarrera IN (18,27,55,46)";
$peticion = mysqli_query($conexion, $sql_mater);
?>

<select name="inscripcion_carrera" id="inscripcion_carrera" class="form-container__input full" onchange="cargarComisiones()">
    <option hidden>Selecciona una carrera</option>
    <?php while ($informacion = mysqli_fetch_assoc($peticion)) { ?>
        <option value="<?php echo $informacion['idCarrera']; ?>">
            <?php echo $informacion['nombre_carrera']; ?>
        </option>
    <?php } ?>
</select>
<?php
$queryCursos = "SELECT * FROM cursos WHERE idCursos = 1";
$resultCursos = mysqli_query($conexion, $queryCursos);
?>
<select name="curso" id="curso" class="form-container__input full" onchange="cargarComisiones()">
    <option hidden>Selecciona un Curso</option>
    <?php while ($curso = mysqli_fetch_assoc($resultCursos)) { ?>
        <option value="<?php echo $curso['idCursos']; ?>">
            <?php echo $curso['curso']; ?>
        </option>
    <?php } ?>
</select>
<select name="comision" id="comision" class="form-container__input full">
    <option hidden>Selecciona una Comisión</option>
</select>
<script>
function cargarComisiones() {
    var carreraId = document.getElementById('inscripcion_carrera').value;
    var cursoId = document.getElementById('curso').value;

    if (carreraId && cursoId) { // Solo realizar la solicitud si ambos selects tienen un valor
        var xhr = new XMLHttpRequest();
        xhr.open('POST', './config_estu/obtener_comisiones.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status == 200) {
                document.getElementById('comision').innerHTML = this.responseText;
            }
        };
        xhr.send('carreraId=' + carreraId + '&cursoId=' + cursoId);
    } else {
        document.getElementById('comision').innerHTML = '<option hidden>Selecciona una Comisión</option>';
    }
}
</script>
        <input type="submit" class="form-container__input" name="enviar" value="Enviar" onclick="mostrarAlertaExitosa(); closeSuccessMessage();">
    </form>
</div>
<script>
 function mostrarAlertaExitosa() {
    alert("Registro completado con éxito!");
}

function closeSuccessMessage() {
    // Aquí puedes agregar código para cerrar cualquier mensaje de éxito o realizar acciones después del envío
    console.log("El formulario se ha enviado correctamente.");
}
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

</script>
<style>
    .form-container {
        max-width: 75%;
        margin: 0 auto;
        padding: 50px;
        background-color: #0036ff25;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-container__h2 {
        text-align: center;
        color: #f3545d;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .form-container__input {
        width: 100%;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .input-group {
        width: 48%;
    }

    .row label {
        font-weight: bold;
        color: #f3545d;
        margin-bottom: 5px;
        display: block;
    }

    .row h3 {
        width: 100%;
        color: #f3545d;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .row div {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    input[type="submit"] {
        background-color: #f3545d;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 10px 20px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
        width: 100%;
    }

    input[type="submit"]:hover {
        background-color: #8c1b1b;
    }
    /* Estilos para los radios personalizados */
input[type="radio"] {
    appearance: none;
    -webkit-appearance: none;
    background-color: #fff;
    border: 2px solid #b71c1c;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    cursor: pointer;
    outline: none;
    transition: background 0.3s ease;
}

input[type="radio"]:checked {
    background-color: #b71c1c;
    border-color: #b71c1c;
}

input[type="radio"]::after {
    content: '';
    display: block;
    width: 10px;
    height: 10px;
    margin: 5px;
    border-radius: 50%;
    background-color: #fff;
    transition: background 0.3s ease;
}

input[type="radio"]:checked::after {
    background-color: #fff;
}
input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    background-color: #fff;
    border: 2px solid #f3545d;
    border-radius: 4px;
    width: 20px;
    height: 20px;
    cursor: pointer;
    outline: none;
    transition: background-color 0.3s ease, border-color 0.3s ease;
    position: relative;
}

input[type="checkbox"]:checked {
    background-color: #b71c1c;
    border-color: #b71c1c;
}

input[type="checkbox"]::after {
    content: '';
    position: absolute;
    top: 4px;
    left: 4px;
    width: 10px;
    height: 10px;
    background-color: white;
    display: none;
}

input[type="checkbox"]:checked::after {
    display: block;
}
.requisitos {
    margin-top: 20px;
}

.requisitos .input-group {
    width: 100%;
    margin-bottom: 10px;
}

.requisitos label {
    font-weight: normal;
    color: #333;
}

.requisitos input[type="checkbox"] {
    margin-right: 10px;
}

</style>
</body>
</html>