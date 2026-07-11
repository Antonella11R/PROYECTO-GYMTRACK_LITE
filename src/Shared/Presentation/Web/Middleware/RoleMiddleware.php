<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\Middleware;

use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\FlashMessenger;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\Support\UrlGenerator;

final class RoleMiddleware implements Middleware
{
    /**
     * @param list<string> $allowedRoles
     */
    public function __construct(
        private SessionGuard $guard,
        private FlashMessenger $flashMessenger,
        private UrlGenerator $urlGenerator,
        private array $allowedRoles
    ) {
    }

    public function process(Request $request, callable $next): Response
    {
        $user = $this->guard->user();

        if ($user === null || !in_array($user->role()->value, $this->allowedRoles, true)) {
            $this->flashMessenger->error('No tienes permisos para acceder a esta sección.');
            return Response::redirect($this->urlGenerator->to('/dashboard'));
        }

        return $next($request);
    }
}
