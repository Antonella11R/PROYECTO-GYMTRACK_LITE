<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Web\Middleware;

use App\Shared\Presentation\Web\Http\Request;
use App\Shared\Presentation\Web\Http\Response;

interface Middleware
{
    public function process(Request $request, callable $next): Response;
}
