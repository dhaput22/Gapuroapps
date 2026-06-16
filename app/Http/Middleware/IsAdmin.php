<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || $user->role !== User::ROLE_ADMIN) {
            abort(403, 'Akses hanya untuk admin.');
        }

        return $next($request);
    }
}
