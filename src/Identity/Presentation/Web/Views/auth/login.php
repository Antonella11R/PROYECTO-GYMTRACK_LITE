<?php

declare(strict_types=1);
?>
<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h1 class="h3 mb-1">GymTrack Lite</h1>
                <p class="text-muted mb-0">Inicia sesión para continuar</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= htmlspecialchars($url('/login'), ENT_QUOTES) ?>" class="d-grid gap-3">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken('login'), ENT_QUOTES) ?>">

                <div>
                    <label class="form-label">Correo</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($values['email'] ?? '', ENT_QUOTES) ?>">
                </div>

                <div>
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-dark w-100">Entrar</button>
            </form>

            <hr class="my-4">
            <div class="small text-muted">
                <div><strong>Admin:</strong> admin@gymtrack.test</div>
                <div><strong>Entrenador:</strong> trainer@gymtrack.test</div>
                <div><strong>Usuario:</strong> user@gymtrack.test</div>
                <div><strong>Clave demo:</strong> Demo123!</div>
            </div>
        </div>
    </div>
</div>
