<?php

declare(strict_types=1);

namespace App\Reporting\Application;

use App\Catalog\Application\ExerciseService;
use App\Identity\Application\UserService;
use App\Identity\Domain\User;
use App\Identity\Domain\UserRole;
use InvalidArgumentException;

final class ReportingService
{
    public function __construct(
        private ReportingReadModel $readModel,
        private UserService $users,
        private ExerciseService $exercises,
        private ProgressDatasetBuilder $datasetBuilder
    ) {
    }

    public function dashboardFor(User $viewer): array
    {
        return match ($viewer->role()) {
            UserRole::ADMIN => [
                'role' => 'admin',
                'metrics' => $this->readModel->adminMetrics(),
                'recentSessions' => $this->readModel->recentSessionsForAdmin(),
            ],
            UserRole::TRAINER => [
                'role' => 'trainer',
                'metrics' => $this->readModel->trainerMetrics($viewer->id() ?? 0),
                'recentSessions' => $this->readModel->recentSessionsForTrainer($viewer->id() ?? 0),
                'athletes' => $this->users->athletesFor($viewer),
            ],
            UserRole::USER => [
                'role' => 'user',
                'metrics' => $this->readModel->userMetrics($viewer->id() ?? 0),
                'recentSessions' => $this->readModel->recentSessionsForUser($viewer->id() ?? 0),
                'progress' => $this->progressReport($viewer, $viewer->id(), null),
            ],
        };
    }

    public function progressReport(User $viewer, ?int $athleteId, ?int $exerciseId): array
    {
        $availableAthletes = $this->users->athletesFor($viewer);

        if ($availableAthletes === []) {
            return [
                'athletes' => [],
                'exercises' => $this->exercises->listActiveExercises(),
                'selectedAthleteId' => null,
                'selectedExerciseId' => null,
                'chart' => ['labels' => [], 'values' => []],
            ];
        }

        $selectedAthleteId = $viewer->role() === UserRole::USER
            ? ($viewer->id() ?? 0)
            : ($athleteId ?? ($availableAthletes[0]->id() ?? 0));

        $allowedAthleteIds = array_map(static fn ($athlete) => $athlete->id(), $availableAthletes);

        if (!in_array($selectedAthleteId, $allowedAthleteIds, true)) {
            throw new InvalidArgumentException('No puedes consultar el progreso de ese atleta.');
        }

        $selectedExerciseId = $exerciseId ?? $this->readModel->defaultExerciseForAthlete($selectedAthleteId);
        $points = $selectedExerciseId === null ? [] : $this->readModel->progressPoints($selectedAthleteId, $selectedExerciseId);

        return [
            'athletes' => $availableAthletes,
            'exercises' => $this->exercises->listActiveExercises(),
            'selectedAthleteId' => $selectedAthleteId,
            'selectedExerciseId' => $selectedExerciseId,
            'chart' => $this->datasetBuilder->build($points),
        ];
    }
}
