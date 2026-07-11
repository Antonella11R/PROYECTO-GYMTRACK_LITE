<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bootstrap;

use App\Catalog\Application\ExerciseService;
use App\Catalog\Infrastructure\PdoExerciseRepository;
use App\Catalog\Presentation\Web\Controllers\AdminExerciseController;
use App\Identity\Application\UserService;
use App\Identity\Infrastructure\PdoUserRepository;
use App\Identity\Presentation\Web\Controllers\AdminUserController;
use App\Identity\Presentation\Web\Controllers\AuthController;
use App\Reporting\Application\ProgressDatasetBuilder;
use App\Reporting\Application\ReportingService;
use App\Reporting\Infrastructure\PdoReportingReadModel;
use App\Reporting\Presentation\Web\Controllers\DashboardController;
use App\Reporting\Presentation\Web\Controllers\ReportController;
use App\Shared\Infrastructure\Config\AppConfig;
use App\Shared\Infrastructure\Database\PdoConnectionFactory;
use App\Shared\Infrastructure\Security\BcryptPasswordHasher;
use App\Shared\Infrastructure\Security\CsrfTokenManager;
use App\Shared\Presentation\Web\Http\Application;
use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;
use App\Shared\Presentation\Web\Middleware\AuthMiddleware;
use App\Shared\Presentation\Web\Middleware\GuestMiddleware;
use App\Shared\Presentation\Web\Middleware\RoleMiddleware;
use App\Shared\Presentation\Web\Routing\Router;
use App\Shared\Presentation\Web\Support\FlashMessenger;
use App\Shared\Presentation\Web\Support\SessionGuard;
use App\Shared\Presentation\Web\Support\UrlGenerator;
use App\Shared\Presentation\Web\View\ViewRenderer;
use App\Training\Application\TrainingService;
use App\Training\Infrastructure\PdoTrainingSessionRepository;
use App\Training\Presentation\Web\Controllers\AthleteController;
use App\Training\Presentation\Web\Controllers\TrainingController;
use Dotenv\Dotenv;

final class AppFactory
{
    public static function create(string $rootPath, string $envFile = '.env'): BootstrappedApp
    {
        self::loadEnvironment($rootPath, $envFile);

        $config = AppConfig::fromEnvironment();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name($config->sessionName());
            session_start();
        }

        $pdo = PdoConnectionFactory::make($config->databaseConfig());

        $userRepository = new PdoUserRepository($pdo);
        $exerciseRepository = new PdoExerciseRepository($pdo);
        $trainingRepository = new PdoTrainingSessionRepository($pdo);
        $reportingReadModel = new PdoReportingReadModel($pdo);
        $passwordHasher = new BcryptPasswordHasher();
        $csrfTokenManager = new CsrfTokenManager();
        $flashMessenger = new FlashMessenger();
        $guard = new SessionGuard($userRepository);
        $urlGenerator = new UrlGenerator($config);
        $viewRenderer = new ViewRenderer($rootPath, $config, $guard, $csrfTokenManager, $flashMessenger, $urlGenerator);

        $userService = new UserService($userRepository, $passwordHasher);
        $exerciseService = new ExerciseService($exerciseRepository);
        $trainingService = new TrainingService($trainingRepository, $userService, $exerciseRepository);
        $reportingService = new ReportingService(
            $reportingReadModel,
            $userService,
            $exerciseService,
            new ProgressDatasetBuilder()
        );

        $authController = new AuthController($viewRenderer, $userService, $guard, $flashMessenger, $csrfTokenManager, $urlGenerator);
        $dashboardController = new DashboardController($viewRenderer, $guard, $reportingService, $urlGenerator);
        $adminUserController = new AdminUserController($viewRenderer, $userService, $flashMessenger, $csrfTokenManager, $urlGenerator);
        $exerciseController = new AdminExerciseController($viewRenderer, $exerciseService, $flashMessenger, $csrfTokenManager, $urlGenerator);
        $athleteController = new AthleteController($viewRenderer, $guard, $userService);
        $trainingController = new TrainingController(
            $viewRenderer,
            $guard,
            $trainingService,
            $exerciseService,
            $flashMessenger,
            $csrfTokenManager,
            $urlGenerator
        );
        $reportController = new ReportController(
            $viewRenderer,
            $guard,
            $reportingService,
            $flashMessenger,
            $urlGenerator
        );

