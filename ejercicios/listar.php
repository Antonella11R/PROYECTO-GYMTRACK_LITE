<?php
// Incluimos la conexión sube un nivel para buscar el archivo
include("../conexion.php");

// Consulta para obtener los ejercicios
$query = "SELECT * FROM ejercicios";
$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Ejercicios - GymTrack Lite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Ejercicios Registrados</h2>
            <a href="crear.php" class="btn btn-primary">Nuevo Ejercicio</a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Grupo Muscular</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Iteramos sobre los resultados
                        if ($resultado && mysqli_num_rows($resultado) > 0) {
                            while($row = mysqli_fetch_assoc($resultado)) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['nombre'] . "</td>";
                                echo "<td>" . $row['grupo_muscular'] . "</td>";
                                echo "<td>" . $row['descripcion_ejercicio'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No hay ejercicios registrados aún.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="../dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
        </div>
    </div>

</body>
</html>