<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - GymTrack Lite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background: linear-gradient(135deg, #0f2027, #203a43, #6a11cb);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card{
            border:none;
            border-radius:18px;
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,0.92);
        }

        .logo{
            width:90px;
            display:block;
            margin:0 auto 15px auto;
        }

        h3{
            font-weight:700;
            color:#333;
        }

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

    <div class="card shadow-lg p-4" style="width: 360px;">

        <img src="imagenes/logo.png" class="logo">

        <h3 class="text-center mb-4">GymTrack Lite</h3>

        <form method="POST" action="validar.php">

            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" placeholder="ejemplo@gmail.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="clave" class="form-control" placeholder="********" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>

        </form>

    </div>

</body>
</html>