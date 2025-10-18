<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('academic_year_strand_subjects')) {
            return;
        }

        Schema::table('academic_year_strand_subjects', function (Blueprint $table) {
            // Ensure individual indexes exist for FKs before dropping the composite unique index
            try { $table->index('academic_year_id', 'idx_ays_academic_year'); } catch (\Throwable $e) {}
            try { $table->index('strand_id', 'idx_ays_strand'); } catch (\Throwable $e) {}
            try { $table->index('subject_id', 'idx_ays_subject'); } catch (\Throwable $e) {}
            try { $table->index('teacher_id', 'idx_ays_teacher'); } catch (\Throwable $e) {}

            // Drop old unique index that prevented multiple sections per subject for the same teacher
            try {
                $table->dropUnique('unique_academic_year_strand_subject');
            } catch (\Throwable $e) {
                // Index might already be dropped; ignore
            }

            // Add new unique index that includes the section pointer
            $table->unique(
                ['teacher_id', 'academic_year_id', 'strand_id', 'subject_id', 'academic_year_strand_section_id'],
                'uniq_teacher_year_strand_subject_section'
            );
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('academic_year_strand_subjects')) {
            return;
        }

        Schema::table('academic_year_strand_subjects', function (Blueprint $table) {
            // Drop the new unique index
            try {
                $table->dropUnique('uniq_teacher_year_strand_subject_section');
            } catch (\Throwable $e) {
                // ignore if not present
            }

            // Restore the original unique index (without section)
            $table->unique(
                ['teacher_id', 'academic_year_id', 'strand_id', 'subject_id'],
                'unique_academic_year_strand_subject'
            );
        });
    }
};
