<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        // Rate limiting: max 3 attempts per 10 minutes per email
        $email = $request->input('email');
        if ($email) {
            $recentAttempts = DB::table('password_otps')
                ->where('email', $email)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->count();
                
            if ($recentAttempts >= 3) {
                return back()
                    ->withErrors(['email' => 'Too many OTP requests. Please wait 10 minutes before trying again.'])
                    ->withInput();
            }
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // Get the user to send personalized email
        $user = User::where('email', $data['email'])->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.'])->withInput();
        }

        // Generate 6-digit OTP
        $code = (string) random_int(100000, 999999);

        // Delete any old unused OTPs for this email
        DB::table('password_otps')
            ->where('email', $data['email'])
            ->whereNull('used_at')
            ->delete();

        // Store new OTP
        DB::table('password_otps')->insert([
            'email' => $data['email'],
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email with OTP
        try {
            Mail::to($data['email'])->send(
                new PasswordResetOtp(
                    $user->name ?? 'User',
                    $code,
                    $user->type ?? 'User'
                )
            );

            // For development, also log the OTP
            Log::info('Password reset OTP sent', [
                'email' => $data['email'],
                'otp' => $code,
                'name' => $user->name,
                'type' => $user->type
            ]);

            return redirect()
                ->route('admin.auth.resetForm')
                ->with('status', 'An OTP has been sent to your email address. Please check your inbox and spam folder.')
                ->with('email', $data['email']);
                
        } catch (\Exception $e) {
            Log::error('Failed to send password reset OTP email', [
                'email' => $data['email'],
                'error' => $e->getMessage(),
                'otp' => $code // Include OTP in log for development
            ]);

            // Still redirect to reset form but show the OTP in development mode
            if (config('app.debug')) {
                return redirect()
                    ->route('admin.auth.resetForm')
                    ->with('status', 'Email service temporarily unavailable. For testing, use this OTP: ' . $code)
                    ->with('email', $data['email']);
            }

            return back()->withErrors(['email' => 'Failed to send email. Please try again later or contact support.'])->withInput();
        }
    }

    public function showResetPassword()
    {
        return view('admin.auth.reset')->with('email', session('email', ''));
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

        if (!$otpRow) {
            return back()
                ->withErrors(['otp' => 'OTP has expired or does not exist. Please request a new one.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        if (!Hash::check($validated['otp'], $otpRow->code_hash)) {
            return back()
                ->withErrors(['otp' => 'Invalid OTP code. Please check and try again.'])
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

        // Log the password reset
        Log::info('Password reset successful', [
            'email' => $validated['email'],
            'user_id' => $user->id,
            'user_type' => $user->type
        ]);

        return redirect()
            ->route('admin.auth.loginForm')
            ->with('status', 'Password has been reset successfully. You can now log in with your new password.');
    }
}
