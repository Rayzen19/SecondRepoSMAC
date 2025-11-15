<?php
// Test email sending for password reset
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Mail\PasswordResetOtp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "=== Email Configuration Test ===\n\n";

echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n";

echo "\n=== Sending Test OTP Email ===\n";

$testEmail = 'tamayomoore@gmail.com';
$testOTP = '123456';

try {
    Mail::to($testEmail)->send(
        new PasswordResetOtp('Test User', $testOTP, 'Admin')
    );
    
    echo "✓ Email sent successfully to {$testEmail}\n";
    echo "  OTP Code: {$testOTP}\n";
    echo "\nCheck the email inbox (and spam folder) for the OTP.\n";
    
} catch (\Exception $e) {
    echo "✗ Failed to send email\n";
    echo "  Error: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Troubleshooting:\n";
    echo "1. Make sure MAIL_PASSWORD in .env is a Google App Password (not regular password)\n";
    echo "2. Enable 'Less secure app access' or use App Password for Gmail\n";
    echo "3. Check if port 587 is not blocked by firewall\n";
}
