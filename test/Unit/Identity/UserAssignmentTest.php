<?php

declare(strict_types=1);

namespace Tests\Unit\Identity;

use App\Identity\Domain\Email;
use App\Identity\Domain\User;
use App\Identity\Domain\UserRole;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UserAssignmentTest extends TestCase
{
    public function test_only_end_users_can_have_a_trainer_assigned(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new User(
            1,
            'Trainer Demo',
            new Email('trainer@gymtrack.test'),
            'hash',
            UserRole::TRAINER,
            99,
            true,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );
    }

    public function test_end_user_can_keep_the_assigned_trainer_id(): void
    {
        $user = new User(
            3,
            'User Demo',
            new Email('user@gymtrack.test'),
            'hash',
            UserRole::USER,
            2,
            true,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        self::assertSame(2, $user->trainerId());
    }
}
