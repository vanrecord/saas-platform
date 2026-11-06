<?php

namespace App\Http\Middleware;

use Closure;

class Wholesale
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
