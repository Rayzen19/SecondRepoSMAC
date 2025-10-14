<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Mail\TeacherWelcome;
use Illuminate\Support\Facades\Mail;

echo "Testing Teacher Email Configuration\n";
echo "====================================\n\n";

// Test email details
$testTeacherName = "Test Teacher";
$testEmail = "johnraymondbarrogo08@gmail.com"; // Using the configured email
$testPassword = "TestPassword123";

echo "Mail Configuration:\n";
echo "  Mailer: " . config('mail.default') . "\n";
echo "  Host: " . config('mail.mailers.smtp.host') . "\n";
echo "  Port: " . config('mail.mailers.smtp.port') . "\n";
echo "  From: " . config('mail.from.address') . "\n";
echo "  From Name: " . config('mail.from.name') . "\n\n";

echo "Sending test email to: {$testEmail}\n";

try {
    Mail::to($testEmail)->send(new TeacherWelcome($testTeacherName, $testEmail, $testPassword));
    echo "✓ Email sent successfully!\n";
    echo "\nPlease check the inbox of {$testEmail}\n";
} catch (\Exception $e) {
    echo "✗ Email failed to send!\n";
    echo "Error: " . $e->getMessage() . "\n";
}
