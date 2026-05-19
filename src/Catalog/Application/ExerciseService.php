<?php

declare(strict_types=1);

namespace App\Catalog\Application;

use App\Catalog\Domain\Exercise;
use App\Catalog\Domain\ExerciseRepository;
use DateTimeImmutable;
use InvalidArgumentException;

final class ExerciseService
{
    public function __construct(private ExerciseRepository $exercises)
    {
    }

    /** @return list<Exercise> */
    public function listExercises(): array
    {
        return $this->exercises->all();
    }

    /** @return list<Exercise> */
    public function listActiveExercises(): array
    {
        return $this->exercises->active();
    }

    public function findExercise(int $id): ?Exercise
    {
        return $this->exercises->findById($id);
    }

    public function create(array $data): Exercise
    {
        $now = new DateTimeImmutable();
        $exercise = new Exercise(
            null,
            (string) ($data['name'] ?? ''),
            (string) ($data['muscle_group'] ?? ''),
            $this->normalizeDescription($data['description'] ?? null),
            true,
            $now,
            $now
        );

        return $this->exercises->save($exercise);
    }

    public function update(int $id, array $data): void
    {
        $exercise = $this->exercises->findById($id);

        if ($exercise === null) {
            throw new InvalidArgumentException('El ejercicio solicitado no existe.');
        }

        $exercise->rename((string) ($data['name'] ?? ''));
        $exercise->changeMuscleGroup((string) ($data['muscle_group'] ?? ''));
        $exercise->changeDescription($this->normalizeDescription($data['description'] ?? null));

        if (($data['is_active'] ?? '1') === '1') {
            $exercise->activate();
        } else {
            $exercise->deactivate();
        }

        $this->exercises->update($exercise);
    }

    public function deactivate(int $id): void
    {
        $exercise = $this->exercises->findById($id);

        if ($exercise === null) {
            throw new InvalidArgumentException('El ejercicio solicitado no existe.');
        }

        $exercise->deactivate();
        $this->exercises->update($exercise);
    }

    private function normalizeDescription(mixed $description): ?string
    {
        if ($description === null) {
            return null;
        }

        $description = trim((string) $description);

        return $description === '' ? null : $description;
    }
}
