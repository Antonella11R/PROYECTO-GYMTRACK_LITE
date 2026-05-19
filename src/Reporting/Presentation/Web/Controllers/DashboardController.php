<?php

declare(strict_types=1);

namespace App\Reporting\Presentation\Web\Controllers;

use App\Reporting\Application\ReportingService;
use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\Support\UrlGenerator;
use App\Shared\Presentation\Web\View\ViewRenderer;

final class DashboardController
{
    public function __construct(
        private ViewRenderer $views,
        private SessionGuard $guard,
        private ReportingService $reporting,
        private UrlGenerator $urlGenerator
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        $viewer = $this->guard->user();

        if ($viewer === null) {
            return Response::redirect($this->urlGenerator->to('/login'));
        }

        $dashboard = $this->reporting->dashboardFor($viewer);

        return Response::html($this->views->render(
            'Reporting/Presentation/Web/Views/dashboard/' . $dashboard['role'],
            [
                'title' => 'Dashboard',
                'dashboard' => $dashboard,
            ]
        ));
    }
}
