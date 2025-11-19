<?php

/**
 * Diagnose Message Issue
 * Helps identify the root cause of the email mismatch
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Message System Diagnostic ===\n\n";

// 1. Check all Barrogo users
echo "1. USER ACCOUNTS:\n";
echo str_repeat("-", 80) . "\n";
$users = DB::table('users')
    ->where('email', 'LIKE', '%barrogo%')
    ->orWhere('name', 'LIKE', '%Barrogo%')
    ->get();

foreach ($users as $user) {
    echo "User ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Type: {$user->type}\n";
    echo "  PK ID: {$user->user_pk_id}\n";
    echo "\n";
}

// 2. Check teacher record
echo "2. TEACHER RECORD:\n";
echo str_repeat("-", 80) . "\n";
$teacher = DB::table('teachers')
    ->where('first_name', 'John Raymond')
    ->where('last_name', 'Barrogo')
    ->first();

if ($teacher) {
    echo "Teacher ID: {$teacher->id}\n";
    echo "  Name: {$teacher->first_name} {$teacher->last_name}\n";
    echo "  Email: {$teacher->email}\n";
    echo "  Employee #: {$teacher->employee_number}\n";
    echo "\n";
}

// 3. Check student record
echo "3. STUDENT RECORD:\n";
echo str_repeat("-", 80) . "\n";
$student = DB::table('students')
    ->where('first_name', 'John Raymond')
    ->where('last_name', 'Barrogo')
    ->first();

if ($student) {
    echo "Student ID: {$student->id}\n";
    echo "  Name: {$student->first_name} {$student->last_name}\n";
    echo "  Email: {$student->email}\n";
    echo "  Student #: {$student->student_number}\n";
    echo "\n";
}

// 4. Check recent messages
echo "4. RECENT MESSAGES:\n";
echo str_repeat("-", 80) . "\n";
$messages = DB::table('messages')
    ->whereIn('sender_id', [5, 26])
    ->orderByDesc('created_at')
    ->limit(5)
    ->get();

foreach ($messages as $msg) {
    $sender = DB::table('users')->where('id', $msg->sender_id)->first();
    $recipients = DB::table('message_recipients')
        ->join('users', 'message_recipients.recipient_id', '=', 'users.id')
        ->where('message_id', $msg->id)
        ->get();
    
    echo "Message ID: {$msg->id}\n";
    echo "  From: {$sender->name} ({$sender->email})\n";
    echo "  Body: " . substr($msg->body, 0, 50) . "...\n";
    echo "  To:\n";
    foreach ($recipients as $r) {
        echo "    - {$r->name} ({$r->email})\n";
    }
    echo "  Sent: {$msg->created_at}\n";
    echo "\n";
}

// 5. Analysis
echo "5. ANALYSIS:\n";
echo str_repeat("-", 80) . "\n";

if ($teacher && $student) {
    echo "⚠ SITUATION: Two people with same name\n";
    echo "  - Teacher: {$teacher->email}\n";
    echo "  - Student: {$student->email}\n";
    echo "\n";
    echo "RECOMMENDED FIX:\n";
    echo "  The teacher account should use ONE consistent email address.\n";
    echo "  Currently the teacher record uses: {$teacher->email}\n";
    echo "\n";
    echo "  If teacher logs in with: johnraymondbarrogo08@gmail.com\n";
    echo "  Then update teacher record to match that email.\n";
    echo "\n";
    echo "  Run: php fix_teacher_email.php\n";
}

echo "\n=== End of Diagnostic ===\n";
