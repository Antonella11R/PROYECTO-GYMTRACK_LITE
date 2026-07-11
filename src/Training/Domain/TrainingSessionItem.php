<?php

declare(strict_types=1);

namespace App\Training\Domain;

use InvalidArgumentException;

final class TrainingSessionItem
{
    public function __construct(
        private int $exerciseId,
        private int $sets,
        private int $repetitions,
        private float $weight,
        private int $rpe,
        private int $position
    ) {
        if ($this->exerciseId <= 0) {
            throw new InvalidArgumentException('Debes seleccionar un ejercicio válido.');
        }

        if ($this->sets <= 0 || $this->repetitions <= 0) {
            throw new InvalidArgumentException('Series y repeticiones deben ser mayores a cero.');
        }

        if ($this->weight < 0) {
            throw new InvalidArgumentException('El peso no puede ser negativo.');
        }

        if ($this->rpe < 1 || $this->rpe > 10) {
            throw new InvalidArgumentException('El RPE debe estar entre 1 y 10.');
        }

        if ($this->position <= 0) {
            throw new InvalidArgumentException('La posición del detalle es inválida.');
        }
    }

    public function exerciseId(): int
    {
        return $this->exerciseId;
    }

    public function sets(): int
    {
        return $this->sets;
    }

    public function repetitions(): int
    {
        return $this->repetitions;
    }

    public function weight(): float
    {
        return $this->weight;
    }

    public function rpe(): int
    {
        return $this->rpe;
    }

    public function position(): int
    {
        return $this->position;
    }
}
