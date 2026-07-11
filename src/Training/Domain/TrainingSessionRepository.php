<?php

declare(strict_types=1);

namespace App\Training\Domain;

use App\Identity\Domain\User;

interface TrainingSessionRepository
{
    public function save(TrainingSession $session): void;

    /** @return list<array<string, mixed>> */
    public function historyForViewer(User $viewer, ?int $athleteId = null): array;
}
