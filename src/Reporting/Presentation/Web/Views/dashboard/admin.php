<?php

declare(strict_types=1);

$metrics = $dashboard['metrics'];
?>
<div class="mb-4">
    <h1 class="h3 mb-1">Dashboard administrativo</h1>
    <p class="text-muted mb-0">Resumen global del sistema y actividad reciente.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Administradores</div>
            <div class="display-6"><?= $metrics['users_by_role']['admin'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Entrenadores</div>
            <div class="display-6"><?= $metrics['users_by_role']['trainer'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Usuarios</div>
            <div class="display-6"><?= $metrics['users_by_role']['user'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="text-muted small">Ejercicios activos</div>
            <div class="display-6"><?= $metrics['exercise_count'] ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h5 mb-3">Sesiones recientes</h2>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Atleta</th>
                    <th>Registrado por</th>
                    <th>Ejercicios</th>
                    <th>Duración</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($dashboard['recentSessions'] as $session): ?>
                    <tr>
                        <td><?= htmlspecialchars($session['performed_on'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($session['athlete_name'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($session['recorded_by_name'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars((string) $session['exercise_count'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars((string) $session['duration_minutes'], ENT_QUOTES) ?> min</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
