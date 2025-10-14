<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "2025-2026"
            $table->enum('semester', ['1st', '2nd'])->nullable();
            $table->enum('academic_status', ['pending', 'completed', 'ongoing enrollment', 'ongoing school year'])->default('pending');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Add list of academic years

        $academicYears = [
            ['name' => '2023-2024', 'semester' => '1st', 'academic_status' => 'pending', 'is_active' => 0],
            ['name' => '2023-2024', 'semester' => '2nd', 'academic_status' => 'pending', 'is_active' => 0],
            ['name' => '2024-2025', 'semester' => '1st', 'academic_status' => 'pending', 'is_active' => 0],
            ['name' => '2024-2025', 'semester' => '2nd', 'academic_status' => 'pending', 'is_active' => 0],
            ['name' => '2025-2026', 'semester' => '1st', 'academic_status' => 'ongoing enrollment', 'is_active' => 1],
            ['name' => '2025-2026', 'semester' => '2nd', 'academic_status' => 'pending', 'is_active' => 0],
        ];

        DB::table('academic_years')->insert($academicYears);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
