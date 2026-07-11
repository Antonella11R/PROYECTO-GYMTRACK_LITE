<?php

declare(strict_types=1);

$isSelfManaged = $viewer->role()->value === 'user';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Registrar entrenamiento</h1>
        <p class="text-muted mb-0">Guarda una sesión con múltiples ejercicios.</p>
    </div>
    <a href="<?= htmlspecialchars($url('/trainings'), ENT_QUOTES) ?>" class="btn btn-outline-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($url('/trainings'), ENT_QUOTES) ?>" id="trainingForm">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken('training_form'), ENT_QUOTES) ?>">

            <div class="row g-3 mb-4">
                <?php if (!$isSelfManaged): ?>
                    <div class="col-md-4">
                        <label class="form-label">Atleta</label>
                        <select name="athlete_user_id" class="form-select" required>
                            <option value="">Selecciona un atleta</option>
                            <?php foreach ($athletes as $athlete): ?>
                                <option value="<?= $athlete->id() ?>" <?= (string) ($values['athlete_user_id'] ?? '') === (string) $athlete->id() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($athlete->name(), ENT_QUOTES) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="athlete_user_id" value="<?= htmlspecialchars((string) ($values['athlete_user_id'] ?? $viewer->id()), ENT_QUOTES) ?>">
                <?php endif; ?>

                <div class="col-md-4">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="performed_on" class="form-control" required value="<?= htmlspecialchars($values['performed_on'] ?? date('Y-m-d'), ENT_QUOTES) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Duración (minutos)</label>
                    <input type="number" min="1" name="duration_minutes" class="form-control" required value="<?= htmlspecialchars((string) ($values['duration_minutes'] ?? '60'), ENT_QUOTES) ?>">
                </div>

                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($values['notes'] ?? '', ENT_QUOTES) ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Detalle de ejercicios</h2>
                <button type="button" class="btn btn-sm btn-outline-dark" id="addItemButton">Agregar fila</button>
            </div>

            <div id="itemsContainer" class="d-grid gap-3">
                <?php foreach ($values['items'] as $index => $item): ?>
                    <div class="border rounded-3 p-3 training-item">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Ejercicio</label>
                                <select name="items[<?= $index ?>][exercise_id]" class="form-select" required>
                                    <option value="">Selecciona</option>
                                    <?php foreach ($exercises as $exercise): ?>
                                        <option value="<?= $exercise->id() ?>" <?= (string) ($item['exercise_id'] ?? '') === (string) $exercise->id() ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($exercise->name() . ' · ' . $exercise->muscleGroup(), ENT_QUOTES) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Series</label>
                                <input type="number" min="1" name="items[<?= $index ?>][sets]" class="form-control" required value="<?= htmlspecialchars((string) ($item['sets'] ?? '4'), ENT_QUOTES) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Repeticiones</label>
                                <input type="number" min="1" name="items[<?= $index ?>][repetitions]" class="form-control" required value="<?= htmlspecialchars((string) ($item['repetitions'] ?? '10'), ENT_QUOTES) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Peso (kg)</label>
                                <input type="number" step="0.01" min="0" name="items[<?= $index ?>][weight]" class="form-control" required value="<?= htmlspecialchars((string) ($item['weight'] ?? '0'), ENT_QUOTES) ?>">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">RPE</label>
                                <input type="number" min="1" max="10" name="items[<?= $index ?>][rpe]" class="form-control" required value="<?= htmlspecialchars((string) ($item['rpe'] ?? '7'), ENT_QUOTES) ?>">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100 remove-item">X</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-4">
                <button class="btn btn-dark">Guardar sesión</button>
            </div>
        </form>
    </div>
</div>

<?php
$exerciseOptions = '';
foreach ($exercises as $exercise) {
    $exerciseOptions .= sprintf(
        '<option value="%d">%s</option>',
        $exercise->id(),
        htmlspecialchars($exercise->name() . ' · ' . $exercise->muscleGroup(), ENT_QUOTES)
    );
}
$scripts = <<<HTML
<script>
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemButton = document.getElementById('addItemButton');
    let nextIndex = {$index} + 1;

    const createItem = (index) => `
        <div class="border rounded-3 p-3 training-item">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Ejercicio</label>
                    <select name="items[\${index}][exercise_id]" class="form-select" required>
                        <option value="">Selecciona</option>
                        {$exerciseOptions}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Series</label>
                    <input type="number" min="1" name="items[\${index}][sets]" class="form-control" value="4" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Repeticiones</label>
                    <input type="number" min="1" name="items[\${index}][repetitions]" class="form-control" value="10" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Peso (kg)</label>
                    <input type="number" step="0.01" min="0" name="items[\${index}][weight]" class="form-control" value="0" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label">RPE</label>
                    <input type="number" min="1" max="10" name="items[\${index}][rpe]" class="form-control" value="7" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100 remove-item">X</button>
                </div>
            </div>
        </div>`;

    addItemButton.addEventListener('click', () => {
        itemsContainer.insertAdjacentHTML('beforeend', createItem(nextIndex));
        nextIndex++;
    });

    itemsContainer.addEventListener('click', (event) => {
        if (!event.target.classList.contains('remove-item')) {
            return;
        }

        if (itemsContainer.querySelectorAll('.training-item').length === 1) {
            return;
        }

        event.target.closest('.training-item').remove();
    });
</script>
HTML;
?>
