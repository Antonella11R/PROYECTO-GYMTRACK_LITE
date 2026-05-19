<?php

declare(strict_types=1);

$metrics = $dashboard['metrics'];
?>
<div class="mb-4">
    <h1 class="h3 mb-1">Dashboard del entrenador</h1>
    <p class="text-muted mb-0">Resumen de atletas asignados y sesiones recientes.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Atletas asignados</div>
            <div class="display-6"><?= $metrics['athlete_count'] ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Sesiones registradas</div>
            <div class="display-6"><?= $metrics['session_count'] ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 d-flex justify-content-center">
            <a href="<?= htmlspecialchars($url('/trainings/create'), ENT_QUOTES) ?>" class="btn btn-dark">Registrar nueva sesión</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Atletas</h2>
                <div class="list-group list-group-flush">
                    <?php foreach ($dashboard['athletes'] as $athlete): ?>
                        <a href="<?= htmlspecialchars($url('/reports/progress') . '?athlete_id=' . $athlete->id(), ENT_QUOTES) ?>" class="list-group-item list-group-item-action d-flex justify-content-between">
                            <span><?= htmlspecialchars($athlete->name(), ENT_QUOTES) ?></span>
                            <span class="text-muted"><?= htmlspecialchars($athlete->email()->value(), ENT_QUOTES) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Sesiones recientes</h2>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Atleta</th>
                            <th>Ejercicios</th>
                            <th>Duración</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($dashboard['recentSessions'] as $session): ?>
                            <tr>
                                <td><?= htmlspecialchars($session['performed_on'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($session['athlete_name'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars((string) $session['exercise_count'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars((string) $session['duration_minutes'], ENT_QUOTES) ?> min</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
