<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('teacher.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('teacher')->attempt($credentials, $request->filled('remember'))) {
            $teacher = Teacher::where('id', Auth::guard('teacher')->user()->user_pk_id)->first();

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
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('teacher')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Remove cached teacher details
        $request->session()->forget('teacher');
        return redirect('/teacher/login');
    }
}
