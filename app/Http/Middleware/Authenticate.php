<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson()) {
            return null; // Let the framework return 401 JSON
        }

        $prefix = $request->route()?->getPrefix() ?? '';
        $path = $request->path();

        $isAdmin = str_starts_with($prefix, '/admin') || str_starts_with($path, 'admin');
        if ($isAdmin) return route('admin.auth.loginForm');

        $isTeacher = str_starts_with($prefix, '/teacher') || str_starts_with($path, 'teacher');
        if ($isTeacher) return route('teacher.auth.loginForm');

        $isStudent = str_starts_with($prefix, '/student') || str_starts_with($path, 'student');
        if ($isStudent) return route('student.auth.loginForm');

        $isGuardian = str_starts_with($prefix, '/guardian') || str_starts_with($path, 'guardian');
        if ($isGuardian) return route('guardian.auth.loginForm');

        // Fallback: home or a generic login path
        return '/';
    }
}
