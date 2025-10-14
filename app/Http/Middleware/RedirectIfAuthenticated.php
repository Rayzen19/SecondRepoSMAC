<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return match ($guard) {
                    'admin' => redirect()->route('admin.dashboard'),
                    'teacher' => redirect()->route('teacher.dashboard'),
                    'student' => redirect()->route('student.dashboard'),
                    'guardian' => redirect()->route('guardian.dashboard'),
                    default => redirect('/'),
                };
            }
        }

        return $next($request);
    }
}