        $authMiddleware = new AuthMiddleware($guard, $flashMessenger, $urlGenerator);
        $guestMiddleware = new GuestMiddleware($guard, $urlGenerator);

        $router = new Router();

        $router->add('GET', '/', static fn (Request $request, array $params): Response => Response::redirect($urlGenerator->to('/dashboard')));
        $router->add('GET', '/login', [$authController, 'showLogin'], [$guestMiddleware]);
        $router->add('POST', '/login', [$authController, 'login'], [$guestMiddleware]);
        $router->add('POST', '/logout', [$authController, 'logout'], [$authMiddleware]);
        $router->add('GET', '/dashboard', [$dashboardController, 'index'], [$authMiddleware]);

        $adminMiddlewares = [$authMiddleware, new RoleMiddleware($guard, $flashMessenger, $urlGenerator, ['admin'])];
        $trainerMiddlewares = [$authMiddleware, new RoleMiddleware($guard, $flashMessenger, $urlGenerator, ['admin', 'trainer'])];

        $router->add('GET', '/admin/users', [$adminUserController, 'index'], $adminMiddlewares);
        $router->add('GET', '/admin/users/create', [$adminUserController, 'create'], $adminMiddlewares);
        $router->add('POST', '/admin/users', [$adminUserController, 'store'], $adminMiddlewares);
        $router->add('GET', '/admin/users/{id}/edit', [$adminUserController, 'edit'], $adminMiddlewares);
        $router->add('POST', '/admin/users/{id}', [$adminUserController, 'update'], $adminMiddlewares);
        $router->add('POST', '/admin/users/{id}/deactivate', [$adminUserController, 'deactivate'], $adminMiddlewares);

        $router->add('GET', '/admin/exercises', [$exerciseController, 'index'], $adminMiddlewares);
        $router->add('GET', '/admin/exercises/create', [$exerciseController, 'create'], $adminMiddlewares);
        $router->add('POST', '/admin/exercises', [$exerciseController, 'store'], $adminMiddlewares);
        $router->add('GET', '/admin/exercises/{id}/edit', [$exerciseController, 'edit'], $adminMiddlewares);
        $router->add('POST', '/admin/exercises/{id}', [$exerciseController, 'update'], $adminMiddlewares);
        $router->add('POST', '/admin/exercises/{id}/deactivate', [$exerciseController, 'deactivate'], $adminMiddlewares);

        $router->add('GET', '/trainer/athletes', [$athleteController, 'index'], $trainerMiddlewares);
        $router->add('GET', '/trainings', [$trainingController, 'index'], [$authMiddleware]);
        $router->add('GET', '/trainings/create', [$trainingController, 'create'], [$authMiddleware]);
        $router->add('POST', '/trainings', [$trainingController, 'store'], [$authMiddleware]);
        $router->add('GET', '/reports/progress', [$reportController, 'progress'], [$authMiddleware]);

        return new BootstrappedApp(
            new Application($router),
            $config,
            $csrfTokenManager,
            $guard
        );
    }

    private static function loadEnvironment(string $rootPath, string $envFile): void
    {
        foreach (['APP_ENV', 'APP_NAME', 'APP_URL', 'SESSION_NAME', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $key) {
            unset($_ENV[$key], $_SERVER[$key]);
            putenv($key);
        }

        $targetFile = $rootPath . DIRECTORY_SEPARATOR . ltrim($envFile, DIRECTORY_SEPARATOR);

        if (!is_file($targetFile)) {
            if (is_file($rootPath . DIRECTORY_SEPARATOR . '.env')) {
                Dotenv::createImmutable($rootPath)->safeLoad();
            }

            return;
        }

        Dotenv::createImmutable($rootPath, basename($envFile))->safeLoad();
    }
}
