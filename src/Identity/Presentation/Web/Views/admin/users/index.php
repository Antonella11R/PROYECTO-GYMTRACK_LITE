<?php

declare(strict_types=1);

$usersById = [];
foreach ($users as $listedUser) {
    $usersById[$listedUser->id()] = $listedUser;
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Usuarios</h1>
        <p class="text-muted mb-0">Administra cuentas, roles y asignaciones.</p>
    </div>
    <a href="<?= htmlspecialchars($url('/admin/users/create'), ENT_QUOTES) ?>" class="btn btn-dark">Nuevo usuario</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Entrenador</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $listedUser): ?>
                    <tr>
                        <td><?= htmlspecialchars($listedUser->name(), ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($listedUser->email()->value(), ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($listedUser->role()->label(), ENT_QUOTES) ?></td>
                        <td>
                            <?php
                            $trainer = $listedUser->trainerId() !== null ? ($usersById[$listedUser->trainerId()] ?? null) : null;
                            echo $trainer ? htmlspecialchars($trainer->name(), ENT_QUOTES) : '<span class="text-muted">Sin asignar</span>';
                            ?>
                        </td>
                        <td>
                            <span class="badge text-bg-<?= $listedUser->isActive() ? 'success' : 'secondary' ?>">
                                <?= $listedUser->isActive() ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="<?= htmlspecialchars($url('/admin/users/' . $listedUser->id() . '/edit'), ENT_QUOTES) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <?php if ($listedUser->isActive()): ?>
                                    <form method="POST" action="<?= htmlspecialchars($url('/admin/users/' . $listedUser->id() . '/deactivate'), ENT_QUOTES) ?>">
                                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken('deactivate_user'), ENT_QUOTES) ?>">
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
