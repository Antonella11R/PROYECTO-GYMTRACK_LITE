<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\Routing;

final class Route
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly mixed $action,
        public readonly array $middlewares = []
    ) {
    }
}
