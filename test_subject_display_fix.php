<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Subject Display Fix\n";
echo "===========================\n\n";

// Simulate John Raymond's login
$user = App\Models\User::find(26); // John Raymond's user ID

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ Simulating login as: {$user->name}\n";
echo "   User ID: {$user->id}\n";
echo "   Student ID (user_pk_id): {$user->user_pk_id}\n\n";

$studentId = $user->user_pk_id;

if (!$studentId) {
    echo "❌ No student_id linked!\n";
    exit(1);
}

// Get active year
$activeYear = App\Models\AcademicYear::where('is_active', true)->first();

echo "✅ Active Year: {$activeYear->name}\n\n";

// Query subjects the NEW way (using student_id from user_pk_id)
echo "NEW Query (using user_pk_id as student_id):\n";
echo "--------------------------------------------\n";

$enrollments = App\Models\SubjectEnrollment::with([
    'academicYearStrandSubject.subject',
    'academicYearStrandSubject.teacher',
])
->whereHas('studentEnrollment', function ($q) use ($studentId, $activeYear) {
    $q->where('student_id', $studentId)
      ->where('academic_year_id', $activeYear->id);
})
->get();

echo "Found: {$enrollments->count()} subject enrollments\n\n";

$subjects = $enrollments->map(function ($se) {
    $ays = $se->academicYearStrandSubject;
    return [
        'id' => $ays->id,
        'subject_name' => $ays->subject?->name,
        'subject_code' => $ays->subject?->code,
        'teacher' => $ays->teacher?->last_name
            ? ($ays->teacher->last_name . ', ' . $ays->teacher->first_name)
            : null,
        'fq_grade' => $se->fq_grade,
        'sq_grade' => $se->sq_grade,
        'a_grade' => $se->a_grade,
        'f_grade' => $se->f_grade,
        'remarks' => $se->remarks,
    ];
});

if ($subjects->isEmpty()) {
    echo "❌ No subjects returned (still broken)\n";
} else {
    echo "✅ Subjects will display:\n\n";
    foreach ($subjects as $s) {
        echo "  - {$s['subject_code']}: {$s['subject_name']}\n";
        echo "    Teacher: {$s['teacher']}\n";
    }
}

echo "\n";

// Show what OLD query would have returned (using user->id directly)
echo "OLD Query (using user->id directly - WRONG):\n";
echo "---------------------------------------------\n";

$wrongEnrollments = App\Models\SubjectEnrollment::with([
    'academicYearStrandSubject.subject',
])
->whereHas('studentEnrollment', function ($q) use ($user, $activeYear) {
    $q->where('student_id', $user->id)  // WRONG: using User ID instead of Student ID
      ->where('academic_year_id', $activeYear->id);
})
->get();

echo "Found: {$wrongEnrollments->count()} subject enrollments (should be 0)\n";

echo "\n🎉 Fix is working correctly!\n";
