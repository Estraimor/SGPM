<?php
include 'layout.php'; 
session_start();

$carrera  = $_POST['carrera']  ?? null;
$curso    = $_POST['curso']    ?? null;
$comision = $_POST['comision'] ?? null;
$anio     = $_POST['anio']     ?? null;

// Para las materias se espera un array; si se envía como string se transforma en array
$materiasRaw = $_POST['materias'] ?? [];
if (!is_array($materiasRaw)) {
    $materiasRaw = explode(',', $materiasRaw);
}
$materiasArray = array_filter(array_map('trim', $materiasRaw));

// Validar que se tenga al menos una carrera, curso, comisión, año y al menos una materia
if (!$carrera || !$curso || !$comision || !$anio || empty($materiasArray)) {
    $_SESSION['error_message'] = "Debes seleccionar Carrera, Curso, Comisión, Año y al menos una materia.";
    header("Location: pre_libro.php");
    exit;
}

// Creamos un string de IDs separados por comas para las materias
$materiasString = implode(',', array_map('intval', $materiasArray));

// Consultar el nombre de la Carrera
$sqlCarrera = "SELECT nombre_carrera FROM carreras WHERE idCarrera = " . intval($carrera);
$resultadoCarrera = $conexion->query($sqlCarrera);
if ($resultadoCarrera && $resultadoCarrera->num_rows > 0) {
    $filaCarrera = $resultadoCarrera->fetch_assoc();
    $nombreCarrera = $filaCarrera['nombre_carrera'];
} else {
    $nombreCarrera = "Carrera no encontrada";
}

// Consultar el nombre del Curso
$sqlCurso = "SELECT curso FROM cursos WHERE idCursos = " . intval($curso);
$resultadoCurso = $conexion->query($sqlCurso);
if ($resultadoCurso && $resultadoCurso->num_rows > 0) {
    $filaCurso = $resultadoCurso->fetch_assoc();
    $nombreCurso = $filaCurso['curso'];
} else {
    $nombreCurso = "Curso no encontrado";
}

// Consultar el nombre de la Comisión
$sqlComision = "SELECT comision FROM comisiones WHERE idComisiones = " . intval($comision);
$resultadoComision = $conexion->query($sqlComision);
if ($resultadoComision && $resultadoComision->num_rows > 0) {
    $filaComision = $resultadoComision->fetch_assoc();
    $nombreComision = $filaComision['comision'];
} else {
    $nombreComision = "Comisión no encontrada";
}

