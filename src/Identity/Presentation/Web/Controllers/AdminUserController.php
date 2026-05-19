<?php

declare(strict_types=1);

namespace App\Identity\Presentation\Web\Controllers;

use App\Identity\Application\UserService;
use App\Identity\Domain\UserRole;
use App\Shared\Infrastructure\Security\CsrfTokenManager;
use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\FlashMessenger;
use App\Shared\Presentation\Web\Support\UrlGenerator;
use App\Shared\Presentation\Web\View\ViewRenderer;
use InvalidArgumentException;

final class AdminUserController
{
    public function __construct(
        private ViewRenderer $views,
        private UserService $users,
        private FlashMessenger $flashMessenger,
        private CsrfTokenManager $csrfTokenManager,
        private UrlGenerator $urlGenerator
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        return Response::html($this->views->render(
            'Identity/Presentation/Web/Views/admin/users/index',
            [
                'title' => 'Usuarios',
                'users' => $this->users->listUsers(),
                'trainers' => $this->users->listTrainers(),
            ]
        ));
    }

    public function create(Request $request, array $params = []): Response
    {
        return Response::html($this->renderForm(
            'Crear usuario',
            '/admin/users',
            [
                'name' => '',
                'email' => '',
                'role' => UserRole::USER->value,
                'trainer_id' => '',
                'password' => '',
            ]
        ));
    }

    public function store(Request $request, array $params = []): Response
    {
        try {
            $this->assertToken((string) $request->input('_token'), 'user_form');
            $this->users->create($request->all());
            $this->flashMessenger->success('Usuario creado correctamente.');

            return Response::redirect($this->urlGenerator->to('/admin/users'));
        } catch (InvalidArgumentException $exception) {
            return Response::html($this->renderForm(
                'Crear usuario',
                '/admin/users',
                $request->all(),
                $exception->getMessage()
            ), 422);
        }
    }

    public function edit(Request $request, array $params): Response
    {
        $user = $this->users->findUser((int) $params['id']);

        if ($user === null) {
            $this->flashMessenger->error('El usuario solicitado no existe.');
            return Response::redirect($this->urlGenerator->to('/admin/users'));
        }

        return Response::html($this->renderForm(
            'Editar usuario',
            '/admin/users/' . $user->id(),
            [
                'name' => $user->name(),
                'email' => $user->email()->value(),
                'role' => $user->role()->value,
                'trainer_id' => $user->trainerId() ?? '',
                'password' => '',
            ]
        ));
    }

    public function update(Request $request, array $params): Response
    {
        try {
            $this->assertToken((string) $request->input('_token'), 'user_form');
            $this->users->update((int) $params['id'], $request->all());
            $this->flashMessenger->success('Usuario actualizado correctamente.');

            return Response::redirect($this->urlGenerator->to('/admin/users'));
        } catch (InvalidArgumentException $exception) {
            return Response::html($this->renderForm(
                'Editar usuario',
                '/admin/users/' . (int) $params['id'],
                $request->all(),
                $exception->getMessage()
            ), 422);
        }
    }

    public function deactivate(Request $request, array $params): Response
    {
        if (!$this->csrfTokenManager->validate((string) $request->input('_token'), 'deactivate_user')) {
            $this->flashMessenger->error('La solicitud expiró. Intenta nuevamente.');
            return Response::redirect($this->urlGenerator->to('/admin/users'));
        }

        try {
            $this->users->deactivate((int) $params['id']);
            $this->flashMessenger->success('Usuario desactivado correctamente.');
        } catch (InvalidArgumentException $exception) {
            $this->flashMessenger->error($exception->getMessage());
        }

        return Response::redirect($this->urlGenerator->to('/admin/users'));
    }

    private function renderForm(string $title, string $action, array $values, ?string $error = null): string
    {
        return $this->views->render('Identity/Presentation/Web/Views/admin/users/form', [
            'title' => $title,
            'action' => $action,
            'values' => $values,
            'error' => $error,
            'roles' => UserRole::cases(),
            'trainers' => $this->users->listTrainers(),
        ]);
    }

    private function assertToken(string $token, string $namespace): void
    {
        if (!$this->csrfTokenManager->validate($token, $namespace)) {
            throw new InvalidArgumentException('La sesión del formulario expiró. Intenta nuevamente.');
        }
    }
}
