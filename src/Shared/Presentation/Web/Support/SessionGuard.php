<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\Support;

use App\Identity\Domain\User;
use App\Identity\Domain\UserRepository;

final class SessionGuard
{
    private ?User $resolvedUser = null;
    private bool $resolved = false;

    public function __construct(private UserRepository $users)
    {
    }

    public function user(): ?User
    {
        if ($this->resolved) {
            return $this->resolvedUser;
        }

        $this->resolved = true;
        $userId = $_SESSION['auth_user_id'] ?? null;

        if ($userId === null) {
            return null;
        }

        $this->resolvedUser = $this->users->findById((int) $userId);

        if ($this->resolvedUser === null || !$this->resolvedUser->isActive()) {
            $this->logout();
            return null;
        }

        return $this->resolvedUser;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function login(User $user): void
    {
        session_regenerate_id(true);
        $_SESSION['auth_user_id'] = $user->id();
        $this->resolvedUser = $user;
        $this->resolved = true;
    }

    public function logout(): void
    {
        unset($_SESSION['auth_user_id']);
        $this->resolvedUser = null;
        $this->resolved = true;
        session_regenerate_id(true);
    }
}
