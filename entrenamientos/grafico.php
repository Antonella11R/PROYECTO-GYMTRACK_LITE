<?php
include('../conexion.php'); 
session_start();

// Datos de prueba para la visualización inicial según el plan de entregas
$fechas_prueba = ["2026-05-01", "2026-05-04", "2026-05-08", "2026-05-12", "2026-05-14"];
$pesos_prueba = [45.5, 48.0, 48.0, 50.5, 55.0];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTrack - Evolución</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4">
                <h4 class="text-center text-primary mb-4">Evolución de Cargas</h4>
                
                <canvas id="graficoEvolucion"></canvas>

                <div class="text-center mt-4">
                    <a href="../dashboard.php" class="btn btn-primary px-4">Volver al Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('graficoEvolucion').getContext('2d');
    
    // Paso de los datos de prueba definidos en PHP hacia el entorno de JavaScript
    const etiquetas = <?php echo json_encode($fechas_prueba); ?>;
    const datos = <?php echo json_encode($pesos_prueba); ?>;

    new Chart(ctx, {
        type: 'line', 
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Peso Levantado (kg)',
                data: datos,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.3, 
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: false }
            }
        }
    });
</script>

</body>
</html>