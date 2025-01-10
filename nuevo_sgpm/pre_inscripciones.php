<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preinscripción Técnicaturas</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/Logo ISPM 2 transparante.png">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url(./assets/img/simulacion-y-automatizacion-de-robotica.jpg);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.9); /* Fondo blanco con transparencia */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            padding-bottom: 15px;
        }

        .logo {
            width: 100px;
            height: 100px;
            background-image: url(./assets/img/Logo%20ISPM%202%20transparante.png);
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0 auto 10px auto; /* Centra el logo y añade espacio abajo */
        }

        .title {
            color: #c0392b; /* Rojo oscuro */
            font-size: 24px;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 10px; /* Espaciado reducido */
            text-align: left;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            gap: 10px; /* Espaciado entre inputs */
            margin-bottom: 15px; /* Margen inferior entre filas */
        }

        .form-group label {
            display: block;
            color: #c0392b; /* Rojo oscuro */
            font-size: 14px;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group input[type="submit"] {
            background-color: #c0392b; /* Rojo oscuro */
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .form-group input[type="submit"]:hover {
            background-color: #a93226; /* Rojo más oscuro */
        }

        .form-group input:focus, .form-group select:focus {
            border-color: #c0392b; /* Rojo oscuro */
            outline: none;
        }

        .form-row .form-group {
            flex: 1; /* Hace que los inputs ocupen el mismo espacio */
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 15px;
                max-width: 100%;
            }

            .title {
                font-size: 20px;
            }

            .subtitle {
                font-size: 14px;
            }

            .form-group input, .form-group select {
                padding: 8px;
                font-size: 12px;
            }

            .form-row {
                flex-direction: column; /* Coloca los inputs uno debajo del otro en pantallas pequeñas */
            }
        }

        .link-special {
            color: #2980b9; /* Color azul */
            cursor: pointer;
            text-decoration: underline;
            margin-left: 5px; /* Margen para separar del texto */
        }
        .link-special {
    color: #c0392b; /* Rojo oscuro */
    cursor: pointer;
    text-decoration: none; /* Sin subrayado */
    margin-left: 5px; /* Margen para separar del texto */
}

.link-special:hover {
    text-decoration: underline; /* Subrayado al pasar el cursor */
}
    </style>
    <script>
        function mostrarAlerta() {
            alert("La Ley de Educación Superior permite a las personas mayores de 25 años que no hayan completado la Escuela Secundaria o el ciclo polimodal, cursar carreras de grado. Para ello, deberán:\n\n1. Acreditar experiencia, idoneidad y/o conocimientos acordes a los estudios que desean realizar.\n2. Cursar el módulo de Introducción a la comprensión y producción de textos, tanto en escritura como en oralidad.\n3. Completar el módulo de Introducción a las matemáticas.\n\n*Atención: Una vez aprobados los diferentes módulos, el/la postulante podrá inscribirse formalmente en la carrera, ciclo lectivo 2025.");
        }
    </script>
</head>
<body>
    <div class="form-container">
        <div class="logo"></div>
        <h1 class="title">¡Transforma tu Futuro con el Instituto Superior Politécnico Misiones N°1!</h1>
        <p class="subtitle">Únete a nuestra comunidad educativa y comienza tu camino hacia el éxito profesional. Inscríbete hoy en nuestras tecnicaturas de vanguardia y conviértete en el profesional que siempre soñaste ser. ¡El 2025 es tu año! <br> Si no terminaste el secundario y tenes más de 25 años<span class="link-special" onclick="mostrarAlerta()">Haz clic aquí!</span></p>
        <form action="insertar_pre_inscripcion.php" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" autocomplete="off" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" autocomplete="off" id="apellido" name="apellido" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="number" autocomplete="off" id="dni" name="dni" required>
                </div>
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input type="number" autocomplete="off" id="celular" name="celular" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" autocomplete="off" id="correo" name="correo" required>
                </div>
            </div>
            <div class="form-group">
                <label for="carrera">Carrera:</label>
                <select id="carrera" name="carrera" required>
                    <option value="" disabled selected>Seleccione una carrera</option>
                    <option value="Comercialización y Marketing">Técnico Superior en Comercialización y Marketing</option>
                    <option value="Automatización y Robótica">Técnico Superior en Automatización y Robótica</option>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" value="Inscribirse">
            </div>
        </form>
    </div>
</body>
</html>
