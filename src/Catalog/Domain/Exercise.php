<?php

declare(strict_types=1);

namespace App\Catalog\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

final class Exercise
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $muscleGroup,
        private ?string $description,
        private bool $active,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
        $this->rename($name);
        $this->changeMuscleGroup($muscleGroup);
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
            $this->muscleGroup,
            $this->description,
            $this->active,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function muscleGroup(): string
    {
        return $this->muscleGroup;
    }

    public function description(): ?string
    {
        return $this->description;
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
            throw new InvalidArgumentException('El nombre del ejercicio es obligatorio.');
        }

        $this->name = $name;
    }

    public function changeMuscleGroup(string $muscleGroup): void
    {
        $muscleGroup = trim($muscleGroup);

        if ($muscleGroup === '') {
            throw new InvalidArgumentException('El grupo muscular es obligatorio.');
        }

        $this->muscleGroup = $muscleGroup;
    }

    public function changeDescription(?string $description): void
    {
        $description = $description !== null ? trim($description) : null;
        $this->description = $description === '' ? null : $description;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function activate(): void
    {
        $this->active = true;
    }
}
