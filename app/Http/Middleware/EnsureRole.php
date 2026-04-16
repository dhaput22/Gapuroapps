<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Anda harus login untuk mengakses fitur ini.');
        }

        if ($roles !== [] && !in_array((string) $user->role, $roles, true)) {
            abort(403, 'Role Anda tidak memiliki akses ke fitur ini.');
        }

        return $next($request);
    }
}
