<?php

declare(strict_types=1);

namespace App\Identity\Domain;

use InvalidArgumentException;

final class Email
{
    public function __construct(private string $value)
    {
        $normalized = mb_strtolower(trim($value));

        if (!filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Debes indicar un correo válido.');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
