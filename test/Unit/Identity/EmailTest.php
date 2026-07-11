<?php

declare(strict_types=1);

namespace Tests\Unit\Identity;

use App\Identity\Domain\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function test_it_normalizes_the_email_to_lowercase(): void
    {
        $email = new Email('USER@GymTrack.TEST');

        self::assertSame('user@gymtrack.test', $email->value());
    }

    public function test_it_rejects_invalid_emails(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Email('correo-invalido');
    }
}
