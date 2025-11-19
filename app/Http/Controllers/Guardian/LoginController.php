<?php
namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // If already logged in as guardian, redirect to dashboard
        if (Auth::guard('guardian')->check()) {
            return redirect()->route('guardian.dashboard');
        }
        
        return view('guardian.auth.login');
    }

    public function login(Request $request)
    {
        // If already logged in as guardian, redirect to dashboard
        if (Auth::guard('guardian')->check()) {
            return redirect()->route('guardian.dashboard');
        }
        
        $credentials = $request->only('email', 'password');
        if (Auth::guard('guardian')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/guardian');
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('guardian')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/guardian/login');
    }
}
