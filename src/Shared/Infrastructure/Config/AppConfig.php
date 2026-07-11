<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Config;

final class AppConfig
{
    public function __construct(private array $values)
    {
    }

    public static function fromEnvironment(): self
    {
        return new self([
            'app_env' => $_ENV['APP_ENV'] ?? 'local',
            'app_name' => $_ENV['APP_NAME'] ?? 'GymTrack Lite',
            'app_url' => rtrim($_ENV['APP_URL'] ?? '', '/'),
            'session_name' => $_ENV['SESSION_NAME'] ?? 'gymtrack_lite_session',
            'db_host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'db_port' => (int) ($_ENV['DB_PORT'] ?? 3306),
            'db_database' => $_ENV['DB_DATABASE'] ?? 'gymtrack_lite',
            'db_username' => $_ENV['DB_USERNAME'] ?? 'root',
            'db_password' => $_ENV['DB_PASSWORD'] ?? '',
        ]);
    }

    public function appName(): string
    {
        return $this->values['app_name'];
    }

    public function appUrl(): string
    {
        return $this->values['app_url'];
    }

    public function basePath(): string
    {
        $path = parse_url($this->appUrl(), PHP_URL_PATH);

        if (is_string($path) && $path !== '' && $path !== '/') {
            return rtrim($path, '/');
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        if (!is_string($scriptName) || $scriptName === '') {
            return '';
        }

        $runtimeBasePath = str_replace('\\', '/', dirname($scriptName));

        return $runtimeBasePath === '/' ? '' : rtrim($runtimeBasePath, '/');
    }

    public function sessionName(): string
    {
        return $this->values['session_name'];
    }

    public function environment(): string
    {
        return $this->values['app_env'];
    }

    public function databaseConfig(): array
    {
        return [
            'host' => $this->values['db_host'],
            'port' => $this->values['db_port'],
            'database' => $this->values['db_database'],
            'username' => $this->values['db_username'],
            'password' => $this->values['db_password'],
        ];
    }
}
