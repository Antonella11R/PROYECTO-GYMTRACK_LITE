<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure;

use App\Catalog\Domain\Exercise;
use App\Catalog\Domain\ExerciseRepository;
use DateTimeImmutable;
use PDO;

final class PdoExerciseRepository implements ExerciseRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?Exercise
    {
        $statement = $this->pdo->prepare('SELECT * FROM exercises WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $record = $statement->fetch();

        return $record === false ? null : $this->map($record);
    }

    public function all(): array
    {
        $statement = $this->pdo->query('SELECT * FROM exercises ORDER BY name ASC');

        return array_map($this->map(...), $statement->fetchAll());
    }

    public function active(): array
    {
        $statement = $this->pdo->query('SELECT * FROM exercises WHERE is_active = 1 ORDER BY name ASC');

        return array_map($this->map(...), $statement->fetchAll());
    }

    public function save(Exercise $exercise): Exercise
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO exercises (name, muscle_group, description, is_active, created_at, updated_at)
             VALUES (:name, :muscle_group, :description, :is_active, :created_at, :updated_at)'
        );

        $statement->execute([
            'name' => $exercise->name(),
            'muscle_group' => $exercise->muscleGroup(),
            'description' => $exercise->description(),
            'is_active' => $exercise->isActive() ? 1 : 0,
            'created_at' => $exercise->createdAt()->format('Y-m-d H:i:s'),
            'updated_at' => $exercise->updatedAt()->format('Y-m-d H:i:s'),
        ]);

        return $exercise->withId((int) $this->pdo->lastInsertId());
    }

    public function update(Exercise $exercise): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE exercises
             SET name = :name,
                 muscle_group = :muscle_group,
                 description = :description,
                 is_active = :is_active,
                 updated_at = NOW()
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $exercise->id(),
            'name' => $exercise->name(),
            'muscle_group' => $exercise->muscleGroup(),
            'description' => $exercise->description(),
            'is_active' => $exercise->isActive() ? 1 : 0,
        ]);
    }

    private function map(array $record): Exercise
    {
        return new Exercise(
            (int) $record['id'],
            $record['name'],
            $record['muscle_group'],
            $record['description'],
            (bool) $record['is_active'],
            new DateTimeImmutable($record['created_at']),
            new DateTimeImmutable($record['updated_at'])
        );
    }
}
