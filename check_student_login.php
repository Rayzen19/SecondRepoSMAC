<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Student Login Accounts\n";
echo "================================\n\n";

// Find all student users
$studentUsers = App\Models\User::where('type', 'student')->get();

echo "Found {$studentUsers->count()} student user(s):\n\n";

foreach ($studentUsers as $user) {
    echo "User ID: {$user->id}\n";
    echo "  Email: {$user->email}\n";
    echo "  Name: {$user->name}\n";
    echo "  Type: {$user->type}\n";
    echo "  user_pk_id: {$user->user_pk_id}\n";
    
    // Check if linked student exists
    if ($user->user_pk_id) {
        $student = App\Models\Student::find($user->user_pk_id);
        if ($student) {
            echo "  ✅ Linked to student: {$student->first_name} {$student->last_name}\n";
            echo "     Student No: {$student->student_number}\n";
            echo "     Student ID: {$student->id}\n";
            
            // Check subject enrollments
            $activeYear = App\Models\AcademicYear::where('is_active', true)->first();
            if ($activeYear) {
                $subjectCount = App\Models\SubjectEnrollment::whereHas('studentEnrollment', function ($q) use ($student, $activeYear) {
                    $q->where('student_id', $student->id)
                      ->where('academic_year_id', $activeYear->id);
                })->count();
                
                echo "     Subject Enrollments: {$subjectCount}\n";
            }
        } else {
            echo "  ❌ Linked student not found!\n";
        }
    } else {
        echo "  ℹ️  No student link (user_pk_id is NULL)\n";
    }
    
    echo "\n";
}

// Check for John Raymond specifically
echo "\nLooking for John Raymond Barrogo:\n";
echo "==================================\n";

$johnStudent = App\Models\Student::where('student_number', '2025-00021')->first();
if ($johnStudent) {
    echo "✅ Student found: ID {$johnStudent->id}\n";
    echo "   Name: {$johnStudent->first_name} {$johnStudent->last_name}\n";
    echo "   Student No: {$johnStudent->student_number}\n\n";
    
    // Find associated user
    $johnUser = App\Models\User::where('type', 'student')
        ->where('user_pk_id', $johnStudent->id)
        ->first();
    
    if ($johnUser) {
        echo "✅ Login account exists:\n";
        echo "   User ID: {$johnUser->id}\n";
        echo "   Email: {$johnUser->email}\n";
        echo "   Password: (use this email to login)\n";
    } else {
        echo "❌ NO LOGIN ACCOUNT FOUND!\n";
        echo "   Creating login account...\n\n";
        
        // Create login account
        $newUser = App\Models\User::create([
            'name' => "{$johnStudent->first_name} {$johnStudent->last_name}",
            'email' => $johnStudent->email ?? "student{$johnStudent->student_number}@school.test",
            'password' => bcrypt('password'), // default password
            'type' => 'student',
            'user_pk_id' => $johnStudent->id,
        ]);
        
        echo "✅ Created login account:\n";
        echo "   Email: {$newUser->email}\n";
        echo "   Password: password (default)\n";
    }
}
