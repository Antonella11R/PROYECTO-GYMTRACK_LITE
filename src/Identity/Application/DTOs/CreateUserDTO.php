<?php

declare(strict_types=1);

namespace App\Identity\Application\DTOs;

final class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role,
        public readonly ?int $trainerId = null
    ) {
    }
}
