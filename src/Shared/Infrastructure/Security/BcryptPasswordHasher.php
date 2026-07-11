<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

final class BcryptPasswordHasher
{
    public function hash(string $plainTextPassword): string
    {
        return password_hash($plainTextPassword, PASSWORD_BCRYPT);
    }

    public function verify(string $plainTextPassword, string $hashedPassword): bool
    {
        return password_verify($plainTextPassword, $hashedPassword);
    }
}
