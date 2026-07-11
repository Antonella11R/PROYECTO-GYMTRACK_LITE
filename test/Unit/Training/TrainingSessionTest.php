<?php

declare(strict_types=1);

namespace Tests\Unit\Training;

use App\Training\Domain\TrainingSession;
use App\Training\Domain\TrainingSessionItem;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TrainingSessionTest extends TestCase
{
    public function test_training_session_requires_at_least_one_item(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TrainingSession(
            null,
            3,
            2,
            new DateTimeImmutable('2026-05-10'),
            60,
            null,
            [],
            new DateTimeImmutable()
        );
    }

    public function test_training_session_item_requires_rpe_inside_range(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TrainingSessionItem(1, 4, 10, 50.0, 12, 1);
    }
}
