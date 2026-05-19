<?php

declare(strict_types=1);

namespace App\Training\Presentation\Web\Controllers;

use App\Catalog\Application\ExerciseService;
use App\Shared\Infrastructure\Security\CsrfTokenManager;
use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\FlashMessenger;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\Support\UrlGenerator;
use App\Shared\Presentation\Web\View\ViewRenderer;
use App\Training\Application\TrainingService;
use InvalidArgumentException;

final class TrainingController
{
    public function __construct(
        private ViewRenderer $views,
        private SessionGuard $guard,
        private TrainingService $trainings,
        private ExerciseService $exercises,
        private FlashMessenger $flashMessenger,
        private CsrfTokenManager $csrfTokenManager,
        private UrlGenerator $urlGenerator
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        $viewer = $this->guard->user();

        if ($viewer === null) {
            return Response::redirect($this->urlGenerator->to('/login'));
        }

        $athleteQuery = $request->query('athlete_id');
        $selectedAthleteId = ($athleteQuery === null || $athleteQuery === '') ? null : (int) $athleteQuery;

        try {
            return Response::html($this->views->render(
                'Training/Presentation/Web/Views/trainings/index',
                [
                    'title' => 'Historial de entrenamientos',
                    'trainings' => $this->trainings->historyFor($viewer, $selectedAthleteId),
                    'athletes' => $this->trainings->availableAthletesFor($viewer),
                    'selectedAthleteId' => $selectedAthleteId,
                ]
            ));
        } catch (InvalidArgumentException $exception) {
            $this->flashMessenger->error($exception->getMessage());
            return Response::redirect($this->urlGenerator->to('/trainings'));
        }
    }

    public function create(Request $request, array $params = []): Response
    {
        $viewer = $this->guard->user();

        if ($viewer === null) {
            return Response::redirect($this->urlGenerator->to('/login'));
        }

        return Response::html($this->renderCreateForm(
            $viewer,
            [
                'athlete_user_id' => $viewer->id(),
                'performed_on' => date('Y-m-d'),
                'duration_minutes' => '60',
                'notes' => '',
                'items' => [
                    ['exercise_id' => '', 'sets' => '4', 'repetitions' => '10', 'weight' => '0', 'rpe' => '7'],
                ],
            ]
        ));
    }

    public function store(Request $request, array $params = []): Response
    {
        $viewer = $this->guard->user();

        if ($viewer === null) {
            return Response::redirect($this->urlGenerator->to('/login'));
        }

        try {
            $this->assertToken((string) $request->input('_token'), 'training_form');
            $this->trainings->createSession($viewer, $request->all());
            $this->flashMessenger->success('La sesión de entrenamiento se guardó correctamente.');

            return Response::redirect($this->urlGenerator->to('/trainings'));
        } catch (InvalidArgumentException $exception) {
            return Response::html($this->renderCreateForm(
                $viewer,
                $request->all(),
                $exception->getMessage()
            ), 422);
        }
    }

    private function renderCreateForm(object $viewer, array $values, ?string $error = null): string
    {
        $items = $values['items'] ?? [];

        if ($items === []) {
            $items = [['exercise_id' => '', 'sets' => '4', 'repetitions' => '10', 'weight' => '0', 'rpe' => '7']];
        }

        return $this->views->render('Training/Presentation/Web/Views/trainings/create', [
            'title' => 'Registrar entrenamiento',
            'viewer' => $viewer,
            'values' => array_merge($values, ['items' => $items]),
            'error' => $error,
            'athletes' => $this->trainings->availableAthletesFor($viewer),
            'exercises' => $this->exercises->listActiveExercises(),
        ]);
    }

    private function assertToken(string $token, string $namespace): void
    {
        if (!$this->csrfTokenManager->validate($token, $namespace)) {
            throw new InvalidArgumentException('La sesión del formulario expiró. Intenta nuevamente.');
        }
    }
}
