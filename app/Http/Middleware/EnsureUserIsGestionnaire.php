<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsGestionnaire
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->isGestionnaire()) {
            abort(403, 'Accès réservé aux gestionnaires.');
        }

        return $next($request);
    }
}
