<?php
// Configuración de la conexión a la base de datos
include '../conexion/conexion.php';

// Obtener los datos del formulario y sanitizar la entrada
$nombre = htmlspecialchars(trim($_POST['nombre']));
$apellido = htmlspecialchars(trim($_POST['apellido']));
$dni = htmlspecialchars(trim($_POST['dni']));
$fecha_nacimiento = htmlspecialchars(trim($_POST['fecha_nacimiento']));
$celular = $_POST['celular'];
$correo = htmlspecialchars(trim($_POST['correo']));
$carrera = htmlspecialchars(trim($_POST['carrera']));

// Comprobar si el DNI ya existe en la base de datos
$sql_check = $conexion->prepare("SELECT idpre_inscripciones FROM pre_inscripciones WHERE DNI = ?");
$sql_check->bind_param("s", $dni);
$sql_check->execute();
$sql_check->store_result();

if ($sql_check->num_rows > 0) {
    // Si el DNI ya existe, mostrar un mensaje de alerta
    echo "<script>
        alert('El DNI ingresado ya está registrado en la base de datos. Por favor, verifica tu información o contacta a la institución para más detalles.');
        window.location.href = 'pre_inscripciones.php'; // Redirigir a la página de inscripción
    </script>";
} else {
    // Si el DNI no existe, proceder con la inserción
    $sql = $conexion->prepare("INSERT INTO pre_inscripciones (nombre, apellido, dni, fecha_nacimiento, celular, correo, carrera) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("sssssss", $nombre, $apellido, $dni, $fecha_nacimiento, $celular, $correo, $carrera);

    if ($sql->execute()) {
        // Mensaje de confirmación
echo "<script>
    alert('Preinscripción Confirmada\\n\\n $nombre $apellido,\\n\\n Has completado correctamente tu preinscripción a la Tecnicatura Superior $carrera.\\n\\n La preinscripción online estará disponible hasta el 15/11/2024\\n\\n Recuerda que la misma no asegura tu cupo ni tiene validez hasta que presentes los requisitos de forma física en la institución Bº Itaembé Guazú, Los Cardenales 6992, Posadas, Misiones.\\n\\n La presentación de requisitos se realizará a partir del 28/10/2024, de lunes a viernes de 19:30 a 22:30 hs.\\n\\n La fecha límite para acreditar tu inscripción y presentar toda la documentación requerida es el 15/11/2024. \\n\\nRequisitos:\\n- Original y copia del Título Secundario\\n- 2 fotos 4x4\\n- Folio A4\\n- Fotocopia del DNI\\n- Fotocopia de la Partida de Nacimiento\\n- \$15.000 Ayuda económica voluntaria para gastos de limpieza (lavandina, cera, trapos de piso, etc.) y gastos administrativos (hojas, carpetas, tóner, etc.).\\n\\nIMPORTANTE: ¡¡SI NO ENCUENTRAS EL MENSAJE, VERIFICA TU CARPETA DE SPAM  NO DESEADO!!.');
    window.location.href = 'pre_inscripciones.php';
</script>";



        // Enviar un correo electrónico al usuario con estilo HTML
        $asunto = "Confirmación de Preinscripción - $carrera";
        $mensaje = "
        <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                color: #333;
            }
            .container {
                width: 80%;
                margin: 0 auto;
                background-color: #ffffff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 0px 10px 0px #cccccc;
            }
            .header {
                background-color: #ff0000; /* Rojo */
                color: #ffffff; /* Blanco */
                padding: 10px;
                border-radius: 10px 10px 0 0;
                text-align: center;
            }
            .header h1 {
                margin: 0;
            }
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .logo img {
                max-width: 150px;
            }
            .content {
                padding: 20px;
                color: #000000; /* Color negro para el contenido */
            }
            .content p {
                font-size: 16px;
                line-height: 1.5;
            }
            .content ul {
                list-style-type: none;
                padding: 0;
            }
            .content ul li {
                background-color: #007bff;
                color: #ffffff;
                padding: 10px;
                margin-bottom: 10px;
                border-radius: 5px;
                text-align: center;
            }
            .qr-section {
                text-align: center;
                margin: 20px 0;
            }
            .qr-section img {
                max-width: 200px;
                border: 2px solid #000000; /* Borde negro */
                border-radius: 10px;
            }
            .footer {
                background-color: #000000; /* Negro */
                color: #ffffff; /* Blanco */
                text-align: center;
                padding: 10px;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='logo'>
                <img src='https://sgpm.politecnicomisiones.edu.ar/imagenes/politecnico.png' alt='Logo'>              
            </div>
            <div class='header'>
                <h1>Preinscripción Confirmada</h1>
            </div>
            <div class='content'>
                <p><strong>$nombre $apellido</strong>,</p>
                <p>Has completado correctamente tu preinscripción a la <strong>Tecnicatura Superior $carrera</strong>.</p>
                <p>Recuerda que la misma no asegura tu cupo ni tiene validez hasta que presentes los requisitos de forma física en la institución <strong>Bº Itaembé Guazú, Los Cardenales 6992, Posadas, Misiones</strong>.</p>
                <p>La presentación de requisitos se realizará a partir del 28/10/2024, de lunes a viernes de 19:30 a 22:30 hs.</p>
                <p>La fecha límite para acreditar tu inscripción y presentar toda la documentación requerida es el 15/11/2024.</strong></p>
                <p><b>Requisitos:</b></p>
                <ul>
                    <li>Original y copia del Título Secundario</li>
                    <li>2 fotos 4x4</li>
                    <li>Folio A4</li>
                    <li>Fotocopia del DNI</li>
                    <li>Fotocopia de la Partida de Nacimiento</li> 
                    <li>$15.000 Ayuda económica voluntaria para gastos de limpieza (lavandina, cera, trapos de piso, etc.) y gastos administrativos (hojas, carpetas, tóner, etc.).</li>  
                </ul>
                <!-- Sección del código QR -->
                <div class='qr-section'>
                    <h2>Únete al grupo de WhatsApp</h2>
                    <img src='https://sgpm.politecnicomisiones.edu.ar/imagenes/QR_preincriptos.jpg' alt='Código QR de WhatsApp'>
                    <p>Escanea el código QR para unirte al grupo de preinscriptos 2025 del ISPM N° 1.</p>
                </div>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Instituto Superior Politécnico Misiones Nº 1. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
</html>";

        // Para enviar el correo en formato HTML con una imagen embebida (logo)
        $cabeceras = "MIME-Version: 1.0" . "\r\n";
        $cabeceras .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $cabeceras .= "From: administracion@politecnicomisiones.edu.ar\r\n";

        // Adjuntar la imagen del logo
        $cabeceras .= "Content-ID: <logo>\r\n";

        // Enviar el correo
        mail($correo, $asunto, $mensaje, $cabeceras);
    } else {
        echo "Error: " . $sql->error;
    }

    // Cerrar la consulta de inserción
    $sql->close();
}

// Cerrar la consulta de verificación y la conexión
$sql_check->close();
$conexion->close();
?>
