<?php

declare(strict_types=1);

namespace App\Identity\Presentation\Web\Controllers;

use App\Identity\Application\UserService;
use App\Shared\Infrastructure\Security\CsrfTokenManager;
use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Support\FlashMessenger;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\Support\UrlGenerator;
use App\Shared\Presentation\Web\View\ViewRenderer;
use InvalidArgumentException;

final class AuthController
{
    public function __construct(
        private ViewRenderer $views,
        private UserService $users,
        private SessionGuard $guard,
        private FlashMessenger $flashMessenger,
        private CsrfTokenManager $csrfTokenManager,
        private UrlGenerator $urlGenerator
    ) {
    }

    public function showLogin(Request $request, array $params = []): Response
    {
        return Response::html($this->views->render(
            'Identity/Presentation/Web/Views/auth/login',
            ['title' => 'Iniciar sesión']
        ));
    }

    public function login(Request $request, array $params = []): Response
    {
        try {
            $this->assertValidToken((string) $request->input('_token'), 'login');
            $user = $this->users->authenticate(
                (string) $request->input('email'),
                (string) $request->input('password')
            );

            if ($user === null) {
                throw new InvalidArgumentException('Correo o contraseña incorrectos.');
            }

            $this->guard->login($user);
            $this->flashMessenger->success('Sesión iniciada correctamente.');

            return Response::redirect($this->urlGenerator->to('/dashboard'));
        } catch (InvalidArgumentException $exception) {
            $this->flashMessenger->error($exception->getMessage());

            return Response::html($this->views->render(
                'Identity/Presentation/Web/Views/auth/login',
                [
                    'title' => 'Iniciar sesión',
                    'error' => $exception->getMessage(),
                    'values' => ['email' => (string) $request->input('email')],
                ]
            ), 422);
        }
    }

    public function logout(Request $request, array $params = []): Response
    {
        if ($this->csrfTokenManager->validate((string) $request->input('_token'), 'logout')) {
            $this->guard->logout();
            $this->flashMessenger->success('Tu sesión se cerró correctamente.');
        }

        return Response::redirect($this->urlGenerator->to('/login'));
    }

    private function assertValidToken(string $token, string $namespace): void
    {
        if (!$this->csrfTokenManager->validate($token, $namespace)) {
            throw new InvalidArgumentException('La sesión del formulario expiró. Intenta nuevamente.');
        }
    }
}
