<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one academic year exists (1st semester), set it active and deactivate others
        $name = date('Y') . '-' . (date('Y') + 1);
        $semester = '1st';
        AcademicYear::query()->update(['is_active' => false]);
        AcademicYear::firstOrCreate(['name' => $name, 'semester' => $semester], [
            'is_active' => true,
        ]);
    }
}
