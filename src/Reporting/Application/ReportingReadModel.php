<?php

declare(strict_types=1);

namespace App\Reporting\Application;

interface ReportingReadModel
{
    public function adminMetrics(): array;

    public function trainerMetrics(int $trainerId): array;

    public function userMetrics(int $userId): array;

    /** @return list<array<string, mixed>> */
    public function recentSessionsForAdmin(int $limit = 5): array;

    /** @return list<array<string, mixed>> */
    public function recentSessionsForTrainer(int $trainerId, int $limit = 5): array;

    /** @return list<array<string, mixed>> */
    public function recentSessionsForUser(int $userId, int $limit = 5): array;

    public function defaultExerciseForAthlete(int $athleteId): ?int;

    /** @return list<array{performed_on:string, weight:float}> */
    public function progressPoints(int $athleteId, int $exerciseId): array;
}
