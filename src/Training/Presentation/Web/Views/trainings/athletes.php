<?php

declare(strict_types=1);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Atletas</h1>
        <p class="text-muted mb-0">Consulta los usuarios que están bajo seguimiento.</p>
    </div>
    <a href="<?= htmlspecialchars($url('/trainings/create'), ENT_QUOTES) ?>" class="btn btn-dark">Registrar sesión</a>
</div>

<div class="card">
    <div class="card-body">
        <?php if ($athletes === []): ?>
            <p class="text-muted mb-0">No hay atletas asignados actualmente.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($athletes as $athlete): ?>
                        <tr>
                            <td><?= htmlspecialchars($athlete->name(), ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($athlete->email()->value(), ENT_QUOTES) ?></td>
                            <td class="text-end">
                                <a href="<?= htmlspecialchars($url('/trainings') . '?athlete_id=' . $athlete->id(), ENT_QUOTES) ?>" class="btn btn-sm btn-outline-primary">Ver historial</a>
                                <a href="<?= htmlspecialchars($url('/reports/progress') . '?athlete_id=' . $athlete->id(), ENT_QUOTES) ?>" class="btn btn-sm btn-outline-secondary">Ver progreso</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
