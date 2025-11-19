<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Guardian;

class UnifiedLoginController extends Controller
{
    public function showLoginForm()
    {
        // Check if already authenticated and redirect to appropriate dashboard
        if (Auth::guard('admin')->check() || Auth::guard('co-admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.dashboard');
        }
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        if (Auth::guard('guardian')->check()) {
            return redirect()->route('guardian.dashboard');
        }

        return view('login-select');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Try admin guard first
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // Try co-admin guard
        if (Auth::guard('co-admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // Try teacher guard
        if (Auth::guard('teacher')->attempt($credentials, $request->filled('remember'))) {
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

                return redirect()->route('teacher.dashboard');
            }
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

                return redirect()->route('student.dashboard');
            }
            Auth::guard('student')->logout();
        }

        // Try guardian guard
        if (Auth::guard('guardian')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('guardian.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials. Please check your email and password.'])->withInput();
    }
}
