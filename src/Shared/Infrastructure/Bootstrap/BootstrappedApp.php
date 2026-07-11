<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bootstrap;

use App\Shared\Infrastructure\Config\AppConfig;
use App\Shared\Infrastructure\Security\CsrfTokenManager;
use App\Shared\Presentation\Web\Http\Application;
use App\Shared\Presentation\Web\Support\SessionGuard;

final class BootstrappedApp
{
    public function __construct(
        private Application $application,
        private AppConfig $config,
        private CsrfTokenManager $csrfTokenManager,
        private SessionGuard $guard
    ) {
    }

    public function application(): Application
    {
        return $this->application;
    }

    public function config(): AppConfig
    {
        return $this->config;
    }

    public function csrf(): CsrfTokenManager
    {
        return $this->csrfTokenManager;
    }

    public function guard(): SessionGuard
    {
        return $this->guard;
    }
}
