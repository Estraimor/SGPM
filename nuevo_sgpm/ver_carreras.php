<?php
include './layout.php'
?>
<div class="contenido">
    <div class="contenedor-materias">
        <h2 class="carrera-title">Elija la carrera</h2>
        <input type="text" id="buscadorCarreras" placeholder="Buscar carrera..." onkeyup="filtrarCarreras()">
        <div class="tabla-contenedor">
            <?php
           $rol = $_SESSION['roles']; // Asegurate de tener esta sesión seteada
$idProfesor = $_SESSION['id']; // ID del profesor logueado

if ($rol == 1) {
    // Ver todas las combinaciones (rol administrador)
    $sql = "
    SELECT 
        p.carreras_idCarrera,
        ca.nombre_carrera,
        cu.idCursos,
        cu.curso,
        co.idComisiones,
        co.comision
    FROM preceptores p
    INNER JOIN carreras ca ON p.carreras_idCarrera = ca.idCarrera
    INNER JOIN cursos cu ON p.cursos_idCursos = cu.idCursos
    INNER JOIN comisiones co ON p.comisiones_idComisiones = co.idComisiones
    GROUP BY p.carreras_idCarrera, cu.idCursos, co.idComisiones
    ";
} else {
    // Ver solo las asignadas a su persona (rol 2, 3, etc.)
    $sql = "
    SELECT 
        p.carreras_idCarrera,
        ca.nombre_carrera,
        cu.idCursos,
        cu.curso,
        co.idComisiones,
        co.comision
    FROM preceptores p
    INNER JOIN carreras ca ON p.carreras_idCarrera = ca.idCarrera
    INNER JOIN cursos cu ON p.cursos_idCursos = cu.idCursos
    INNER JOIN comisiones co ON p.comisiones_idComisiones = co.idComisiones
    WHERE p.profesor_idProrfesor = $idProfesor
    GROUP BY p.carreras_idCarrera, cu.idCursos, co.idComisiones
    ";
}

$resultado = mysqli_query($conexion, $sql);
$carreras = [];

while ($row = mysqli_fetch_assoc($resultado)) {
    $carreras[] = $row;
}
?>
            <table id="tablaCarreras" class="tabla-materias">
                <thead>
                 <tr>
                     <th>Carrera</th>
                     <th>Curso</th>
                     <th>Comisión</th>
                     <th>Seleccionar</th>
                 </tr>
                </thead>
                <tbody>
                <?php 
                if (isset($carreras) && is_array($carreras)) {
                    foreach ($carreras as $carrera) { ?>
                        <tr>
                            <td><?= htmlspecialchars($carrera['nombre_carrera']); ?></td>
                            <td><?= htmlspecialchars($carrera['curso']); ?></td>
                            <td><?= htmlspecialchars($carrera['comision']); ?></td>
                            <td>
                <button class="btn-seleccionar"
                        onclick="irAsistenciaCarrera(
                            '<?= $carrera['carreras_idCarrera']; ?>',
                            '<?= $carrera['idCursos']; ?>',
                            '<?= $carrera['idComisiones']; ?>')">
                    <span class="icono">&#10003;</span> Seleccionar
                </button>
                            </td>
                        </tr>
                <?php }
                } else { ?>
                    <tr>
                        <td colspan="4">No hay carreras asignadas.</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filtrarCarreras() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("buscadorCarreras");
    filter = input.value.toUpperCase();
    table = document.getElementById("tablaCarreras");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function irAsistenciaCarrera(idCarrera, idCurso, idComision) {
    window.location.href = "ver_asistencia.php?carrera=" + idCarrera + "&curso=" + idCurso + "&comision=" + idComision;
}
</script>
<style>
	.contenido {
    padding: 20px;
    display: flex;
    justify-content: center;
}

.contenedor-materias {
    background-color: rgba(255, 255, 255, 0.7); /* Fondo blanco medio difuminado */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    width: 100%;
}

.carrera-title {
    color: #d32f2f; /* Rojo */
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
}

#buscadorCarreras {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    box-sizing: border-box;
    border: 1px solid #d32f2f;
    border-radius: 5px;
}

.tabla-contenedor {
    max-height: 450px; /* Limita la altura para overflow */
    overflow-y: auto;
    border: 1px solid #d32f2f;
    border-radius: 5px;
}

.tabla-materias {
    width: 100%;
    border-collapse: collapse;
}

.tabla-materias th, .tabla-materias td {
    border: 1px solid #d32f2f;
    padding: 10px;
    text-align: left;
}

.tabla-materias th {
    background-color: #d32f2f;
    color: white;
}

.tabla-materias tr:nth-child(even) {
    background-color: #f9f9f9;
}

.btn-seleccionar {
    background-color: #d32f2f;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
}

.btn-seleccionar .icono {
    margin-right: 5px;
}

.btn-seleccionar:hover {
    background-color: #b71c1c;
}

</style>


<!--   Core JS Files   -->
<script src="assets/js/core/jquery.3.2.1.min.js"></script>

<script src="assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>


<!-- jQuery Scrollbar -->
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Azzara JS -->
<script src="assets/js/ready.min.js"></script>