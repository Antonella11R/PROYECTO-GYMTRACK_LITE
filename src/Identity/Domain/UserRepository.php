<?php

declare(strict_types=1);

namespace App\Identity\Domain;

interface UserRepository
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    /** @return list<User> */
    public function all(): array;

    /** @return list<User> */
    public function trainers(): array;

    /** @return list<User> */
    public function activeAthletes(): array;

    /** @return list<User> */
    public function athletesForTrainer(int $trainerId): array;

    public function save(User $user): User;

    public function update(User $user): void;
}
