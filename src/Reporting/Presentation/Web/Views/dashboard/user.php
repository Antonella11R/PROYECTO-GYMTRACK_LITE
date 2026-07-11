<?php

declare(strict_types=1);

$metrics = $dashboard['metrics'];
$progress = $dashboard['progress'];
$selectedExerciseName = 'Sin datos todavía';
foreach ($progress['exercises'] as $exercise) {
    if ($exercise->id() === $progress['selectedExerciseId']) {
        $selectedExerciseName = $exercise->name();
        break;
    }
}
?>
<div class="mb-4">
    <h1 class="h3 mb-1">Tu dashboard</h1>
    <p class="text-muted mb-0">Resumen personal de actividad y evolución.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Sesiones registradas</div>
            <div class="display-6"><?= $metrics['session_count'] ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Minutos acumulados</div>
            <div class="display-6"><?= $metrics['total_minutes'] ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 d-flex justify-content-center">
            <a href="<?= htmlspecialchars($url('/trainings/create'), ENT_QUOTES) ?>" class="btn btn-dark">Registrar entrenamiento</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Evolución de cargas</h2>
                    <a href="<?= htmlspecialchars($url('/reports/progress'), ENT_QUOTES) ?>" class="btn btn-sm btn-outline-secondary">Ver detalle</a>
                </div>
                <p class="text-muted small">Ejercicio actual: <?= htmlspecialchars($selectedExerciseName, ENT_QUOTES) ?></p>
                <canvas id="dashboardProgressChart" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Sesiones recientes</h2>
                <div class="list-group list-group-flush">
                    <?php foreach ($dashboard['recentSessions'] as $session): ?>
                        <div class="list-group-item px-0">
                            <div class="fw-semibold"><?= htmlspecialchars($session['performed_on'], ENT_QUOTES) ?></div>
                            <div class="small text-muted"><?= htmlspecialchars((string) $session['exercise_count'], ENT_QUOTES) ?> ejercicios · <?= htmlspecialchars((string) $session['duration_minutes'], ENT_QUOTES) ?> min</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$labels = json_encode($progress['chart']['labels'], JSON_THROW_ON_ERROR);
$values = json_encode($progress['chart']['values'], JSON_THROW_ON_ERROR);
$scripts = <<<HTML
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dashboardChartElement = document.getElementById('dashboardProgressChart');
    const dashboardChartLabels = {$labels};
    const dashboardChartValues = {$values};

    if (dashboardChartLabels.length > 0) {
        new Chart(dashboardChartElement, {
            type: 'line',
            data: {
                labels: dashboardChartLabels,
                datasets: [{
                    label: 'Peso levantado (kg)',
                    data: dashboardChartValues,
                    borderColor: '#111827',
                    backgroundColor: 'rgba(17, 24, 39, 0.12)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    }
</script>
HTML;
?>
