<?php

declare(strict_types=1);

use App\Shared\Presentation\Web\Http\Request;

$bootstrappedApp = require __DIR__ . '/bootstrap.php';
$response = $bootstrappedApp->application()->handle(Request::capture());
$response->send();
