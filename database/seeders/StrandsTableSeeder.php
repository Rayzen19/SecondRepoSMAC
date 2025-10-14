<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['code' => 'STEM', 'name' => 'Science, Technology, Engineering and Mathematics'],
            ['code' => 'ABM', 'name' => 'Accountancy, Business and Management'],
            ['code' => 'HUMSS', 'name' => 'Humanities and Social Sciences'],
            ['code' => 'GAS', 'name' => 'General Academic Strand'],
            ['code' => 'TVL-ICT', 'name' => 'Technical-Vocational-Livelihood - Information and Communications Technology'],
            ['code' => 'TVL-HE', 'name' => 'Technical-Vocational-Livelihood - Home Economics'],
        ];
        foreach ($rows as $row) {
            \App\Models\Strand::updateOrCreate(['code' => $row['code']], ['name' => $row['name']]);
        }
    }
}
