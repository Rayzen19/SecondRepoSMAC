<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('academic_year_strand_sections')) {
            // Base table missing; nothing to alter in this pass.
            return;
        }
        if (!Schema::hasColumn('academic_year_strand_sections', 'adviser_teacher_id')) {
            Schema::table('academic_year_strand_sections', function (Blueprint $table) {
                $table->unsignedBigInteger('adviser_teacher_id')->nullable()->after('section_id');
                $table->foreign('adviser_teacher_id')
                    ->references('id')
                    ->on('teachers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('academic_year_strand_sections') && Schema::hasColumn('academic_year_strand_sections', 'adviser_teacher_id')) {
            Schema::table('academic_year_strand_sections', function (Blueprint $table) {
                $table->dropForeign(['adviser_teacher_id']);
                $table->dropColumn('adviser_teacher_id');
            });
        }
    }
};
