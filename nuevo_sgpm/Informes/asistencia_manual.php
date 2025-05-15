<?php
include '../layout.php';
session_start();
include '../../conexion/conexion.php';
?>

<style>
    

    .contenido h2 {
        color: #f3545d;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 600;
    }

    form.form-informes {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-dates {
        display: flex;
        gap: 15px;
        justify-content: space-between;
    }

    .form-group,
    .date-field {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    label {
        margin-bottom: 6px;
        font-weight: 500;
        color: #333;
    }

    input[type="date"],
    select {
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    input[type="date"]:focus,
    select:focus {
        outline: none;
        border-color: #f3545d;
        box-shadow: 0 0 5px rgba(243, 84, 93, 0.3);
    }

    .boton-submit-informes {
        background-color: #f3545d;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .boton-submit-informes:hover {
        background-color: #d64150;
    }

    @media (max-width: 600px) {
        .form-dates {
            flex-direction: column;
        }
    }
</style>

<div class="contenido">
    <h2>Generar Planilla de Asistencia Manual</h2>

    <form action="./PDF_asistencia_manual.php" method="post" class="form-informes">
        <div class="form-dates">
            <div class="date-field">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>

            <div class="date-field">
                <label for="fecha_fin">Fecha de fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>
        </div>

        <div class="form-group">
            <label for="carrera">Carrera:</label>
            <select name="carrera" id="carrera" required>
                <option hidden value="">Selecciona una carrera</option>
                <?php
                $sql = "SELECT idCarrera, nombre_carrera FROM carreras";
                $result = mysqli_query($conexion, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['idCarrera']}'>" . htmlspecialchars($row['nombre_carrera']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="curso">Curso:</label>
            <select name="curso" id="curso" required>
                <option hidden value="">Selecciona un curso</option>
                <?php
                $sql = "
                    SELECT DISTINCT cu.idCursos, cu.curso
                    FROM cursos cu
                    INNER JOIN materias m ON m.cursos_idCursos = cu.idCursos
                    INNER JOIN matriculacion_materias mm ON mm.materias_idMaterias = m.idMaterias
                ";
                $result = mysqli_query($conexion, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['idCursos']}'>Curso {$row['curso']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="comision">Comisión:</label>
            <select name="comision" id="comision" required>
                <option hidden value="">Selecciona una comisión</option>
                <?php
                $sql = "
                    SELECT DISTINCT co.idComisiones, co.comision
                    FROM comisiones co
                    INNER JOIN materias m ON m.comisiones_idComisiones = co.idComisiones
                    INNER JOIN matriculacion_materias mm ON mm.materias_idMaterias = m.idMaterias
                ";
                $result = mysqli_query($conexion, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['idComisiones']}'>Comisión {$row['comision']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <input type="submit" value="Generar Planilla PDF" class="boton-submit-informes">
        </div>
    </form>
</div>
