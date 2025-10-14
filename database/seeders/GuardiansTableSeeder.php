<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GuardiansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Guardian::factory(20)->create();
    }
}
