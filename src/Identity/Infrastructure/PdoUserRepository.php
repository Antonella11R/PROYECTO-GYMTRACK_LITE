<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure;

use App\Identity\Domain\Email;
use App\Identity\Domain\User;
use App\Identity\Domain\UserRepository;
use App\Identity\Domain\UserRole;
use DateTimeImmutable;
use PDO;

final class PdoUserRepository implements UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $record = $statement->fetch();

        return $record === false ? null : $this->map($record);
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => mb_strtolower(trim($email))]);
        $record = $statement->fetch();

        return $record === false ? null : $this->map($record);
    }

    public function all(): array
    {
        $statement = $this->pdo->query('SELECT * FROM users ORDER BY created_at DESC');

        return array_map($this->map(...), $statement->fetchAll());
    }

    public function trainers(): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM users WHERE role = 'trainer' AND is_active = 1 ORDER BY name ASC");
        $statement->execute();

        return array_map($this->map(...), $statement->fetchAll());
    }

    public function activeAthletes(): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM users WHERE role = 'user' AND is_active = 1 ORDER BY name ASC");
        $statement->execute();

        return array_map($this->map(...), $statement->fetchAll());
    }

    public function athletesForTrainer(int $trainerId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM users WHERE role = 'user' AND is_active = 1 AND trainer_id = :trainer_id ORDER BY name ASC"
        );
        $statement->execute(['trainer_id' => $trainerId]);

        return array_map($this->map(...), $statement->fetchAll());
    }

    public function save(User $user): User
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (name, email, password_hash, role, trainer_id, is_active, created_at, updated_at)
             VALUES (:name, :email, :password_hash, :role, :trainer_id, :is_active, :created_at, :updated_at)'
        );

        $statement->execute([
            'name' => $user->name(),
            'email' => $user->email()->value(),
            'password_hash' => $user->passwordHash(),
            'role' => $user->role()->value,
            'trainer_id' => $user->trainerId(),
            'is_active' => $user->isActive() ? 1 : 0,
            'created_at' => $user->createdAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->updatedAt()->format('Y-m-d H:i:s'),
        ]);

        return $user->withId((int) $this->pdo->lastInsertId());
    }

    public function update(User $user): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE users
             SET name = :name,
                 email = :email,
                 password_hash = :password_hash,
                 role = :role,
                 trainer_id = :trainer_id,
                 is_active = :is_active,
                 updated_at = NOW()
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $user->id(),
            'name' => $user->name(),
            'email' => $user->email()->value(),
            'password_hash' => $user->passwordHash(),
            'role' => $user->role()->value,
            'trainer_id' => $user->trainerId(),
            'is_active' => $user->isActive() ? 1 : 0,
        ]);
    }

    private function map(array $record): User
    {
        return new User(
            (int) $record['id'],
            $record['name'],
            new Email($record['email']),
            $record['password_hash'],
            UserRole::from($record['role']),
            $record['trainer_id'] !== null ? (int) $record['trainer_id'] : null,
            (bool) $record['is_active'],
            new DateTimeImmutable($record['created_at']),
            new DateTimeImmutable($record['updated_at'])
        );
    }
}
