<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;

class CheckTeacherStatus
{
    /**
     * Handle an incoming request.
     * 
     * Inactive teachers can only view their profile details.
     * All other actions are restricted.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.auth.loginForm');
        }

        $user = Auth::guard('teacher')->user();
        $teacher = Teacher::find($user->user_pk_id);

        // If teacher not found or status is not active
        if (!$teacher || $teacher->status !== 'active') {
            // Allow access only to profile viewing routes
            $allowedRoutes = [
                'teacher.profile.show',
                'teacher.profile.edit',
                'teacher.profile.password.edit',
                'teacher.auth.logout',
            ];

            // Check if the current route is in the allowed list
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()
                    ->route('teacher.profile.show')
                    ->with('warning', 'Your account is currently ' . ($teacher ? $teacher->status : 'inactive') . '. You can only view your profile details. Please contact the administrator for assistance.');
            }
        }

        return $next($request);
    }
}
