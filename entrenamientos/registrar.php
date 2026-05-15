<?php
session_start();

/* =========================
   CONEXIÓN
========================= */
$host = "localhost";
$dbname = "gymtrack_lite";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$user_id = $_SESSION['user_id'];

$mensaje = "";
$tipo = "";

/* =========================
   EJERCICIOS
========================= */
$stmt = $pdo->prepare("
    SELECT id, nombre, grupo_muscular, descripcion_ejercicio 
    FROM ejercicios 
    ORDER BY nombre ASC
");
$stmt->execute();
$ejercicios = $stmt->fetchAll();

/* =========================
   GUARDAR
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ejercicio_id = $_POST['ejercicio_id'];
    $fecha = $_POST['fecha'];
    $series = $_POST['series'];
    $repeticiones = $_POST['repeticiones'];
    $peso = $_POST['peso'];
    $rpe = $_POST['rpe'];
    $notas = $_POST['notas'];

    try {
        $sql = "INSERT INTO sesiones_entrenamiento 
                (user_id, ejercicio_id, fecha, series, repeticiones, peso, rpe, notas)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $user_id, $ejercicio_id, $fecha,
            $series, $repeticiones, $peso,
            $rpe, $notas
        ]);

        $mensaje = "Entrenamiento registrado correctamente";
        $tipo = "success";

    } catch (Exception $e) {
        $mensaje = "Error al guardar el entrenamiento";
        $tipo = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Entrenamiento</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* 🟣 Labels morados */
        label{
            color:#6a11cb;
            font-weight:600;
        }

        /* 🟣 Botón degradado morado */
        .btn-morado{
            background: linear-gradient(135deg, #6a11cb, #4e0fa3);
            color:white;
            border:none;
        }

        .btn-morado:hover{
            background: linear-gradient(135deg, #4e0fa3, #6a11cb);
            color:white;
        }

        body{
            background:#f5f5f5;
        }
    </style>
</head>

<body>

<div class="container mt-5">

    <h2 class="mb-4" style="color:#6a11cb;">Registrar Entrenamiento</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= $tipo ?>">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <div class="card p-4">

        <form method="POST">

            <!-- EJERCICIO -->
            <div class="mb-3">
                <label>Ejercicio</label>
                <select name="ejercicio_id" class="form-control" required>
                    <option value="">Seleccione ejercicio</option>
                    <?php foreach ($ejercicios as $e): ?>
                        <option value="<?= $e['id'] ?>">
                            <?= $e['nombre'] ?> - <?= $e['grupo_muscular'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- FECHA -->
            <div class="mb-3">
                <label>Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>

            <!-- SERIES -->
            <div class="mb-3">
                <label>Series</label>
                <input type="number" name="series" class="form-control" value="3">
            </div>

            <!-- REPETICIONES -->
            <div class="mb-3">
                <label>Repeticiones</label>
                <input type="number" name="repeticiones" class="form-control" value="10">
            </div>

            <!-- PESO -->
            <div class="mb-3">
                <label>Peso (kg)</label>
                <input type="number" step="0.5" name="peso" class="form-control" required>
            </div>

            <!-- RPE -->
            <div class="mb-3">
                <label>RPE</label>
                <select name="rpe" class="form-control">
                    <option value="">Sin RPE</option>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- NOTAS -->
            <div class="mb-3">
                <label>Notas</label>
                <textarea name="notas" class="form-control"></textarea>
            </div>

            <!-- BOTÓN -->
            <button class="btn btn-morado w-100">
                Guardar Entrenamiento
            </button>

        </form>

    </div>
</div>

</body>
</html>