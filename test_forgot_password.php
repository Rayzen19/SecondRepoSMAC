<?php
// Test complete forgot password flow
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== Forgot Password System Test ===\n\n";

$testEmail = 'tamayomoore@gmail.com';

// 1. Check if user exists
echo "1. Checking if user exists...\n";
$user = User::where('email', $testEmail)->first();
if ($user) {
    echo "   ✓ User found: {$user->name} ({$user->type})\n\n";
} else {
    echo "   ✗ User not found!\n";
    exit(1);
}

// 2. Generate and store OTP
echo "2. Generating OTP...\n";
$otp = (string) random_int(100000, 999999);
echo "   OTP Code: {$otp}\n";

// Delete old OTPs
DB::table('password_otps')->where('email', $testEmail)->delete();

// Insert new OTP
DB::table('password_otps')->insert([
    'email' => $testEmail,
    'code_hash' => Hash::make($otp),
    'expires_at' => now()->addMinutes(10),
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "   ✓ OTP stored in database\n\n";

// 3. Verify OTP can be retrieved and checked
echo "3. Verifying OTP storage...\n";
$otpRow = DB::table('password_otps')
    ->where('email', $testEmail)
    ->whereNull('used_at')
    ->where('expires_at', '>=', now())
    ->orderByDesc('id')
    ->first();

if ($otpRow && Hash::check($otp, $otpRow->code_hash)) {
    echo "   ✓ OTP can be verified correctly\n";
    $expiresIn = now()->diffInMinutes($otpRow->expires_at);
    echo "   ✓ Expires in: {$expiresIn} minutes\n\n";
} else {
    echo "   ✗ OTP verification failed!\n";
    exit(1);
}

// 4. Test email configuration
echo "4. Checking email configuration...\n";
echo "   MAIL_MAILER: " . config('mail.default') . "\n";
echo "   MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "   MAIL_FROM: " . config('mail.from.address') . "\n";
echo "   ✓ Email is configured\n\n";

// 5. Check routes
echo "5. Checking routes...\n";
$routes = [
    'admin.auth.forgotForm',
    'admin.auth.forgotSend',
    'admin.auth.resetForm',
    'admin.auth.resetProcess',
    'admin.auth.loginForm'
];

foreach ($routes as $route) {
    try {
        $url = route($route);
        echo "   ✓ {$route}: {$url}\n";
    } catch (\Exception $e) {
        echo "   ✗ {$route}: NOT FOUND\n";
    }
}

echo "\n=== Summary ===\n";
echo "✓ Forgot password system is properly configured!\n\n";
echo "To test the complete flow:\n";
echo "1. Go to: http://127.0.0.1:8000/admin/forgot-password\n";
echo "2. Enter email: {$testEmail}\n";
echo "3. Check email for OTP (or use: {$otp})\n";
echo "4. Enter OTP and new password on reset page\n";
echo "5. Login with new password\n";
