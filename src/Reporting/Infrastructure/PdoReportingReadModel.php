<?php

declare(strict_types=1);

namespace App\Reporting\Infrastructure;

use App\Reporting\Application\ReportingReadModel;
use PDO;

final class PdoReportingReadModel implements ReportingReadModel
{
    public function __construct(private PDO $pdo)
    {
    }

    public function adminMetrics(): array
    {
        $userCounts = $this->pdo->query(
            "SELECT role, COUNT(*) AS total FROM users WHERE is_active = 1 GROUP BY role"
        )->fetchAll();

        $countsByRole = [
            'admin' => 0,
            'trainer' => 0,
            'user' => 0,
        ];

        foreach ($userCounts as $row) {
            $countsByRole[$row['role']] = (int) $row['total'];
        }

        return [
            'users_by_role' => $countsByRole,
            'exercise_count' => (int) $this->pdo->query('SELECT COUNT(*) FROM exercises WHERE is_active = 1')->fetchColumn(),
            'session_count' => (int) $this->pdo->query('SELECT COUNT(*) FROM training_sessions')->fetchColumn(),
        ];
    }

    public function trainerMetrics(int $trainerId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM users WHERE role = "user" AND is_active = 1 AND trainer_id = :trainer_id'
        );
        $statement->execute(['trainer_id' => $trainerId]);

        $sessions = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM training_sessions ts
             INNER JOIN users athlete ON athlete.id = ts.athlete_user_id
             WHERE athlete.trainer_id = :trainer_id'
        );
        $sessions->execute(['trainer_id' => $trainerId]);

        return [
            'athlete_count' => (int) $statement->fetchColumn(),
            'session_count' => (int) $sessions->fetchColumn(),
        ];
    }

    public function userMetrics(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) AS total_sessions, COALESCE(SUM(duration_minutes), 0) AS total_minutes
             FROM training_sessions
             WHERE athlete_user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);

        $row = $statement->fetch() ?: ['total_sessions' => 0, 'total_minutes' => 0];

        return [
            'session_count' => (int) $row['total_sessions'],
            'total_minutes' => (int) $row['total_minutes'],
        ];
    }

    public function recentSessionsForAdmin(int $limit = 5): array
    {
        return $this->recentSessions('1=1', [], $limit);
    }

    public function recentSessionsForTrainer(int $trainerId, int $limit = 5): array
    {
        return $this->recentSessions('athlete.trainer_id = :trainer_id', ['trainer_id' => $trainerId], $limit);
    }

    public function recentSessionsForUser(int $userId, int $limit = 5): array
    {
        return $this->recentSessions('ts.athlete_user_id = :athlete_user_id', ['athlete_user_id' => $userId], $limit);
    }

    public function defaultExerciseForAthlete(int $athleteId): ?int
    {
        $statement = $this->pdo->prepare(
            'SELECT tsi.exercise_id
             FROM training_sessions ts
             INNER JOIN training_session_items tsi ON tsi.training_session_id = ts.id
             WHERE ts.athlete_user_id = :athlete_user_id
             ORDER BY ts.performed_on DESC, ts.id DESC, tsi.position ASC
             LIMIT 1'
        );
        $statement->execute(['athlete_user_id' => $athleteId]);

        $exerciseId = $statement->fetchColumn();

        return $exerciseId === false ? null : (int) $exerciseId;
    }

    public function progressPoints(int $athleteId, int $exerciseId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT ts.performed_on, MAX(tsi.weight) AS weight
             FROM training_sessions ts
             INNER JOIN training_session_items tsi ON tsi.training_session_id = ts.id
             WHERE ts.athlete_user_id = :athlete_user_id
               AND tsi.exercise_id = :exercise_id
             GROUP BY ts.performed_on
             ORDER BY ts.performed_on ASC'
        );
        $statement->execute([
            'athlete_user_id' => $athleteId,
            'exercise_id' => $exerciseId,
        ]);

        return array_map(
            static fn (array $row): array => [
                'performed_on' => $row['performed_on'],
                'weight' => (float) $row['weight'],
            ],
            $statement->fetchAll()
        );
    }

    /** @return list<array<string, mixed>> */
    private function recentSessions(string $condition, array $parameters, int $limit): array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                ts.id,
                ts.performed_on,
                ts.duration_minutes,
                athlete.name AS athlete_name,
                recorder.name AS recorded_by_name,
                COUNT(tsi.id) AS exercise_count
             FROM training_sessions ts
             INNER JOIN users athlete ON athlete.id = ts.athlete_user_id
             INNER JOIN users recorder ON recorder.id = ts.recorded_by_user_id
             INNER JOIN training_session_items tsi ON tsi.training_session_id = ts.id
             WHERE {$condition}
             GROUP BY ts.id, ts.performed_on, ts.duration_minutes, athlete.name, recorder.name
             ORDER BY ts.performed_on DESC, ts.id DESC
             LIMIT {$limit}"
        );
        $statement->execute($parameters);

        return $statement->fetchAll();
    }
}
