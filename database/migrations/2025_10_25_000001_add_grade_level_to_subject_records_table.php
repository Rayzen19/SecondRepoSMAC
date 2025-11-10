<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds a 'grade_level' column to separate records by grade level.
     * This allows teachers to maintain separate assessments for different grade levels
     * (e.g., Grade 11 and Grade 12) within the same subject assignment.
     */
    public function up(): void
    {
        Schema::table('subject_records', function (Blueprint $table) {
            if (!Schema::hasColumn('subject_records', 'grade_level')) {
                $table->string('grade_level', 10)->nullable()->after('term');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_records', function (Blueprint $table) {
            if (Schema::hasColumn('subject_records', 'grade_level')) {
                $table->dropColumn('grade_level');
            }
        });
    }
};
