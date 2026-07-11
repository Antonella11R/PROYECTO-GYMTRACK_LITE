<?php

declare(strict_types=1);

namespace App\Catalog\Presentation\Web\Controllers;

use App\Catalog\Application\ExerciseService;
use App\Shared\Infrastructure\Security\CsrfTokenManager;
use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\FlashMessenger;
use App\Shared\Presentation\Web\Support\UrlGenerator;
use App\Shared\Presentation\Web\View\ViewRenderer;
use InvalidArgumentException;

final class AdminExerciseController
{
    public function __construct(
        private ViewRenderer $views,
        private ExerciseService $exercises,
        private FlashMessenger $flashMessenger,
        private CsrfTokenManager $csrfTokenManager,
        private UrlGenerator $urlGenerator
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        return Response::html($this->views->render(
            'Catalog/Presentation/Web/Views/admin/exercises/index',
            [
                'title' => 'Ejercicios',
                'exercises' => $this->exercises->listExercises(),
            ]
        ));
    }

    public function create(Request $request, array $params = []): Response
    {
        return Response::html($this->renderForm(
            'Crear ejercicio',
            '/admin/exercises',
            ['name' => '', 'muscle_group' => '', 'description' => '', 'is_active' => '1']
        ));
    }

    public function store(Request $request, array $params = []): Response
    {
        try {
            $this->assertToken((string) $request->input('_token'), 'exercise_form');
            $this->exercises->create($request->all());
            $this->flashMessenger->success('Ejercicio creado correctamente.');

            return Response::redirect($this->urlGenerator->to('/admin/exercises'));
        } catch (InvalidArgumentException $exception) {
            return Response::html($this->renderForm(
                'Crear ejercicio',
                '/admin/exercises',
                $request->all(),
                $exception->getMessage()
            ), 422);
        }
    }

    public function edit(Request $request, array $params): Response
    {
        $exercise = $this->exercises->findExercise((int) $params['id']);

        if ($exercise === null) {
            $this->flashMessenger->error('El ejercicio solicitado no existe.');
            return Response::redirect($this->urlGenerator->to('/admin/exercises'));
        }

        return Response::html($this->renderForm(
            'Editar ejercicio',
            '/admin/exercises/' . $exercise->id(),
            [
                'name' => $exercise->name(),
                'muscle_group' => $exercise->muscleGroup(),
                'description' => $exercise->description() ?? '',
                'is_active' => $exercise->isActive() ? '1' : '0',
            ]
        ));
    }

    public function update(Request $request, array $params): Response
    {
        try {
            $this->assertToken((string) $request->input('_token'), 'exercise_form');
            $this->exercises->update((int) $params['id'], $request->all());
            $this->flashMessenger->success('Ejercicio actualizado correctamente.');

            return Response::redirect($this->urlGenerator->to('/admin/exercises'));
        } catch (InvalidArgumentException $exception) {
            return Response::html($this->renderForm(
                'Editar ejercicio',
                '/admin/exercises/' . (int) $params['id'],
                $request->all(),
                $exception->getMessage()
            ), 422);
        }
    }

    public function deactivate(Request $request, array $params): Response
    {
        if (!$this->csrfTokenManager->validate((string) $request->input('_token'), 'deactivate_exercise')) {
            $this->flashMessenger->error('La solicitud expiró. Intenta nuevamente.');
            return Response::redirect($this->urlGenerator->to('/admin/exercises'));
        }

        try {
            $this->exercises->deactivate((int) $params['id']);
            $this->flashMessenger->success('Ejercicio desactivado correctamente.');
        } catch (InvalidArgumentException $exception) {
            $this->flashMessenger->error($exception->getMessage());
        }

        return Response::redirect($this->urlGenerator->to('/admin/exercises'));
    }

    private function renderForm(string $title, string $action, array $values, ?string $error = null): string
    {
        return $this->views->render('Catalog/Presentation/Web/Views/admin/exercises/form', [
            'title' => $title,
            'action' => $action,
            'values' => $values,
            'error' => $error,
        ]);
    }

    private function assertToken(string $token, string $namespace): void
    {
        if (!$this->csrfTokenManager->validate($token, $namespace)) {
            throw new InvalidArgumentException('La sesión del formulario expiró. Intenta nuevamente.');
        }
    }
}
