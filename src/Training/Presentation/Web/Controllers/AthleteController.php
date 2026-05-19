<?php

declare(strict_types=1);

namespace App\Training\Presentation\Web\Controllers;

use App\Identity\Application\UserService;
use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\View\ViewRenderer;

final class AthleteController
{
    public function __construct(
        private ViewRenderer $views,
        private SessionGuard $guard,
        private UserService $users
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        $viewer = $this->guard->user();

        return Response::html($this->views->render(
            'Training/Presentation/Web/Views/trainings/athletes',
            [
                'title' => 'Atletas asignados',
                'athletes' => $viewer === null ? [] : $this->users->athletesFor($viewer),
            ]
        ));
    }
}
