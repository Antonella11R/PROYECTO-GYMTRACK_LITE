<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Permite el uso correcto de caracteres especiales -->
    <meta charset="UTF-8">

    <!-- Título que aparece en la pestaña del navegador -->
    <title>Login - GymTrack Lite</title>

    <!-- Hace que el diseño sea responsive en celulares -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN para estilos visuales -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <style>
        /*
        Fondo degradado azul a morado profesional
        */
        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background: linear-gradient(135deg, #0f2027, #203a43, #6a11cb);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /*
        Tarjeta con efecto vidrio (glassmorphism)
        */
        .card{
            border:none;
            border-radius:18px;
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,0.92);
        }

        /* Logo */
        .logo{
            width:90px;
            display:block;
            margin:0 auto 15px auto;
        }

        /* Título */
        h3{
            font-weight:700;
            color:#333;
        }

        /* Botón personalizado */
        .btn-primary{
            background-color:#6a11cb;
            border:none;
        }

        .btn-primary:hover{
            background-color:#4e0fa3;
        }
    </style>
</head>

<body>

    <!-- Tarjeta contenedora del formulario -->
    <div class="card shadow-lg p-4" style="width: 360px;">

        <!-- Logo del sistema -->
        <img src="imagenes/logo.png" class="logo">

        <!-- Título del sistema -->
        <h3 class="text-center mb-4">GymTrack Lite</h3>

        <!-- 
        Formulario que enviará los datos por POST.
        Más adelante se conectará con PHP y la base de datos.
        -->
        <form method="POST" action="">

            <!-- Campo para ingresar el correo -->
            <div class="mb-3">
                <label class="form-label">Correo</label>
                <!-- 
                type="email": valida formato de correo
                name="correo": se usará luego en PHP
                required: obligatorio
                -->
                <input type="email" name="correo" class="form-control" placeholder="ejemplo@gmail.com" required>
            </div>

            <!-- Campo para ingresar la contraseña -->
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <!-- 
                type="password": oculta los caracteres
                name="clave": se usará luego en PHP
                required: obligatorio
                -->
                <input type="password" name="clave" class="form-control" placeholder="********" required>
            </div>

            <!-- Botón para enviar el formulario -->
            <!-- w-100: ocupa todo el ancho -->
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>

        </form>
    </div>

</body>
</html>