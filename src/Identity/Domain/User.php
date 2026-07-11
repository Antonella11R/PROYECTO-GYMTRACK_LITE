<?php

declare(strict_types=1);

namespace App\Identity\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

final class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private Email $email,
        private string $passwordHash,
        private UserRole $role,
        private ?int $trainerId,
        private bool $active,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
        $this->rename($name);
        $this->changeRole($role, $trainerId);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->name,
            $this->email,
            $this->passwordHash,
            $this->role,
            $this->trainerId,
            $this->active,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function role(): UserRole
    {
        return $this->role;
    }

    public function trainerId(): ?int
    {
        return $this->trainerId;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function rename(string $name): void
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('El nombre es obligatorio.');
        }

        $this->name = $name;
    }

    public function changeEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function changePassword(string $passwordHash): void
    {
        if ($passwordHash === '') {
            throw new InvalidArgumentException('La contraseña es obligatoria.');
        }

        $this->passwordHash = $passwordHash;
    }

    public function changeRole(UserRole $role, ?int $trainerId = null): void
    {
        if ($role !== UserRole::USER && $trainerId !== null) {
            throw new InvalidArgumentException('Solo los usuarios finales pueden tener entrenador asignado.');
        }

        $this->role = $role;
        $this->trainerId = $role === UserRole::USER ? $trainerId : null;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }
}
