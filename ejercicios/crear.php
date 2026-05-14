<?php
// 1. Incluimos la conexión subiendo un nivel 
include("../conexion.php");

// 2. Lógica para guardar los datos cuando se presiona el botón
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturamos los datos del formulario [cite: 28]
    $nombre = $_POST['nombre'];
    $grupo_muscular = $_POST['grupo_muscular'];
    $descripcion = $_POST['descripcion_ejercicio'];

    // Consulta para insertar en la tabla de Melanie [cite: 11, 29]
    $query = "INSERT INTO ejercicios (nombre, grupo_muscular, descripcion_ejercicio) 
              VALUES ('$nombre', '$grupo_muscular', '$descripcion')";

    if (mysqli_query($conexion, $query)) {
        // Si se guarda con éxito, nos manda a la lista de Ignacio [cite: 30]
        header("Location: listar.php?msj=success");
    } else {
        echo "<div class='alert alert-danger'>Error al guardar: " . mysqli_error($conexion) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Ejercicio - GymTrack Lite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Nuevo Ejercicio</h2>
                    <a href="listar.php" class="btn btn-outline-secondary btn-sm">Ver Lista</a>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Nombre del Ejercicio</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Press de Banca" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Grupo Muscular</label>
                                <select name="grupo_muscular" class="form-select" required>
                                    <option value="">Selecciona uno...</option>
                                    <option value="Pecho">Pecho</option>
                                    <option value="Espalda">Espalda</option>
                                    <option value="Piernas">Piernas</option>
                                    <option value="Hombros">Hombros</option>
                                    <option value="Brazos">Brazos</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion_ejercicio" class="form-control" rows="3" placeholder="Breve explicación del movimiento..."></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Guardar Ejercicio</button>
                                <a href="../dashboard.php" class="btn btn-light">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>