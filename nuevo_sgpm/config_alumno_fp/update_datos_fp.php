<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../conexion/conexion.php';
session_start();

// ===================== VALIDACIÓN DE LEGAJO =====================
$legajo_fp = intval($_POST['legajo_fp'] ?? 0);
if ($legajo_fp <= 0) {
    echo "Legajo inválido";
    exit;
}

// ===================== ACTUALIZACIÓN DE alumnos_fp =====================
$camposUpdate = [];
$valores = [];

// Carreras_idCarrera (0 a 3)
for ($i = 0; $i <= 3; $i++) {
    $campo = 'carreras_idCarrera' . ($i == 0 ? '' : $i);
    $camposUpdate[] = "$campo = ?";
    $valores[] = (isset($_POST[$campo]) && is_numeric($_POST[$campo]) && $_POST[$campo] > 0) ? intval($_POST[$campo]) : NULL;
}

// Requisitos (checkboxes)
$requisitos = ['original_titulo', 'fotos', 'folio', 'fotocopia_dni', 'fotocopia_partida_nacimiento', 'constancia_cuil', 'Pago'];
foreach ($requisitos as $campo) {
    $camposUpdate[] = "$campo = ?";
    $valores[] = isset($_POST[$campo]) ? intval($_POST[$campo]) : 0;
}

$setString = implode(", ", $camposUpdate);
$sql = "UPDATE alumnos_fp SET $setString WHERE legajo_afp = ?";
$valores[] = $legajo_fp;

$stmt = $conexion->prepare($sql);
$tipos = str_repeat("i", count($valores));
$stmt->bind_param($tipos, ...$valores);
$stmt->execute();

// ===================== INSERCIÓN / ACTUALIZACIÓN EN inscripcion_fp =====================
if (!empty($_POST['carreras_idCarrera'])) {
    foreach ($_POST['carreras_idCarrera'] as $i => $idCarrera) {
        $idInscripcion = $_POST['idincripcion_fp'][$i];
        $estado = intval($_POST['estado'][$i]);
        $fechaInscripcion = $_POST['fecha_inscripcion'][$i] ?: null;
        $fechaFinalizacion = $_POST['fecha_finalizacion'][$i] ?: null;
        $corte = is_numeric($_POST['corte'][$i]) ? intval($_POST['corte'][$i]) : null;

        if ($idInscripcion === "new") {
            // 🔄 INSERT
            $stmtInsert = $conexion->prepare("
                INSERT INTO inscripcion_fp (
                    alumnos_fp_idalumnos_fp, alumnos_fp_legajo_afp,
                    carreras_idCarrera, estado, fecha_inscripcion, fecha_finalizacion, corte
                ) VALUES (
                    (SELECT idalumnos_fp FROM alumnos_fp WHERE legajo_afp = ?),
                    ?, ?, ?, ?, ?, ?
                )
            ");
            $stmtInsert->bind_param(
                "iiisssi",
                $legajo_fp, $legajo_fp,
                $idCarrera, $estado, $fechaInscripcion, $fechaFinalizacion, $corte
            );
            $stmtInsert->execute();
        } else {
            // ✏️ UPDATE
            $stmtUpdate = $conexion->prepare("
                UPDATE inscripcion_fp
                SET carreras_idCarrera = ?, estado = ?, fecha_inscripcion = ?, fecha_finalizacion = ?, corte = ?
                WHERE idincripcion_fp = ? AND alumnos_fp_legajo_afp = ?
            ");
            $stmtUpdate->bind_param(
                "iissiii",
                $idCarrera, $estado, $fechaInscripcion, $fechaFinalizacion, $corte,
                $idInscripcion, $legajo_fp
            );
            $stmtUpdate->execute();
        }
    }
}

// ===================== ELIMINACIÓN DE REGISTROS DE inscripcion_fp =====================
if (!empty($_POST['eliminar_inscripcion_fp'])) {
    foreach ($_POST['eliminar_inscripcion_fp'] as $idBorrar) {
        $id = intval($idBorrar);
        $stmtDel = $conexion->prepare("DELETE FROM inscripcion_fp WHERE idincripcion_fp = ? AND alumnos_fp_legajo_afp = ?");
        $stmtDel->bind_param("ii", $id, $legajo_fp);
        $stmtDel->execute();
    }
}

// ===================== REDIRECCIÓN CON MENSAJE =====================
$urlRedireccion = "../ver_detalles_fp.php?legajo=$legajo_fp";

if ($stmt->affected_rows > 0) {
    echo "<meta http-equiv='refresh' content='1;url=$urlRedireccion'>";
    echo "
    <div style='
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 12px;
        border-radius: 5px;
        margin: 20px;
        font-family: sans-serif;
        font-weight: bold;
        text-align: center;
    '>
        ✅ Datos actualizados correctamente. Redirigiendo...
    </div>
    ";
} elseif ($stmt->affected_rows === 0) {
    echo "<meta http-equiv='refresh' content='1;url=$urlRedireccion'>";
    echo "
    <div style='
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
        padding: 12px;
        border-radius: 5px;
        margin: 20px;
        font-family: sans-serif;
        font-weight: bold;
        text-align: center;
    '>
        ⚠️ No hiciste ningún cambio. Todo quedó igual. Redirigiendo...
    </div>
    ";
} else {
    echo "
    <div style='
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        padding: 12px;
        border-radius: 5px;
        margin: 20px;
        font-family: sans-serif;
        font-weight: bold;
        text-align: center;
    '>
        ❌ Error al actualizar los datos.
    </div>
    ";
}
