<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\Middleware;

use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\FlashMessenger;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\Support\UrlGenerator;

final class AuthMiddleware implements Middleware
{
    public function __construct(
        private SessionGuard $guard,
        private FlashMessenger $flashMessenger,
        private UrlGenerator $urlGenerator
    ) {
    }

    public function process(Request $request, callable $next): Response
    {
        if (!$this->guard->check()) {
            $this->flashMessenger->error('Debes iniciar sesión para continuar.');
            return Response::redirect($this->urlGenerator->to('/login'));
        }

        return $next($request);
    }
}
