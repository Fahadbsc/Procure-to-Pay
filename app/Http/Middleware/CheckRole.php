<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !$user->roles()->whereIn('name', $roles)->exists()) {
            abort(403, 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
