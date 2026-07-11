<?php

declare(strict_types=1);

namespace App\Training\Infrastructure;

use App\Identity\Domain\User;
use App\Identity\Domain\UserRole;
use App\Training\Domain\TrainingSession;
use App\Training\Domain\TrainingSessionRepository;
use PDO;

final class PdoTrainingSessionRepository implements TrainingSessionRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function save(TrainingSession $session): void
    {
        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO training_sessions (athlete_user_id, recorded_by_user_id, performed_on, duration_minutes, notes, created_at)
                 VALUES (:athlete_user_id, :recorded_by_user_id, :performed_on, :duration_minutes, :notes, :created_at)'
            );

            $statement->execute([
                'athlete_user_id' => $session->athleteUserId(),
                'recorded_by_user_id' => $session->recordedByUserId(),
                'performed_on' => $session->performedOn()->format('Y-m-d'),
                'duration_minutes' => $session->durationMinutes(),
                'notes' => $session->notes(),
                'created_at' => $session->createdAt()->format('Y-m-d H:i:s'),
            ]);

            $sessionId = (int) $this->pdo->lastInsertId();

            $itemStatement = $this->pdo->prepare(
                'INSERT INTO training_session_items
                    (training_session_id, exercise_id, sets, repetitions, weight, rpe, position)
                 VALUES
                    (:training_session_id, :exercise_id, :sets, :repetitions, :weight, :rpe, :position)'
            );

            foreach ($session->items() as $item) {
                $itemStatement->execute([
                    'training_session_id' => $sessionId,
                    'exercise_id' => $item->exerciseId(),
                    'sets' => $item->sets(),
                    'repetitions' => $item->repetitions(),
                    'weight' => $item->weight(),
                    'rpe' => $item->rpe(),
                    'position' => $item->position(),
                ]);
            }

            $this->pdo->commit();
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function historyForViewer(User $viewer, ?int $athleteId = null): array
    {
        $conditions = [];
        $parameters = [];

        if ($athleteId !== null) {
            $conditions[] = 'ts.athlete_user_id = :athlete_id';
            $parameters['athlete_id'] = $athleteId;
        } elseif ($viewer->role() === UserRole::USER) {
            $conditions[] = 'ts.athlete_user_id = :athlete_id';
            $parameters['athlete_id'] = $viewer->id();
        } elseif ($viewer->role() === UserRole::TRAINER) {
            $conditions[] = 'athlete.trainer_id = :trainer_id';
            $parameters['trainer_id'] = $viewer->id();
        }

        $whereClause = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $sql = <<<SQL
            SELECT
                ts.id,
                ts.performed_on,
                ts.duration_minutes,
                ts.notes,
                athlete.name AS athlete_name,
                recorder.name AS recorded_by_name,
                GROUP_CONCAT(
                    CONCAT(ex.name, ' · ', tsi.sets, 'x', tsi.repetitions, ' · ', tsi.weight, ' kg · RPE ', tsi.rpe)
                    ORDER BY tsi.position
                    SEPARATOR "\n"
                ) AS summary
            FROM training_sessions ts
            INNER JOIN users athlete ON athlete.id = ts.athlete_user_id
            INNER JOIN users recorder ON recorder.id = ts.recorded_by_user_id
            INNER JOIN training_session_items tsi ON tsi.training_session_id = ts.id
            INNER JOIN exercises ex ON ex.id = tsi.exercise_id
            {$whereClause}
            GROUP BY ts.id, ts.performed_on, ts.duration_minutes, ts.notes, athlete.name, recorder.name
            ORDER BY ts.performed_on DESC, ts.id DESC
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }
}
