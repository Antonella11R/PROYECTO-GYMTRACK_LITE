<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Integration\Support\IntegrationTestCase;

final class AdminManagementTest extends IntegrationTestCase
{
    public function test_trainer_cannot_access_admin_users_page(): void
    {
        $this->login('trainer@gymtrack.test');

        $response = $this->request('GET', '/admin/users');

        self::assertSame(302, $response->statusCode());
        self::assertSame('/test/dashboard', $response->headers()['Location']);
    }

    public function test_admin_can_create_a_user_and_an_exercise(): void
    {
        $this->login('admin@gymtrack.test');

        $userResponse = $this->request('POST', '/admin/users', [], [
            '_token' => $this->app->csrf()->token('user_form'),
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@gymtrack.test',
            'role' => 'user',
            'trainer_id' => '2',
            'password' => 'Password123',
        ]);

        self::assertSame(302, $userResponse->statusCode());
        self::assertSame('/test/admin/users', $userResponse->headers()['Location']);

        $exerciseResponse = $this->request('POST', '/admin/exercises', [], [
            '_token' => $this->app->csrf()->token('exercise_form'),
            'name' => 'Dominadas',
            'muscle_group' => 'Espalda',
            'description' => 'Trabajo vertical de tracción.',
            'is_active' => '1',
        ]);

        self::assertSame(302, $exerciseResponse->statusCode());
        self::assertSame('/test/admin/exercises', $exerciseResponse->headers()['Location']);

        $connection = $this->connection();

        self::assertSame('1', (string) $connection->query("SELECT COUNT(*) FROM users WHERE email = 'nuevo@gymtrack.test'")->fetchColumn());
        self::assertSame('1', (string) $connection->query("SELECT COUNT(*) FROM exercises WHERE name = 'Dominadas'")->fetchColumn());
    }
}
