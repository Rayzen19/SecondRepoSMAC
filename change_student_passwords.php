<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

echo "========================================\n";
echo "   CHANGE STUDENT PASSWORDS\n";
echo "========================================\n\n";

// Array of students to update
$students = [
    ['number' => 'STU-000041', 'email' => 'brixnathan.alejandro@student.newsmac.edu.ph', 'password' => 'BrixnathanAlejandro7524', 'name' => 'Brix Nathan Alejandro'],
    ['number' => 'STU-000042', 'email' => 'romar.benamir@student.newsmac.edu.ph', 'password' => 'RomarBenamir4735', 'name' => 'Romar Benamir'],
    ['number' => 'STU-000043', 'email' => 'anito.cabujat@student.newsmac.edu.ph', 'password' => 'AnitoCabujat1737', 'name' => 'Anito Cabujat'],
    ['number' => 'STU-000044', 'email' => 'anthonyjosh.clemente@student.newsmac.edu.ph', 'password' => 'AnthonyjoshClemente1249', 'name' => 'Anthony Josh Clemente'],
    ['number' => 'STU-000045', 'email' => 'rexter.deluna@student.newsmac.edu.ph', 'password' => 'RexterDe7441', 'name' => 'Rexter De Luna'],
    ['number' => 'STU-000046', 'email' => 'markjiro.delacruz@student.newsmac.edu.ph', 'password' => 'MarkjiroDela8291', 'name' => 'Mark Jiro Dela Cruz'],
    ['number' => 'STU-000047', 'email' => 'markadrian.dulman@student.newsmac.edu.ph', 'password' => 'MarkadrianDulman7994', 'name' => 'Mark Adrian Dulman'],
    ['number' => 'STU-000048', 'email' => 'maxallan.gabrino@student.newsmac.edu.ph', 'password' => 'MaxallanGabrino5049', 'name' => 'Max Allan Gabrino'],
    ['number' => 'STU-000049', 'email' => 'larry.gicoso@student.newsmac.edu.ph', 'password' => 'LarryGicoso4332', 'name' => 'Larry Gicoso'],
    ['number' => 'STU-000050', 'email' => 'rheimar.legaspi@student.newsmac.edu.ph', 'password' => 'RheimarLegaspi8653', 'name' => 'Rheimar Legaspi'],
    ['number' => 'STU-000051', 'email' => 'laurence.mariano@student.newsmac.edu.ph', 'password' => 'LaurenceMariano5433', 'name' => 'Laurence Mariano'],
    ['number' => 'STU-000052', 'email' => 'jhonndavid.nava@student.newsmac.edu.ph', 'password' => 'JhonndavidNava6670', 'name' => 'Jhonn David Nava'],
    ['number' => 'STU-000053', 'email' => 'marcpaul.ngoho@student.newsmac.edu.ph', 'password' => 'MarcpaulNgoho2977', 'name' => 'Marc Paul Ngoho'],
    ['number' => 'STU-000054', 'email' => 'vyxzen.perez@student.newsmac.edu.ph', 'password' => 'VyxzenPerez7764', 'name' => 'Vyxzen Perez'],
    ['number' => 'STU-000055', 'email' => 'winston.saparon@student.newsmac.edu.ph', 'password' => 'WinstonSaparon1017', 'name' => 'Winston Saparon'],
    ['number' => 'STU-000056', 'email' => 'jibren.sarip@student.newsmac.edu.ph', 'password' => 'JibrenSarip4034', 'name' => 'Jibren Sarip'],
    ['number' => 'STU-000057', 'email' => 'seanjheremt.satombo@student.newsmac.edu.ph', 'password' => 'SeanjheremtSatombo6318', 'name' => 'Sean Jheremt Satombo'],
    ['number' => 'STU-000058', 'email' => 'andrey.trinidad@student.newsmac.edu.ph', 'password' => 'AndreyTrinidad8651', 'name' => 'Andrey Trinidad'],
    ['number' => 'STU-000059', 'email' => 'justine.tymico@student.newsmac.edu.ph', 'password' => 'JustineTymico1415', 'name' => 'Justine Tymico'],
    ['number' => 'STU-000060', 'email' => 'edrich.vergara@student.newsmac.edu.ph', 'password' => 'EdrichVergara8737', 'name' => 'Edrich Vergara'],
];

$updated = 0;
$notFound = 0;
$errors = 0;

foreach ($students as $index => $studentData) {
    $num = $index + 1;
    echo "[$num/20] Processing: {$studentData['name']} ({$studentData['number']})\n";
    
    try {
        // Find student by student number
        $student = Student::where('student_number', $studentData['number'])->first();
        
        if (!$student) {
            echo "  ❌ Student not found with number: {$studentData['number']}\n";
            $notFound++;
            continue;
        }
        
        // Find user account by email (since user_pk_id might be null)
        $user = User::where('email', $studentData['email'])
                    ->where('type', 'student')
                    ->first();
        
        if (!$user) {
            echo "  ❌ User account not found for email: {$studentData['email']}\n";
            $notFound++;
            continue;
        }
        
        // Update user_pk_id if it's null
        if (!$user->user_pk_id) {
            $user->user_pk_id = $student->id;
        }
        
        // Update password
        $user->password = Hash::make($studentData['password']);
        $user->save();
        
        echo "  ✅ Password updated successfully!\n";
        echo "     Email: {$user->email}\n";
        echo "     New Password: {$studentData['password']}\n";
        $updated++;
        
    } catch (Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
        $errors++;
    }
    
    echo "\n";
}

echo "========================================\n";
echo "SUMMARY\n";
echo "========================================\n";
echo "Total Students: " . count($students) . "\n";
echo "✅ Successfully Updated: $updated\n";
echo "❌ Not Found: $notFound\n";
echo "⚠️  Errors: $errors\n";
echo "========================================\n";

if ($updated > 0) {
    echo "\n✅ Password changes completed!\n";
    echo "Students can now login with their new passwords.\n";
}
