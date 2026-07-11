<?php

declare(strict_types=1);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Historial de entrenamientos</h1>
        <p class="text-muted mb-0">Revisa las sesiones registradas y su detalle.</p>
    </div>
    <a href="<?= htmlspecialchars($url('/trainings/create'), ENT_QUOTES) ?>" class="btn btn-dark">Registrar entrenamiento</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= htmlspecialchars($url('/trainings'), ENT_QUOTES) ?>" class="row g-3 align-items-end">
            <?php if (count($athletes) > 1): ?>
                <div class="col-md-4">
                    <label class="form-label">Atleta</label>
                    <select name="athlete_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($athletes as $athlete): ?>
                            <option value="<?= $athlete->id() ?>" <?= (string) $selectedAthleteId === (string) $athlete->id() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($athlete->name(), ENT_QUOTES) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-dark w-100">Filtrar</button>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">La vista muestra las sesiones permitidas para tu perfil actual.</p>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if ($trainings === []): ?>
            <p class="text-muted mb-0">No hay sesiones registradas todavía.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Atleta</th>
                        <th>Registrado por</th>
                        <th>Duración</th>
                        <th>Detalle</th>
                        <th>Notas</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($trainings as $training): ?>
                        <tr>
                            <td><?= htmlspecialchars($training['performed_on'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($training['athlete_name'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($training['recorded_by_name'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars((string) $training['duration_minutes'], ENT_QUOTES) ?> min</td>
                            <td><?= nl2br(htmlspecialchars($training['summary'], ENT_QUOTES)) ?></td>
                            <td><?= htmlspecialchars($training['notes'] ?? 'Sin notas', ENT_QUOTES) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
