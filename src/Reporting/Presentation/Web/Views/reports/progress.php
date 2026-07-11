<?php

declare(strict_types=1);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Evolución de cargas</h1>
        <p class="text-muted mb-0">Visualiza la progresión de peso por ejercicio.</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= htmlspecialchars($url('/reports/progress'), ENT_QUOTES) ?>" class="row g-3 align-items-end">
            <?php if ($viewer->role()->value !== 'user'): ?>
                <div class="col-md-4">
                    <label class="form-label">Atleta</label>
                    <select name="athlete_id" class="form-select">
                        <?php foreach ($report['athletes'] as $athlete): ?>
                            <option value="<?= $athlete->id() ?>" <?= (string) $report['selectedAthleteId'] === (string) $athlete->id() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($athlete->name(), ENT_QUOTES) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-md-4">
                <label class="form-label">Ejercicio</label>
                <select name="exercise_id" class="form-select">
                    <?php foreach ($report['exercises'] as $exercise): ?>
                        <option value="<?= $exercise->id() ?>" <?= (string) $report['selectedExerciseId'] === (string) $exercise->id() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($exercise->name() . ' · ' . $exercise->muscleGroup(), ENT_QUOTES) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-dark w-100">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if ($report['chart']['labels'] === []): ?>
            <p class="text-muted mb-0">Todavía no hay datos para el filtro seleccionado.</p>
        <?php else: ?>
            <canvas id="progressChart" height="110"></canvas>
        <?php endif; ?>
    </div>
</div>

<?php
$labels = json_encode($report['chart']['labels'], JSON_THROW_ON_ERROR);
$values = json_encode($report['chart']['values'], JSON_THROW_ON_ERROR);
$scripts = <<<HTML
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = {$labels};
    const values = {$values};
    const chartElement = document.getElementById('progressChart');

    if (chartElement && labels.length > 0) {
        new Chart(chartElement, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Peso levantado (kg)',
                    data: values,
                    borderColor: '#0f172a',
                    backgroundColor: 'rgba(59, 130, 246, 0.14)',
                    fill: true,
                    tension: 0.25
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
