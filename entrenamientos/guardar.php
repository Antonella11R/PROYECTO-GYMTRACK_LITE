<?php

// =========================
// CONEXIÓN CORRECTA
// =========================
require_once __DIR__ . "/../conexion.php";

// =========================
// SOLO POST
// =========================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // =========================
    // RECIBIR DATOS
    // =========================
    $id_usuario = $_POST['id_usuario'];
    $id_ejercicio = $_POST['id_ejercicio'];
    $series = $_POST['series'];
    $repeticiones = $_POST['repeticiones'];
    $peso = $_POST['peso'];
    $rpe = $_POST['rpe'];
    $fecha = $_POST['fecha'];

    // =========================
    // VALIDACIÓN BÁSICA
    // =========================
    if (
        !empty($id_usuario) &&
        !empty($id_ejercicio) &&
        !empty($series) &&
        !empty($repeticiones) &&
        !empty($peso)
    ) {

        // =========================
        // INSERT SEGURO (mysqli)
        // =========================
        $sql = "INSERT INTO detalle_entrenamiento 
                (id_usuario, id_ejercicio, series, repeticiones, peso, rpe, fecha)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexion, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "iiiidss",
            $id_usuario,
            $id_ejercicio,
            $series,
            $repeticiones,
            $peso,
            $rpe,
            $fecha
        );

        $resultado = mysqli_stmt_execute($stmt);

        // =========================
        // RESPUESTA
        // =========================
        if ($resultado) {
            echo "<script>
                    alert('Entrenamiento guardado correctamente');
                    window.location.href = 'historial.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al guardar el entrenamiento');
                    window.history.back();
                  </script>";
        }

    } else {
        echo "<script>
                alert('Complete todos los campos obligatorios');
                window.history.back();
              </script>";
    }

} else {
    header("Location: registrar.php");
    exit();
}

?>