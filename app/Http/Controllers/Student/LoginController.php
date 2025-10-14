<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('student.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('student')->attempt($credentials, $request->filled('remember'))) {
            $student = Student::where('id', Auth::guard('student')->user()->user_pk_id)->first();

            if ($student) {
                $request->session()->regenerate();

                $request->session()->put('student', [
                    'id' => $student->id,
                    'student_number' => $student->student_number,
                    'first_name' => $student->first_name,
                    'middle_name' => $student->middle_name,
                    'last_name' => $student->last_name,
                    'suffix' => $student->suffix,
                    'name' => $student->name, // accessor: "Last, First"
                    'email' => $student->email,
                    'mobile_number' => $student->mobile_number,
                    'address' => $student->address,
                    'gender' => $student->gender,
                    'birthdate' => $student->birthdate,
                    'status' => $student->status,
                    'profile_picture' => $student->profile_picture,
                    'contact' => $student->contact, // accessor: mobile | email
                    'guardian_name' => $student->guardian_name,
                    'guardian_contact' => $student->guardian_contact,
                    'guardian_email' => $student->guardian_email,
                    'program' => $student->program,
                    'academic_year' => $student->academic_year,
                    'academic_year_id' => $student->academic_year_id,
                ]);

                return redirect()->intended('/student');
            }
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->forget('student');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // After student logs out, redirect to the Admin login page
        return redirect()->route('admin.auth.loginForm');
    }
}
