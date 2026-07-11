<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\Http;

use App\Shared\Presentation\Web\Routing\Router;

final class Application
{
    public function __construct(private Router $router)
    {
    }

    public function handle(Request $request): Response
    {
        return $this->router->dispatch($request);
    }
}
