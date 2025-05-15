<?php include './layout.php'; ?>
<div class="contenido">
    <h2>Generar Planilla de Docentes con Cargo</h2>
    <form action="./generar_pdf.php" method="POST">
        <label for="fecha">Seleccionar fecha:</label>
        <input type="date" name="fecha" required>

        <br><br>
        <button type="submit" class="btn">Generar PDF</button>
    </form>
</div>
