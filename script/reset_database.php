<?php

declare(strict_types=1);

function loadEnvFile(string $path): array
{
    if (!is_file($path)) {
        throw new RuntimeException(sprintf('No se encontró el archivo de entorno: %s', $path));
    }

    $variables = [];

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $variables[trim($key)] = trim($value, " \t\n\r\0\x0B\"");
    }

    return $variables;
}

function executeSqlFile(PDO $pdo, string $path, string $database): void
{
    $sql = file_get_contents($path);

    if ($sql === false) {
        throw new RuntimeException(sprintf('No se pudo leer el archivo SQL: %s', $path));
    }

    $sql = str_replace('`gymtrack_lite`', sprintf('`%s`', $database), $sql);

    $pdo->exec($sql);
}

$rootPath = dirname(__DIR__);
$envFile = $argv[1] ?? '.env';
$environment = loadEnvFile($rootPath . DIRECTORY_SEPARATOR . ltrim($envFile, DIRECTORY_SEPARATOR));

$host = $environment['DB_HOST'] ?? '127.0.0.1';
$port = (int) ($environment['DB_PORT'] ?? 3306);
$database = $environment['DB_DATABASE'] ?? 'gymtrack_lite';
$username = $environment['DB_USERNAME'] ?? 'root';
$password = $environment['DB_PASSWORD'] ?? '';

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port),
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$pdo->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $database));

$migrationPath = $rootPath . '/script/migrations/001_initial_schema.sql';
$seedPath = $rootPath . '/script/seeds/001_demo_seed.sql';

executeSqlFile($pdo, $migrationPath, $database);
executeSqlFile($pdo, $seedPath, $database);

echo sprintf("Base de datos '%s' reconstruida correctamente.\n", $database);
