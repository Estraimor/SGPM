<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./stilos-login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="icon" href="../politecnico.ico">
    <title>login</title>
    
</head>
<body>
    
    <form action="" method="post">
        
    <section>
        <div class="form-box">
            <div class="form-value">
                <form action="" autocomplete="off">
                    <h2>Inicia sesión para crear cuenta</h2>
                    <?php include'./controlar-login-register.php';?>
                    <div class="inputbox">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="text" required name="usuario" id="usuario" autocomplete="off">     
                         <label for="usuario"> Usuario </label>
                    </div>
                    <div class="inputbox">
                    <ion-icon class="eye" name="eye-off-outline" onclick="togglePassword()"></ion-icon>
                        <input type="password" id="password" required name="password" >
                        <label for="password">Contraseña</label>
                    </div>
                    
                    <button type="submit" name="enviar" class="submit-button">Crear Cuenta</button>
                    
                </form>
               
            </div>
        </div>
    </section>            
    </form>

    <script>
       function togglePassword() {
    var passwordInput = document.getElementById("password");
    var eyeIcon = document.querySelector(".eye");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.name = "eye-outline"; // Cambia el icono a ojo abierto
    } else {
        passwordInput.type = "password";
        eyeIcon.name = "eye-off-outline"; // Cambia el icono a ojo cerrado
    }
}
    </script>


<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>