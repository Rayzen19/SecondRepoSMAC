<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Guardian;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        // If already authenticated as teacher, send to teacher dashboard
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // Try admin first
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }

        // Try teacher guard next
        if (Auth::guard('teacher')->attempt($credentials, $request->filled('remember'))) {
            // Ensure teacher model exists and cache some teacher info in session (same as teacher login flow)
            $teacherUser = Auth::guard('teacher')->user();
            $teacher = null;
            if ($teacherUser && $teacherUser->user_pk_id) {
                $teacher = Teacher::where('id', $teacherUser->user_pk_id)->first();
            }

            if ($teacher) {
                $request->session()->regenerate();

                $request->session()->put('teacher', [
                    'id' => $teacher->id,
                    'employee_number' => $teacher->employee_number,
                    'first_name' => $teacher->first_name,
                    'middle_name' => $teacher->middle_name,
                    'last_name' => $teacher->last_name,
                    'suffix' => $teacher->suffix,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'phone' => $teacher->phone,
                    'department' => $teacher->department,
                    'specialization' => $teacher->specialization,
                    'term' => $teacher->term,
                    'status' => $teacher->status,
                    'profile_picture' => $teacher->profile_picture,
                ]);

                return redirect()->intended('/teacher');
            }
            // If teacher mapping missing, log out the guard and fall through to invalid
            Auth::guard('teacher')->logout();
        }

        // Try student guard
        if (Auth::guard('student')->attempt($credentials, $request->filled('remember'))) {
            $studentUser = Auth::guard('student')->user();
            $student = null;
            if ($studentUser && $studentUser->user_pk_id) {
                $student = Student::where('id', $studentUser->user_pk_id)->first();
            }

            if ($student) {
                $request->session()->regenerate();

                $request->session()->put('student', [
                    'id' => $student->id,
                    'student_number' => $student->student_number,
                    'first_name' => $student->first_name,   
                    'middle_name' => $student->middle_name,
                    'last_name' => $student->last_name,
                    'suffix' => $student->suffix,
                    'name' => $student->name,
                    'email' => $student->email,
                    'mobile_number' => $student->mobile_number,
                    'address' => $student->address,
                    'gender' => $student->gender,
                    'birthdate' => $student->birthdate,
                    'status' => $student->status,
                    'profile_picture' => $student->profile_picture,
                    'contact' => $student->contact ?? null,
                    'guardian_name' => $student->guardian_name ?? null,
                    'guardian_contact' => $student->guardian_contact ?? null,
                    'guardian_email' => $student->guardian_email ?? null,
                    'program' => $student->program ?? null,
                    'academic_year' => $student->academic_year ?? null,
                    'academic_year_id' => $student->academic_year_id ?? null,
                ]);

                return redirect()->intended('/student');
            }

            Auth::guard('student')->logout();
        }

        // Try guardian guard
        if (Auth::guard('guardian')->attempt($credentials, $request->filled('remember'))) {
            // Guardian login doesn't cache a detailed session currently; just regenerate and redirect.
            $request->session()->regenerate();
            return redirect()->intended('/guardian');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        // Logout whichever guard is currently authenticated (admin or teacher)
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        if (Auth::guard('teacher')->check()) {
            Auth::guard('teacher')->logout();
            // Remove cached teacher details
            $request->session()->forget('teacher');
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}
