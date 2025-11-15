<?php

/**
 * Import the 5 main strands into the system
 * Based on Senior High School tracks/strands
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Strand;

$strands = [
    [
        'code' => 'ABM',
        'name' => 'Accountancy, Business, and Management',
        'is_active' => true,
    ],
    [
        'code' => 'HUMSS',
        'name' => 'Humanities and Social Sciences',
        'is_active' => true,
    ],
    [
        'code' => 'STEM',
        'name' => 'Science, Technology, Engineering, and Mathematics',
        'is_active' => true,
    ],
    [
        'code' => 'TVL-BP',
        'name' => 'Bread and Pastry Production (NC II)',
        'is_active' => true,
    ],
    [
        'code' => 'TVL-CP',
        'name' => 'Computer Programming (Java) (NC III)',
        'is_active' => true,
    ],
];

echo "Starting strand import...\n\n";

foreach ($strands as $strandData) {
    try {
        // Check if strand already exists
        $existing = Strand::where('code', $strandData['code'])->first();
        
        if ($existing) {
            echo "✓ Strand '{$strandData['code']}' already exists (ID: {$existing->id})\n";
            echo "  Name: {$existing->name}\n";
            echo "  Status: " . ($existing->is_active ? 'Active' : 'Inactive') . "\n\n";
        } else {
            // Create new strand
            $strand = Strand::create($strandData);
            echo "✓ Created strand '{$strandData['code']}' (ID: {$strand->id})\n";
            echo "  Name: {$strand->name}\n";
            echo "  Status: Active\n\n";
        }
    } catch (Exception $e) {
        echo "✗ Error creating strand '{$strandData['code']}': " . $e->getMessage() . "\n\n";
    }
}

// Display final summary
echo "=================================\n";
echo "Import Summary\n";
echo "=================================\n";
$totalStrands = Strand::count();
$activeStrands = Strand::where('is_active', true)->count();

echo "Total strands in database: {$totalStrands}\n";
echo "Active strands: {$activeStrands}\n\n";

echo "All strands:\n";
$allStrands = Strand::orderBy('code')->get();
foreach ($allStrands as $strand) {
    $status = $strand->is_active ? '✓ Active' : '✗ Inactive';
    echo "  {$strand->code} - {$strand->name} [{$status}]\n";
}

echo "\n✓ Import process completed!\n";
echo "You can now view the strands at: http://127.0.0.1:8000/admin/strands\n";
