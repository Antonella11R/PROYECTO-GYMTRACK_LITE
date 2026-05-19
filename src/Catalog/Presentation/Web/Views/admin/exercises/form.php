<?php

declare(strict_types=1);
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1"><?= htmlspecialchars($title, ENT_QUOTES) ?></h1>
                <p class="text-muted mb-0">Define el ejercicio que formará parte del catálogo.</p>
            </div>
            <a href="<?= htmlspecialchars($url('/admin/exercises'), ENT_QUOTES) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= htmlspecialchars($url($action), ENT_QUOTES) ?>" class="row g-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken('exercise_form'), ENT_QUOTES) ?>">

                    <div class="col-md-6">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($values['name'] ?? '', ENT_QUOTES) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Grupo muscular</label>
                        <input type="text" name="muscle_group" class="form-control" required value="<?= htmlspecialchars($values['muscle_group'] ?? '', ENT_QUOTES) ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($values['description'] ?? '', ENT_QUOTES) ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select name="is_active" class="form-select">
                            <option value="1" <?= ($values['is_active'] ?? '1') === '1' ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= ($values['is_active'] ?? '1') === '0' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-dark">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
