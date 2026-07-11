<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Bootstrap\AppFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

return AppFactory::create(dirname(__DIR__));
