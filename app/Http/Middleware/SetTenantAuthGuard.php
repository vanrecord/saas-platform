<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantAuthGuard
{
    /**
     * Set the active authentication guard to 'tenant' during tenant requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        auth()->shouldUse('tenant');

        return $next($request);
    }
}

