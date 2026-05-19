<?php

declare(strict_types=1);

namespace App\Reporting\Presentation\Web\Controllers;

use App\Reporting\Application\ReportingService;
use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\FlashMessenger;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\Support\UrlGenerator;
use App\Shared\Presentation\Web\View\ViewRenderer;
use InvalidArgumentException;

final class ReportController
{
    public function __construct(
        private ViewRenderer $views,
        private SessionGuard $guard,
        private ReportingService $reporting,
        private FlashMessenger $flashMessenger,
        private UrlGenerator $urlGenerator
    ) {
    }

    public function progress(Request $request, array $params = []): Response
    {
        $viewer = $this->guard->user();

        if ($viewer === null) {
            return Response::redirect($this->urlGenerator->to('/login'));
        }

        try {
            $athleteQuery = $request->query('athlete_id');
            $exerciseQuery = $request->query('exercise_id');
            $report = $this->reporting->progressReport(
                $viewer,
                ($athleteQuery === null || $athleteQuery === '') ? null : (int) $athleteQuery,
                ($exerciseQuery === null || $exerciseQuery === '') ? null : (int) $exerciseQuery
            );

            return Response::html($this->views->render(
                'Reporting/Presentation/Web/Views/reports/progress',
                [
                    'title' => 'Evolución de cargas',
                    'report' => $report,
                    'viewer' => $viewer,
                ]
            ));
        } catch (InvalidArgumentException $exception) {
            $this->flashMessenger->error($exception->getMessage());
            return Response::redirect($this->urlGenerator->to('/dashboard'));
        }
    }
}
