<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Strand;
use App\Models\StrandSubject;
use App\Models\Subject;

echo "=== Checking Strand and Subject Configuration ===\n\n";

// List all strands
$strands = Strand::all();
echo "All Strands:\n";
foreach ($strands as $strand) {
    echo "  ID: {$strand->id} | Code: {$strand->code} | Name: {$strand->name}\n";
    
    $strandSubjectCount = StrandSubject::where('strand_id', $strand->id)->count();
    echo "    Subjects assigned: {$strandSubjectCount}\n";
}

echo "\n";

// Check TVL-CP specifically
$tvlcp = Strand::where('code', 'TVL-CP')->first();
if ($tvlcp) {
    echo "TVL-CP Strand Details:\n";
    echo "  ID: {$tvlcp->id}\n";
    echo "  Code: {$tvlcp->code}\n";
    echo "  Name: {$tvlcp->name}\n\n";
    
    $tvlcpSubjects = StrandSubject::with('subject')
        ->where('strand_id', $tvlcp->id)
        ->get();
    
    echo "  Assigned Subjects: {$tvlcpSubjects->count()}\n";
    foreach ($tvlcpSubjects as $ss) {
        $subject = $ss->subject;
        echo "    - [{$subject->code}] {$subject->name}\n";
    }
}

echo "\n=== All Subjects in Database ===\n";
$allSubjects = Subject::orderBy('type')->orderBy('name')->get();
echo "Total subjects: {$allSubjects->count()}\n\n";

$byType = $allSubjects->groupBy('type');
foreach ($byType as $type => $subjects) {
    echo "{$type}: {$subjects->count()} subjects\n";
    foreach ($subjects as $subject) {
        echo "  - [{$subject->code}] {$subject->name} (Semester: {$subject->semester}, Units: {$subject->units})\n";
    }
    echo "\n";
}
