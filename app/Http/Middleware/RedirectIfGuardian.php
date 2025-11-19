<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfGuardian
{
    /**
     * Handle an incoming request.
     *
     * Only check if the user is authenticated as a guardian, not other guards.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only redirect if logged in specifically as guardian
        if (Auth::guard('guardian')->check()) {
            return redirect()->route('guardian.dashboard');
        }

        return $next($request);
    }
}