// Consultar los nombres de las Materias seleccionadas
$materiasIn = implode(',', array_map('intval', $materiasArray));
$nombresMaterias = [];
if (!empty($materiasIn)) {
    $sqlMaterias = "SELECT Nombre FROM materias WHERE idMaterias IN ($materiasIn)";
    $resultadoMaterias = $conexion->query($sqlMaterias);
    if ($resultadoMaterias && $resultadoMaterias->num_rows > 0) {
        while ($filaMateria = $resultadoMaterias->fetch_assoc()) {
            $nombresMaterias[] = $filaMateria['Nombre'];
        }
    }
}
$materiasTexto = !empty($nombresMaterias)
                 ? implode(", ", $nombresMaterias)
                 : "No se encontraron materias";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- Meta viewport para la correcta visualización en móviles -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <style>
        
        .tabla-elegante {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        .tabla-elegante thead th {
            background-color: #f3545d;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .tabla-elegante tbody td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            word-wrap: break-word;
        }
        .tabla-elegante tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .tabla-elegante tbody tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }
        /* Inputs y textarea al 100% */
        input[type="date"], textarea, input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #f3545d;
            border-radius: 5px;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            min-height: 25px;
        }
        input[type="date"]:focus, textarea:focus, input[type="text"]:focus {
            outline: none;
            border-color: #ff002b;
            box-shadow: 0 0 8px rgba(199, 0, 57, 0.3);
        }
        /* Botón Cargar Día */
        .btn-enviar {
            background: linear-gradient(145deg, #f3545d, #ff002b);
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s;
        }
        .btn-enviar:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            background: linear-gradient(145deg, #ff002b, #f3545d);
        }
        .btn-enviar:active {
            transform: scale(0.95);
        }
        /* Modal */
        .modal {
            display: none;
            position: fixed;
          
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            animation: modalFadeIn 0.5s;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-content {
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            transform: scale(0.9);
            animation: modalZoomIn 0.3s forwards;
        }
        @keyframes modalZoomIn {
            from { transform: scale(0.9); }
            to { transform: scale(1); }
        }
        .close {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #fff;
            color: #333;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-top: 50px;
            z-index: 999999999999999;
        }
        textarea.input-editar {
    width: 100%;
    font-family: inherit;
    font-size: 14px;
    border: 2px solid #f3545d;
    border-radius: 5px;
    padding: 8px;
    resize: none;
    overflow: hidden;
    box-sizing: border-box;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
textarea.input-editar:focus {
    outline: none;
    border-color: #ff002b;
    box-shadow: 0 0 8px rgba(199, 0, 57, 0.3);
}
        .close:hover {
            background-color: #f3545d;
            color: #fff;
            transform: scale(1.1);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .modal-header h2 {
            margin: 0;
            font-size: 1.5em;
            color: #333;
        }
        /* Botones para acciones en la tabla */
        .btn-confirmar, .btn-cancelar {
            color: white;
            margin-bottom: 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn-confirmar { background-color: #28a745; }
        .btn-confirmar:hover { background-color: #218838; }
        .btn-cancelar { background-color: #dc3545; }
        .btn-cancelar:hover { background-color: #c82333; }
        .btn-modificar {
            background-color: #ffc107;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            margin-bottom: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn-modificar:hover { background-color: #e0a800; }
        .btn-borrar {
            background-color: #dc3545;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            margin: 0 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn-borrar:hover { background-color: #c82333; }
        /* Contenedor responsive para la tabla */
        .table-responsive { overflow-x: auto; }
        
        /* Media queries para dispositivos móviles */
        @media (max-width: 768px) {
            .contenido { padding: 10px; }
            h1 { font-size: 1.5rem; }
            .tabla-elegante thead th, .tabla-elegante tbody td { padding: 8px; font-size: 0.9rem; }
            .btn-enviar, .btn-modificar, .btn-borrar, .btn-confirmar, .btn-cancelar { font-size: 0.8rem; padding: 8px 15px; }
            .modal-content { width: 95%; padding: 20px; }
            
            
        }
          @media (max-width: 768px){
              .modal-content{
                margin-top: 65px;
            }
          }
        @media (max-width: 480px) {
            .tabla-elegante thead th, .tabla-elegante tbody td { padding: 6px; font-size: 0.8rem; }
            .btn-enviar, .btn-modificar, .btn-borrar, .btn-confirmar, .btn-cancelar { font-size: 0.7rem; padding: 6px 10px; }
        }
        @media (max-width: 480px){
            .modal-content{
                margin-top: 65px;
            }
        }
    </style>
</head>
<body>
<!-- CONTENIDO PRINCIPAL -->
<div class="contenido">
    <!-- Encabezados con la información seleccionada -->
    <h1>Carrera: <?php echo htmlspecialchars($nombreCarrera); ?></h1>
    Curso: <?php echo htmlspecialchars($nombreCurso); ?> | 
    Comisión: <?php echo htmlspecialchars($nombreComision); ?> |
    Año: <?php echo htmlspecialchars($anio); ?> |
    Unidad Curricular: <?php echo htmlspecialchars($materiasTexto); ?><br><br>

    <!-- Botón para abrir el modal "Cargar Día" -->
    <button id="abrirModal" class="btn-enviar">Cargar Día</button>

    <!-- Modal para agregar un nuevo registro -->
    <div id="modalForm" class="modal">
        <span id="cerrarModal" class="close">&times;</span>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Agregar Nuevo Día</h2>
            </div>
            <form id="nuevoLibroForm" action="guardar_libro_tema.php" method="post">
                <div>
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" required>
                </div>
                <div>
                    <label for="capacidades">Capacidades</label>
                    <textarea name="capacidades" placeholder="Capacidades"></textarea>
                </div>
                <div>
                    <label for="contenidos">Contenidos</label>
                    <textarea name="contenidos" placeholder="Contenidos" required></textarea>
                </div>
                <div>
                    <label for="evaluacion">Evaluación</label>
                    <textarea name="evaluacion" placeholder="Evaluación"></textarea>
                </div>
                <div>
                    <label for="observacion">Observación Diaria</label>
                    <textarea name="observacion" placeholder="Observación Diaria"></textarea>
                </div>
                <!-- Campos ocultos para pasar información -->
                <div>
                    <input type="hidden" name="profesor" value="<?php echo intval($_SESSION['id'] ?? 0); ?>">
                    <input type="hidden" name="carrera" value="<?php echo intval($carrera); ?>">
                    <input type="hidden" name="curso" value="<?php echo intval($curso); ?>">
                    <input type="hidden" name="comision" value="<?php echo intval($comision); ?>">
                    <input type="hidden" name="anio" value="<?php echo htmlspecialchars($anio); ?>">
                    <input type="hidden" name="materias" value="<?php echo htmlspecialchars($materiasString); ?>">
                    <input type="submit" name="enviar" value="Confirmar" class="btn-enviar">
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla para mostrar los registros existentes -->
    <div class="table-responsive">
        <table id="tablaLibros" class="tabla-elegante">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Capacidades</th>
                    <th>Contenidos</th>
                    <th>Evaluación</th>
                    <th>Observaciones Diarias</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los registros se cargarán dinámicamente vía AJAX -->
            </tbody>
        </table>
    </div>
</div>


<script>
$(document).ready(function() {
    // Mostrar el modal al hacer clic en "Cargar Día"
    $('#abrirModal').on('click', function() {
        $('#modalForm').fadeIn();
    });
    // Cerrar el modal al hacer clic en la "X"
    $('#cerrarModal').on('click', function() {
        $('#modalForm').fadeOut();
    });
    // Cerrar el modal al hacer clic fuera de él
    $(window).on('click', function(event) {
        if (event.target === document.getElementById('modalForm')) {
            $('#modalForm').fadeOut();
        }
    });
    
    // Función para cargar registros existentes desde ajax_libro_tema.php
    function cargarDatos() {
        $.ajax({
            url: 'ajax_libro_tema.php',
            method: 'GET',
            data: {
                carrera: '<?php echo intval($carrera); ?>',
                anio: '<?php echo intval($anio); ?>',
                materias: '<?php echo htmlspecialchars($materiasString); ?>'
            },
            success: function(response) {
                let data;
                try {
                    data = JSON.parse(response);
                } catch (e) {
                    console.error('Error parseando JSON:', e, response);
                    return;
                }
                const tbody = $('#tablaLibros tbody');
                tbody.empty();
                if (data.length > 0) {
                    data.forEach(function(row) {
                        const fechaOriginal = row.fecha;
                        const partesFecha = fechaOriginal.split('-');
                        let fechaFormateada = fechaOriginal;
                        if (partesFecha.length === 3) {
                            fechaFormateada = partesFecha[2] + '/' + partesFecha[1] + '/' + partesFecha[0];
                        }
                        const newRow = $(`
                            <tr>
                                <td class="fecha">${fechaFormateada}</td>
                                <td class="editable" data-campo="capacidades">${row.capacidades}</td>
                                <td class="editable" data-campo="contenidos">${row.contenidos}</td>
                                <td class="editable" data-campo="evaluacion">${row.evaluacion}</td>
                                <td class="editable" data-campo="observacion_diaria">${row.observacion_diaria}</td>
                                <td>
                                    <button class="btn-modificar">Modificar</button>
                                    <button class="btn-borrar">Borrar</button>
                                    <button type="button" class="btn-confirmar" style="display: none;">Confirmar</button>
                                    <button type="button" class="btn-cancelar" style="display: none;">Cancelar</button>
                                    <input type="hidden" class="materia-id" value="${row.idMaterias}">
                                    <input type="hidden" class="carrera-id" value="${row.idCarrera}">
                                    <input type="hidden" class="fecha-original" value="${fechaOriginal}">
                                </td>
                            </tr>
                        `);
                        tbody.append(newRow);
                    });
                }
                // Delegar evento para "Modificar"
                $('#tablaLibros').off('click', '.btn-modificar').on('click', '.btn-modificar', function() {
                    const fila = $(this).closest('tr');
                    fila.find('.editable').each(function() {
                        const contenido = $(this).text();
                        $(this).attr('data-original', contenido);
                       $(this).html('<textarea class="input-editar auto-ajustable">' + contenido + '</textarea>');

                    });
                    fila.find('.btn-modificar, .btn-borrar').hide();
                    fila.find('.btn-confirmar, .btn-cancelar').show();
                });
                // Delegar evento para "Confirmar"
                $('#tablaLibros').off('click', '.btn-confirmar').on('click', '.btn-confirmar', function() {
                    const fila = $(this).closest('tr');
                    const datos = {
                        original_fecha: fila.find('.fecha-original').val(),
                        materia: fila.find('.materia-id').val(),
                        carrera: fila.find('.carrera-id').val(),
                        profesor: '<?php echo intval($_SESSION["id"] ?? 0); ?>'
                    };
                    fila.find('.editable').each(function() {
                        const campo = $(this).data('campo');
                       const contenido = $(this).find('textarea, input').val();

                        datos[campo] = contenido;
                    });
                    $.ajax({
                        url: 'update_libro_tema.php',
                        method: 'POST',
                        data: datos,
                        success: function(resp) {
                            alert('Datos actualizados exitosamente');
                            fila.find('.editable').each(function() {
                                const campo = $(this).data('campo');
                                $(this).text(datos[campo]);
                            });
                            fila.find('.btn-confirmar, .btn-cancelar').hide();
                            fila.find('.btn-modificar, .btn-borrar').show();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al actualizar los datos:', error);
                            alert('Ocurrió un error al actualizar.');
                        }
                    });
                });
                // Delegar evento para "Cancelar"
                $('#tablaLibros').off('click', '.btn-cancelar').on('click', '.btn-cancelar', function() {
                    const fila = $(this).closest('tr');
                    fila.find('.editable').each(function() {
                        const original = $(this).attr('data-original');
                        $(this).text(original);
                    });
                    fila.find('.btn-confirmar, .btn-cancelar').hide();
                    fila.find('.btn-modificar, .btn-borrar').show();
                });
                // Delegar evento para "Borrar"
                $('#tablaLibros').off('click', '.btn-borrar').on('click', '.btn-borrar', function() {
                    const fila = $(this).closest('tr');
                    const fecha = fila.find('.fecha-original').val();
                    const materia = fila.find('.materia-id').val();
                    const carrera = fila.find('.carrera-id').val();
                    if (confirm('¿Está seguro de que desea borrar este registro?')) {
                        $.ajax({
                            url: 'delete_libro_tema.php',
                            method: 'POST',
                            data: { fecha: fecha, materia: materia, carrera: carrera },
                            success: function(resp) {
                                alert(resp);
                                if (resp.includes('exitosamente')) {
                                    fila.remove();
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al borrar el registro:', error);
                                alert('Error al borrar el registro.');
                            }
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
            }
        });
    }
    cargarDatos();
});

    // Función para autoajustar altura
    function ajustarAlturaTextareas() {
        $('textarea.auto-ajustable').each(function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // Ejecutar cuando se modifica una fila
    $(document).on('input', 'textarea.auto-ajustable', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // También ejecutar al insertar los textareas al hacer clic en "Modificar"
    $(document).on('click', '.btn-modificar', function () {
        setTimeout(() => ajustarAlturaTextareas(), 0); // Esperamos a que se renderice
    });
</script>
</body>
</html>
