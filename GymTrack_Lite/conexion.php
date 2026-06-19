<?php

$host = "localhost";
$usuario = "root";
$contrasena = "";
$basedatos = "gymtrack_lite";

$conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos");
}

$conexion->set_charset("utf8");

?>