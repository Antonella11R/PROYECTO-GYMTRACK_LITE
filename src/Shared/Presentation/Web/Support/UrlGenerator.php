<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\Support;

use App\Shared\Infrastructure\Config\AppConfig;

final class UrlGenerator
{
    public function __construct(private AppConfig $config)
    {
    }

    public function to(string $path = '/'): string
    {
        $normalizedPath = '/' . ltrim($path, '/');
        $basePath = $this->config->basePath();

        if ($normalizedPath === '/') {
            return $basePath === '' ? '/' : $basePath . '/';
        }

        return $basePath . $normalizedPath;
    }
}
