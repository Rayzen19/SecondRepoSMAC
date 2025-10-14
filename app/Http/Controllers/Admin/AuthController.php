<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    public function showForgotPassword()
    {
        return view('admin.auth.forgot');
    }

    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // Generate 6-digit OTP
        $code = (string) random_int(100000, 999999);

        DB::table('password_otps')->insert([
            'email' => $data['email'],
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // For development, log the OTP so testers can retrieve it
        Log::info('Password reset OTP', ['email' => $data['email'], 'otp' => $code]);

        return redirect()->route('admin.auth.resetForm')->with('status', 'An OTP has been sent to your email. ['.  $code . ']');
    }

    public function showResetPassword()
    {
        return view('admin.auth.reset');
    }

    public function resetWithOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        // Fetch the latest unused & unexpired OTP for the email
        $otpRow = DB::table('password_otps')
            ->where('email', $validated['email'])
            ->whereNull('used_at')
            ->where('expires_at', '>=', now())
            ->orderByDesc('id')
            ->first();

        if (!$otpRow || !Hash::check($validated['otp'], $otpRow->code_hash)) {
            return back()
                ->withErrors(['otp' => 'Invalid or expired OTP.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        // Update password
        /** @var User $user */
        $user = User::where('email', $validated['email'])->firstOrFail();
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Mark OTP as used
        DB::table('password_otps')->where('id', $otpRow->id)->update([
            'used_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.auth.loginForm')->with('status', 'Password has been reset. You can now log in.');
    }
}
