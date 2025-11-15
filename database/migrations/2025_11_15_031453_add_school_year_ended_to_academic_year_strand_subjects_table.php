<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('academic_year_strand_subjects', function (Blueprint $table) {
            $table->boolean('school_year_ended')->default(false)->after('grades_published');
            $table->timestamp('school_year_ended_at')->nullable()->after('school_year_ended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_year_strand_subjects', function (Blueprint $table) {
            $table->dropColumn(['school_year_ended', 'school_year_ended_at']);
        });
    }
};
