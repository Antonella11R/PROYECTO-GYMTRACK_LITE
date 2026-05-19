<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\View;

use App\Shared\Infrastructure\Config\AppConfig;
use App\Shared\Infrastructure\Security\CsrfTokenManager;
use App\Shared\Presentation\Web\Support\FlashMessenger;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\Support\UrlGenerator;

final class ViewRenderer
{
    public function __construct(
        private string $rootPath,
        private AppConfig $config,
        private SessionGuard $guard,
        private CsrfTokenManager $csrfTokenManager,
        private FlashMessenger $flashMessenger,
        private UrlGenerator $urlGenerator
    ) {
    }

    public function render(string $view, array $params = [], ?string $layout = 'Shared/Presentation/Web/Views/layouts/app'): string
    {
        $viewFile = $this->rootPath . '/src/' . $view . '.php';
        $layoutFile = $layout === null ? null : $this->rootPath . '/src/' . $layout . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException(sprintf('View not found: %s', $viewFile));
        }

        $shared = [
            'appName' => $this->config->appName(),
            'appUrl' => $this->config->appUrl(),
            'currentUser' => $this->guard->user(),
            'flashMessages' => $this->flashMessenger->pull(),
            'csrfToken' => fn (string $namespace = 'default'): string => $this->csrfTokenManager->token($namespace),
            'url' => fn (string $path = '/'): string => $this->urlGenerator->to($path),
        ];

        extract(array_merge($shared, $params), EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        if ($layoutFile === null) {
            return $content;
        }

        ob_start();
        require $layoutFile;

        return (string) ob_get_clean();
    }
}
