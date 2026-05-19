<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

final class CsrfTokenManager
{
    public function token(string $namespace = 'default'): string
    {
        if (!isset($_SESSION['_csrf'][$namespace])) {
            $_SESSION['_csrf'][$namespace] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'][$namespace];
    }

    public function validate(?string $token, string $namespace = 'default'): bool
    {
        $storedToken = $_SESSION['_csrf'][$namespace] ?? null;

        if ($storedToken === null || $token === null) {
            return false;
        }

        return hash_equals($storedToken, $token);
    }
}
