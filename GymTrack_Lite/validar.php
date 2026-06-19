<?php
include("Conexion.php");
session_start();

// Verificar que el formulario se envió
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Datos del formulario
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    // Consulta CORRECTA (tu columna es "password")
    $sql = "SELECT * FROM usuarios 
            WHERE correo='$correo' AND password='$clave'";

    $resultado = $conexion->query($sql);

    // Validación
    if ($resultado->num_rows > 0) {

        // Guardar sesión (opcional pero recomendado)
        $_SESSION['usuario'] = $correo;

        // Redirigir al sistema
        header("Location: dashboard.php");
        exit();

    } else {
        // Error de login
        echo "<script>
                alert('Correo o contraseña incorrectos');
                window.location.href='login.php';
              </script>";
    }

} else {
    // Si intentan entrar sin enviar formulario
    header("Location: login.php");
    exit();
}
?>