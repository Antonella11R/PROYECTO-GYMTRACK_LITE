<?php

declare(strict_types=1);

namespace App\Training\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

final class TrainingSession
{
    /**
     * @param list<TrainingSessionItem> $items
     */
    public function __construct(
        private ?int $id,
        private int $athleteUserId,
        private int $recordedByUserId,
        private DateTimeImmutable $performedOn,
        private int $durationMinutes,
        private ?string $notes,
        private array $items,
        private DateTimeImmutable $createdAt
    ) {
        if ($this->athleteUserId <= 0 || $this->recordedByUserId <= 0) {
            throw new InvalidArgumentException('La sesión requiere usuarios válidos.');
        }

        if ($this->durationMinutes <= 0) {
            throw new InvalidArgumentException('La duración debe ser mayor a cero.');
        }

        if ($this->items === []) {
            throw new InvalidArgumentException('Debes registrar al menos un ejercicio en la sesión.');
        }
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function athleteUserId(): int
    {
        return $this->athleteUserId;
    }

    public function recordedByUserId(): int
    {
        return $this->recordedByUserId;
    }

    public function performedOn(): DateTimeImmutable
    {
        return $this->performedOn;
    }

    public function durationMinutes(): int
    {
        return $this->durationMinutes;
    }

    public function notes(): ?string
    {
        return $this->notes;
    }

    /** @return list<TrainingSessionItem> */
    public function items(): array
    {
        return $this->items;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
