<?php

declare(strict_types=1);
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1"><?= htmlspecialchars($title, ENT_QUOTES) ?></h1>
                <p class="text-muted mb-0">Configura datos, rol y asignación.</p>
            </div>
            <a href="<?= htmlspecialchars($url('/admin/users'), ENT_QUOTES) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= htmlspecialchars($url($action), ENT_QUOTES) ?>" class="row g-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken('user_form'), ENT_QUOTES) ?>">

                    <div class="col-md-6">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($values['name'] ?? '', ENT_QUOTES) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Correo</label>
                        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($values['email'] ?? '', ENT_QUOTES) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Rol</label>
                        <select name="role" class="form-select" id="roleField">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= htmlspecialchars($role->value, ENT_QUOTES) ?>" <?= ($values['role'] ?? 'user') === $role->value ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($role->label(), ENT_QUOTES) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6" id="trainerField">
                        <label class="form-label">Entrenador asignado</label>
                        <select name="trainer_id" class="form-select">
                            <option value="">Sin asignar</option>
                            <?php foreach ($trainers as $trainer): ?>
                                <option value="<?= $trainer->id() ?>" <?= (string) ($values['trainer_id'] ?? '') === (string) $trainer->id() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($trainer->name(), ENT_QUOTES) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contraseña <?= str_contains($action, '/admin/users/') ? '(opcional)' : '' ?></label>
                        <input type="password" name="password" class="form-control" <?= str_contains($action, '/admin/users/') ? '' : 'required' ?>>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-dark">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $scripts = <<<HTML
<script>
    const roleField = document.getElementById('roleField');
    const trainerField = document.getElementById('trainerField');

    const updateTrainerVisibility = () => {
        trainerField.style.display = roleField.value === 'user' ? 'block' : 'none';
    };

    roleField.addEventListener('change', updateTrainerVisibility);
    updateTrainerVisibility();
</script>
HTML; ?>
