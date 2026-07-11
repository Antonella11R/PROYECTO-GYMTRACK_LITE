<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? $appName, ENT_QUOTES) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(180deg, #f3f5f7 0%, #eaf0f6 100%);
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(34, 56, 101, 0.08);
        }
        .table thead th {
            white-space: nowrap;
        }
    </style>
</head>
<body>
<?php if ($currentUser !== null): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?= htmlspecialchars($url('/dashboard'), ENT_QUOTES) ?>"><?= htmlspecialchars($appName, ENT_QUOTES) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($url('/dashboard'), ENT_QUOTES) ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($url('/trainings'), ENT_QUOTES) ?>">Entrenamientos</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($url('/reports/progress'), ENT_QUOTES) ?>">Progreso</a></li>
                    <?php if (in_array($currentUser->role()->value, ['admin', 'trainer'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($url('/trainer/athletes'), ENT_QUOTES) ?>">Atletas</a></li>
                    <?php endif; ?>
                    <?php if ($currentUser->role()->value === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($url('/admin/users'), ENT_QUOTES) ?>">Usuarios</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($url('/admin/exercises'), ENT_QUOTES) ?>">Ejercicios</a></li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white-50 small">
                        <?= htmlspecialchars($currentUser->name(), ENT_QUOTES) ?> · <?= htmlspecialchars($currentUser->role()->label(), ENT_QUOTES) ?>
                    </span>
                    <form method="POST" action="<?= htmlspecialchars($url('/logout'), ENT_QUOTES) ?>" class="m-0">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken('logout'), ENT_QUOTES) ?>">
                        <button class="btn btn-outline-light btn-sm">Salir</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
<?php endif; ?>

<main class="container py-4">
    <?php foreach ($flashMessages as $flashMessage): ?>
        <div class="alert alert-<?= htmlspecialchars($flashMessage['type'], ENT_QUOTES) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flashMessage['message'], ENT_QUOTES) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <?= $content ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $scripts ?? '' ?>
</body>
</html>
