<?php

declare(strict_types=1);

namespace App\Identity\Domain;

enum UserRole: string
{
    case ADMIN = 'admin';
    case TRAINER = 'trainer';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::TRAINER => 'Entrenador',
            self::USER => 'Usuario',
        };
    }
}
