<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\Middleware;

use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\Support\UrlGenerator;

final class GuestMiddleware implements Middleware
{
    public function __construct(
        private SessionGuard $guard,
        private UrlGenerator $urlGenerator
    )
    {
    }

    public function process(Request $request, callable $next): Response
    {
        if ($this->guard->check()) {
            return Response::redirect($this->urlGenerator->to('/dashboard'));
        }

        return $next($request);
    }
}
