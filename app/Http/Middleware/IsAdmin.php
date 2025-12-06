<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || $user->role !== 'admin') {
            // jika bukan admin, arahkan ke halaman dashboard atau 403
            abort(403, 'Akses hanya untuk admin.');
        }

        return $next($request);
    }
}
