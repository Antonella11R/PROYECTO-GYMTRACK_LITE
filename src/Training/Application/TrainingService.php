<?php

declare(strict_types=1);

namespace App\Training\Application;

use App\Catalog\Domain\ExerciseRepository;
use App\Identity\Application\UserService;
use App\Identity\Domain\User;
use App\Identity\Domain\UserRole;
use App\Training\Domain\TrainingSession;
use App\Training\Domain\TrainingSessionItem;
use App\Training\Domain\TrainingSessionRepository;
use DateTimeImmutable;
use InvalidArgumentException;

final class TrainingService
{
    public function __construct(
        private TrainingSessionRepository $trainingSessions,
        private UserService $users,
        private ExerciseRepository $exercises
    ) {
    }

    /** @return list<array<string, mixed>> */
    public function historyFor(User $viewer, ?int $athleteId = null): array
    {
        if ($athleteId !== null) {
            $this->assertAthleteAccess($viewer, $athleteId);
        }

        return $this->trainingSessions->historyForViewer($viewer, $athleteId);
    }

    /** @return list<User> */
    public function availableAthletesFor(User $viewer): array
    {
        return $this->users->athletesFor($viewer);
    }

    public function createSession(User $actor, array $payload): void
    {
        $athleteId = $this->resolveAthleteId($actor, $payload['athlete_user_id'] ?? null);
        $this->assertAthleteAccess($actor, $athleteId);

        $athlete = $this->users->findUser($athleteId);

        if ($athlete === null || $athlete->role() !== UserRole::USER || !$athlete->isActive()) {
            throw new InvalidArgumentException('Debes seleccionar un atleta válido.');
        }

        $items = $this->buildItems($payload);

        $session = new TrainingSession(
            null,
            $athleteId,
            $actor->id() ?? 0,
            new DateTimeImmutable((string) ($payload['performed_on'] ?? 'now')),
            (int) ($payload['duration_minutes'] ?? 0),
            $this->normalizeNotes($payload['notes'] ?? null),
            $items,
            new DateTimeImmutable()
        );

        $this->trainingSessions->save($session);
    }

    private function resolveAthleteId(User $actor, mixed $requestedAthleteId): int
    {
        if ($actor->role() === UserRole::USER) {
            return $actor->id() ?? 0;
        }

        $athleteId = (int) $requestedAthleteId;

        if ($athleteId <= 0) {
            throw new InvalidArgumentException('Debes seleccionar un atleta.');
        }

        return $athleteId;
    }

    private function assertAthleteAccess(User $viewer, int $athleteId): void
    {
        if ($viewer->role() === UserRole::ADMIN) {
            return;
        }

        if ($viewer->role() === UserRole::USER && $viewer->id() !== $athleteId) {
            throw new InvalidArgumentException('No puedes acceder a entrenamientos de otro usuario.');
        }

        if ($viewer->role() === UserRole::TRAINER) {
            $allowedIds = array_map(
                static fn (User $athlete): int => $athlete->id() ?? 0,
                $this->users->athletesFor($viewer)
            );

            if (!in_array($athleteId, $allowedIds, true)) {
                throw new InvalidArgumentException('Ese atleta no está asignado a tu cuenta.');
            }
        }
    }

    /**
     * @return list<TrainingSessionItem>
     */
    private function buildItems(array $payload): array
    {
        $itemsData = $payload['items'] ?? [];
        $items = [];
        $position = 1;

        foreach ($itemsData as $itemData) {
            if (($itemData['exercise_id'] ?? '') === '') {
                continue;
            }

            $exercise = $this->exercises->findById((int) $itemData['exercise_id']);

            if ($exercise === null || !$exercise->isActive()) {
                throw new InvalidArgumentException('Uno de los ejercicios seleccionados no existe o está inactivo.');
            }

            $items[] = new TrainingSessionItem(
                (int) $itemData['exercise_id'],
                (int) ($itemData['sets'] ?? 0),
                (int) ($itemData['repetitions'] ?? 0),
                (float) ($itemData['weight'] ?? 0),
                (int) ($itemData['rpe'] ?? 0),
                $position
            );
            $position++;
        }

        if ($items === []) {
            throw new InvalidArgumentException('Debes agregar al menos un detalle válido a la sesión.');
        }

        return $items;
    }

    private function normalizeNotes(mixed $notes): ?string
    {
        if ($notes === null) {
            return null;
        }

        $notes = trim((string) $notes);

        return $notes === '' ? null : $notes;
    }
}
