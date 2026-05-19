<?php

declare(strict_types=1);

namespace Tests\Integration\Support;

use App\Shared\Infrastructure\Bootstrap\AppFactory;
use App\Shared\Infrastructure\Bootstrap\BootstrappedApp;
use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

abstract class IntegrationTestCase extends TestCase
{
    protected BootstrappedApp $app;
    protected array $env = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->destroySession();
        $this->env = $this->loadEnv(dirname(__DIR__, 3) . '/.env.testing');
        $this->resetDatabase();
        $this->app = AppFactory::create(dirname(__DIR__, 3), '.env.testing');
    }

    protected function tearDown(): void
    {
        $this->destroySession();
        parent::tearDown();
    }

    protected function request(string $method, string $path, array $query = [], array $body = []): Response
    {
        return $this->app->application()->handle(new Request(strtoupper($method), $path, $query, $body));
    }

    protected function login(string $email, string $password = 'Demo123!'): Response
    {
        return $this->request('POST', '/login', [], [
            '_token' => $this->app->csrf()->token('login'),
            'email' => $email,
            'password' => $password,
        ]);
    }

    protected function connection(): PDO
    {
        return new PDO(
            sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $this->env['DB_HOST'],
                (int) $this->env['DB_PORT'],
                $this->env['DB_DATABASE']
            ),
            $this->env['DB_USERNAME'],
            $this->env['DB_PASSWORD'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    private function resetDatabase(): void
    {
        $adminPdo = new PDO(
            sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $this->env['DB_HOST'], (int) $this->env['DB_PORT']),
            $this->env['DB_USERNAME'],
            $this->env['DB_PASSWORD'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $adminPdo->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $this->env['DB_DATABASE']));

        foreach ([
            dirname(__DIR__, 3) . '/script/migrations/001_initial_schema.sql',
            dirname(__DIR__, 3) . '/script/seeds/001_demo_seed.sql',
        ] as $sqlFile) {
            $sql = file_get_contents($sqlFile);

            if ($sql === false) {
                throw new RuntimeException(sprintf('No se pudo leer el archivo SQL: %s', $sqlFile));
            }

            $sql = str_replace('`gymtrack_lite`', sprintf('`%s`', $this->env['DB_DATABASE']), $sql);
            $adminPdo->exec($sql);
        }
    }

    private function loadEnv(string $path): array
    {
        $values = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $values[trim($key)] = trim($value, " \t\n\r\0\x0B\"");
        }

        return $values;
    }

    private function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_unset();
            session_destroy();
        }
    }
}
