<?php
include("../conexion.php");

$query = "SELECT 
            entrenamientos.fecha,
            entrenamientos.tiempo_duracion,
            usuarios.nombre AS nombre_usuario,
            ejercicios.nombre AS nombre_ejercicio,
            ejercicios.grupo_muscular,
            detalle_entrenamiento.num_series,
            detalle_entrenamiento.num_repeticiones,
            detalle_entrenamiento.peso,
            detalle_entrenamiento.rpe
          FROM detalle_entrenamiento
          INNER JOIN entrenamientos 
            ON detalle_entrenamiento.id_entrenamiento = entrenamientos.id
          INNER JOIN ejercicios 
            ON detalle_entrenamiento.id_ejercicio = ejercicios.id
          INNER JOIN usuarios 
            ON entrenamientos.id_usuario = usuarios.id
          ORDER BY entrenamientos.fecha DESC";

$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Entrenamientos - GymTrack Lite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Historial de Entrenamientos</h2>
        <a href="../dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
    </div>

    <div class="card shadow">
        <div class="card-body">

            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Ejercicio</th>
                        <th>Grupo muscular</th>
                        <th>Series</th>
                        <th>Repeticiones</th>
                        <th>Peso</th>
                        <th>RPE</th>
                        <th>Duración</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            echo "<tr>";
                            echo "<td>" . $fila['fecha'] . "</td>";
                            echo "<td>" . $fila['nombre_usuario'] . "</td>";
                            echo "<td>" . $fila['nombre_ejercicio'] . "</td>";
                            echo "<td>" . $fila['grupo_muscular'] . "</td>";
                            echo "<td>" . $fila['num_series'] . "</td>";
                            echo "<td>" . $fila['num_repeticiones'] . "</td>";
                            echo "<td>" . $fila['peso'] . " kg</td>";
                            echo "<td>" . $fila['rpe'] . "</td>";
                            echo "<td>" . $fila['tiempo_duracion'] . " min</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr>";
                        echo "<td colspan='9'>No hay entrenamientos registrados.</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>