<?php
// Datos de conexión
$host = "localhost";
$usuario = "root";
$contrasena = "";
$basedatos = "gymtrack_lite"; // ← cambia si tu BD tiene otro nombre

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer codificación UTF-8
$conexion->set_charset("utf8");
?>