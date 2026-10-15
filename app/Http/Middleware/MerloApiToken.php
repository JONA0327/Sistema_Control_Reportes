<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MerloApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = (string) config('services.merlo.token');

        abort_unless($token !== '' && hash_equals($token, (string) $request->bearerToken()), 401);

        return $next($request);
    }
}
