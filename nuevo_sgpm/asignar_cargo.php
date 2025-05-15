<?php
include './layout.php';

$profesores = $conexion->query("SELECT idProrfesor, nombre_profe, apellido_profe FROM profesor ORDER BY apellido_profe ASC");
?>

<style>
    .panel-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        padding: 10px;
    }

    .panel {
        flex: 1;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .panel h3 {
        color: #f3545d;
        margin-bottom: 15px;
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .btn {
        background-color: #f3545d;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #d43c48;
    }

    select, input {
        width: 100%;
        padding: 8px;
        margin-top: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .acciones {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 10px;
    }
</style>

<div class="contenido">
    <div class="panel-container">

        <!-- Panel izquierdo: Lista de docentes -->
        <div class="panel">
            <h3>Docentes</h3>
            <table id="tabla-docentes">
                <thead>
                    <tr>
                        <th>Apellido</th>
                        <th>Nombre</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($doc = $profesores->fetch_assoc()): ?>
                        <tr>
                            <td><?= $doc['apellido_profe'] ?></td>
                            <td><?= $doc['nombre_profe'] ?></td>
                            <td>
                                <button class="btn seleccionar"
                                    data-id="<?= $doc['idProrfesor'] ?>"
                                    data-nombre="<?= $doc['apellido_profe'] ?>, <?= $doc['nombre_profe'] ?>">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Panel central: Cargo actual -->
        <div class="panel" id="panel-central">
            <h3>Cargo actual</h3>
            <p style="text-align:center;"><strong>Docente:</strong> <span id="docente-seleccionado">Ninguno</span></p>
            <div id="cargo-info">
                <p><strong>Cargo:</strong> <span id="cargo-nombre">Ninguno</span></p>
                <p><strong>Turno:</strong>
                    <select id="turno-actual" disabled>
                        <option value="Mañana">Mañana</option>
                        <option value="Tarde">Tarde</option>
                        <option value="Noche">Noche</option>
                    </select>
                </p>
                <div class="acciones">
                    <button id="btn-editar-turno" class="btn" style="display:none;">Editar Turno</button>
                    <button id="btn-eliminar-cargo" class="btn" style="display:none;">Quitar Cargo</button>
                </div>
            </div>
        </div>

        <!-- Panel derecho: Asignar nuevo cargo -->
        <div class="panel">
            <h3>Asignar Cargo</h3>
            <select id="nuevo-cargo">
                <option value="">-- Seleccionar cargo --</option>
                <option value="MEP">MEP</option>
                <option value="Asistente Técnico">Asistente Técnico</option>
                <option value="Rectoria">Rectoria</option>
                 <option value="Secretaria">Secretaria</option>
                <option value="Administración">Administración</option>
                <option value="Preceptora">Preceptora</option>
                <option value="Preceptor">Preceptor</option>
                <option value="Bedel">Bedel</option>
                <option value="Departamento de Programación">Departamento de Programación</option>
                <option value="Personal de Servicio">Personal de Servicio</option>
            </select>

            <label for="turno-nuevo">Turno:</label>
            <select id="turno-nuevo">
                <option value="Mañana">Mañana</option>
                <option value="Tarde">Tarde</option>
                <option value="Noche">Noche</option>
            </select>

            <button id="btn-asignar-cargo" class="btn" style="margin-top:10px;">Asignar Cargo</button>
        </div>
    </div>
</div>

<script>
    let idSeleccionado = null;
    let nombreSeleccionado = '';

    const cargoNombre = document.getElementById('cargo-nombre');
    const turnoActual = document.getElementById('turno-actual');
    const btnEditar = document.getElementById('btn-editar-turno');
    const btnEliminar = document.getElementById('btn-eliminar-cargo');
    const docenteSpan = document.getElementById('docente-seleccionado');

    // Delegación de eventos para botones "Seleccionar"
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('seleccionar')) {
            nombreSeleccionado = e.target.getAttribute('data-nombre');
            idSeleccionado = e.target.getAttribute('data-id');
            docenteSpan.textContent = nombreSeleccionado;
            fetchCargo();
        }
    });

    function fetchCargo() {
        fetch('ajax/obtener_cargo_actual.php', {
            method: 'POST',
            body: new URLSearchParams({ id: idSeleccionado })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                cargoNombre.textContent = data.cargo;
                turnoActual.value = data.turno;
                turnoActual.disabled = false;
                btnEditar.style.display = 'inline-block';
                btnEliminar.style.display = 'inline-block';
            } else {
                cargoNombre.textContent = 'Ninguno';
                turnoActual.disabled = true;
                btnEditar.style.display = 'none';
                btnEliminar.style.display = 'none';
            }
        });
    }

    btnEditar.addEventListener('click', () => {
        fetch('./ajax/editar_turno.php', {
            method: 'POST',
            body: new URLSearchParams({
                id: idSeleccionado,
                turno: turnoActual.value
            })
        })
        .then(res => res.json())
        .then(data => {
            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? 'Turno actualizado' : 'Error',
                text: data.success ? `El turno fue actualizado para ${nombreSeleccionado}` : data.msg
            });
        });
    });

    btnEliminar.addEventListener('click', () => {
        Swal.fire({
            title: `¿Quitar cargo de ${nombreSeleccionado}?`,
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f3545d',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Sí, quitar'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('ajax/eliminar_cargo.php', {
                    method: 'POST',
                    body: new URLSearchParams({ id: idSeleccionado })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        cargoNombre.textContent = 'Ninguno';
                        turnoActual.disabled = true;
                        btnEditar.style.display = 'none';
                        btnEliminar.style.display = 'none';
                        Swal.fire('Eliminado', `El cargo de ${nombreSeleccionado} fue quitado.`, 'success');
                    } else {
                        Swal.fire('Error', data.msg, 'error');
                    }
                });
            }
        });
    });

    document.getElementById('btn-asignar-cargo').addEventListener('click', () => {
        const cargo = document.getElementById('nuevo-cargo').value;
        const turno = document.getElementById('turno-nuevo').value;

        if (!idSeleccionado || !cargo) {
            Swal.fire('Campos incompletos', 'Seleccioná un profesor y un cargo.', 'warning');
            return;
        }

        fetch('./ajax/asignar_cargo.php', {
            method: 'POST',
            body: new URLSearchParams({
                id: idSeleccionado,
                cargo: cargo,
                turno: turno
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchCargo();
                Swal.fire('Asignado', `Cargo asignado a ${nombreSeleccionado}.`, 'success');
            } else {
                Swal.fire('Error', data.msg, 'error');
            }
        });
    });

    // Activar DataTable
    new DataTable("#tabla-docentes", {
        perPage: 7,
        labels: {
            placeholder: "Buscar...",
            perPage: "{select} docentes por página",
            noRows: "No se encontraron docentes",
            info: "Mostrando {start} a {end} de {rows} docentes"
        }
    });
</script>
