<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\Routing;

use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Middleware\Middleware;

final class Router
{
    /** @var list<Route> */
    private array $routes = [];

    public function add(string $method, string $path, callable $action, array $middlewares = []): void
    {
        $this->routes[] = new Route(strtoupper($method), $path, $action, $middlewares);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route->method !== $request->method()) {
                continue;
            }

            $parameters = $this->match($route->path, $request->path());

            if ($parameters === null) {
                continue;
            }

            $runner = array_reduce(
                array_reverse($route->middlewares),
                fn (callable $next, Middleware $middleware): callable => fn (Request $currentRequest): Response => $middleware->process($currentRequest, $next),
                fn (Request $currentRequest): Response => call_user_func($route->action, $currentRequest, $parameters)
            );

            return $runner($request);
        }

        return Response::html('<h1>404</h1><p>La página solicitada no existe.</p>', 404);
    }

    private function match(string $routePath, string $requestPath): ?array
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . rtrim($pattern, '/') . '/?$#';
        $requestPath = '/' . ltrim($requestPath, '/');

        if (!preg_match($pattern, $requestPath, $matches)) {
            return null;
        }

        return array_filter(
            $matches,
            static fn (string|int $key): bool => is_string($key),
            ARRAY_FILTER_USE_KEY
        );
    }
}
