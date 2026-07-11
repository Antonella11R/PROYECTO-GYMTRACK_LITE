<?php

declare(strict_types=1);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Ejercicios</h1>
        <p class="text-muted mb-0">Gestiona el catálogo base del gimnasio.</p>
    </div>
    <a href="<?= htmlspecialchars($url('/admin/exercises/create'), ENT_QUOTES) ?>" class="btn btn-dark">Nuevo ejercicio</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Grupo muscular</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($exercises as $exercise): ?>
                    <tr>
                        <td><?= htmlspecialchars($exercise->name(), ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($exercise->muscleGroup(), ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($exercise->description() ?? 'Sin descripción', ENT_QUOTES) ?></td>
                        <td>
                            <span class="badge text-bg-<?= $exercise->isActive() ? 'success' : 'secondary' ?>">
                                <?= $exercise->isActive() ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="<?= htmlspecialchars($url('/admin/exercises/' . $exercise->id() . '/edit'), ENT_QUOTES) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <?php if ($exercise->isActive()): ?>
                                    <form method="POST" action="<?= htmlspecialchars($url('/admin/exercises/' . $exercise->id() . '/deactivate'), ENT_QUOTES) ?>">
                                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken('deactivate_exercise'), ENT_QUOTES) ?>">
                                        <button class="btn btn-sm btn-outline-danger">Desactivar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
