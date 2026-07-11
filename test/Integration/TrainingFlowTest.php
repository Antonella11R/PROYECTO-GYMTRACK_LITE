<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Integration\Support\IntegrationTestCase;

final class TrainingFlowTest extends IntegrationTestCase
{
    public function test_trainer_can_register_a_training_for_an_assigned_athlete(): void
    {
        $this->login('trainer@gymtrack.test');

        $response = $this->request('POST', '/trainings', [], [
            '_token' => $this->app->csrf()->token('training_form'),
            'athlete_user_id' => '3',
            'performed_on' => '2026-05-18',
            'duration_minutes' => '80',
            'notes' => 'Trabajo de seguimiento desde integración.',
            'items' => [
                ['exercise_id' => '1', 'sets' => '5', 'repetitions' => '5', 'weight' => '55', 'rpe' => '8'],
                ['exercise_id' => '4', 'sets' => '4', 'repetitions' => '8', 'weight' => '42.5', 'rpe' => '7'],
            ],
        ]);

        self::assertSame(302, $response->statusCode());
        self::assertSame('/test/trainings', $response->headers()['Location']);

        $connection = $this->connection();

        self::assertSame('5', (string) $connection->query('SELECT COUNT(*) FROM training_sessions')->fetchColumn());
        self::assertSame('14', (string) $connection->query('SELECT COUNT(*) FROM training_session_items')->fetchColumn());
    }

    public function test_progress_report_is_available_for_the_logged_user(): void
    {
        $this->login('user@gymtrack.test');

        $response = $this->request('GET', '/reports/progress');

        self::assertSame(200, $response->statusCode());
        self::assertStringContainsString('Evolución de cargas', $response->content());
        self::assertStringContainsString('Peso levantado', $response->content());
    }
}
