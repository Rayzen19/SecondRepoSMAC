<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Testing email configuration...\n";
echo "Mail driver: " . config('mail.default') . "\n";
echo "Mail host: " . config('mail.mailers.smtp.host') . "\n";
echo "Mail port: " . config('mail.mailers.smtp.port') . "\n";
echo "Mail username: " . config('mail.mailers.smtp.username') . "\n";
echo "Mail from: " . config('mail.from.address') . "\n\n";

try {
    Mail::raw('This is a test email from SMS system.', function ($message) {
        $message->to('johnraymondbarrogo08@gmail.com')
                ->subject('Test Email from SMS');
    });
    
    echo "✓ Email sent successfully!\n";
    echo "Check your inbox at: johnraymondbarrogo08@gmail.com\n";
} catch (Exception $e) {
    echo "✗ Error sending email:\n";
    echo $e->getMessage() . "\n";
}
