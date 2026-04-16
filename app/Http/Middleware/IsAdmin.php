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

        if (! $user || $user->role !== User::ROLE_SUPER_ADMIN) {
            abort(403, 'Akses hanya untuk super admin.');
        }

        return $next($request);
    }
}
