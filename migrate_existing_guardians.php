<?php

/**
 * Script to migrate existing student guardian data to the guardians table
 * Run this once to create guardian records for existing students
 * 
 * Usage: php migrate_existing_guardians.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\Guardian;
use App\Models\Auth\GuardianUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

echo "Starting guardian migration...\n\n";

$students = Student::whereNotNull('guardian_email')
    ->where('guardian_email', '!=', '')
    ->get();

$totalStudents = $students->count();
$createdGuardians = 0;
$createdGuardianUsers = 0;
$linkedStudents = 0;
$errors = 0;

echo "Found {$totalStudents} students with guardian information\n\n";

foreach ($students as $index => $student) {
    echo "[" . ($index + 1) . "/{$totalStudents}] Processing: {$student->name} (#{$student->student_number})...\n";
    
    try {
        DB::beginTransaction();

        // Check if student already has a guardian linked
        if ($student->guardians()->exists()) {
            echo "  ✓ Student already has guardian linked\n";
            DB::commit();
            continue;
        }

        // Check if guardian exists by email
        $guardian = Guardian::where('email', $student->guardian_email)->first();

        if (!$guardian) {
            // Parse guardian name
            $guardianName = $student->guardian_name ?? 'Guardian';
            $nameParts = parseGuardianName($guardianName);

            // Generate guardian number
            $year = now()->year;
            $lastGuardianNumber = Guardian::withTrashed()
                ->where('guardian_number', 'like', 'GRD-' . $year . '-%')
                ->orderByDesc('guardian_number')
                ->value('guardian_number');

            if ($lastGuardianNumber) {
                $parts = explode('-', $lastGuardianNumber);
                $lastSeq = isset($parts[2]) ? (int) $parts[2] : 0;
            } else {
                $lastSeq = 0;
            }

            $guardianNumber = 'GRD-' . $year . '-' . str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);

            // Create guardian
            $guardian = Guardian::create([
                'guardian_number' => $guardianNumber,
                'first_name' => $nameParts['first_name'],
                'middle_name' => $nameParts['middle_name'],
                'last_name' => $nameParts['last_name'],
                'suffix' => $nameParts['suffix'],
                'gender' => 'male', // Default
                'email' => $student->guardian_email,
                'mobile_number' => $student->guardian_contact,
                'address' => $student->address ?? null,
                'status' => 'active',
            ]);

            $createdGuardians++;
            echo "  ✓ Created guardian: {$guardian->name} ({$guardianNumber})\n";
        } else {
            echo "  ✓ Found existing guardian: {$guardian->name} ({$guardian->guardian_number})\n";
        }

        // Link guardian to student
        if (!$student->guardians()->where('guardian_id', $guardian->id)->exists()) {
            $student->guardians()->attach($guardian->id);
            $linkedStudents++;
            echo "  ✓ Linked guardian to student\n";
        }

        // Create guardian user account if not exists
        $guardianUser = DB::table('users')
            ->where('email', $guardian->email)
            ->where('type', 'guardian')
            ->first();

        if (!$guardianUser) {
            $guardianPassword = Str::password(12, symbols: true);
            
            GuardianUser::query()->withoutGlobalScopes()->create([
                'name' => $guardian->name,
                'email' => $guardian->email,
                'password' => Hash::make($guardianPassword),
                'type' => 'guardian',
                'user_pk_id' => $guardian->id,
            ]);

            // Store encrypted password
            $guardian->forceFill([
                'generated_password_encrypted' => Crypt::encryptString($guardianPassword),
            ])->save();

            $createdGuardianUsers++;
            echo "  ✓ Created guardian user account\n";
            echo "  ℹ Guardian Password: {$guardianPassword}\n";
        } else {
            echo "  ✓ Guardian user account already exists\n";
        }

        DB::commit();
        echo "  ✓ Success!\n\n";

    } catch (\Throwable $e) {
        DB::rollBack();
        $errors++;
        echo "  ✗ Error: {$e->getMessage()}\n\n";
        Log::error('Guardian migration error', [
            'student_id' => $student->id,
            'error' => $e->getMessage(),
        ]);
    }
}

echo "\n==========================================\n";
echo "Migration Complete!\n";
echo "==========================================\n";
echo "Total students processed: {$totalStudents}\n";
echo "Guardians created: {$createdGuardians}\n";
echo "Guardian user accounts created: {$createdGuardianUsers}\n";
echo "Students linked: {$linkedStudents}\n";
echo "Errors: {$errors}\n";
echo "==========================================\n\n";

/**
 * Parse guardian full name into components
 */
function parseGuardianName(string $fullName): array
{
    $parts = explode(' ', trim($fullName));
    $suffix = null;
    
    // Check for common suffixes
    $suffixes = ['Jr.', 'Jr', 'Sr.', 'Sr', 'II', 'III', 'IV', 'V'];
    $lastPart = end($parts);
    if (in_array($lastPart, $suffixes)) {
        $suffix = array_pop($parts);
    }

    $firstName = '';
    $middleName = null;
    $lastName = '';

    if (count($parts) === 1) {
        // Only one name, use as first name
        $firstName = $parts[0];
        $lastName = $parts[0];
    } elseif (count($parts) === 2) {
        // Two names: first and last
        $firstName = $parts[0];
        $lastName = $parts[1];
    } else {
        // Three or more names: first, middle(s), last
        $firstName = array_shift($parts);
        $lastName = array_pop($parts);
        $middleName = implode(' ', $parts);
    }

    return [
        'first_name' => $firstName,
        'middle_name' => $middleName,
        'last_name' => $lastName,
        'suffix' => $suffix,
    ];
}
