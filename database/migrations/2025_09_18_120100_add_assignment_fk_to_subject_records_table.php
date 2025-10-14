<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_records', function (Blueprint $table) {
            if (!Schema::hasColumn('subject_records', 'academic_year_strand_subject_id')) {
                $table->unsignedBigInteger('academic_year_strand_subject_id')->after('id');
                $table->foreign('academic_year_strand_subject_id')
                    ->references('id')
                    ->on('academic_year_strand_subjects')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subject_records', function (Blueprint $table) {
            if (Schema::hasColumn('subject_records', 'academic_year_strand_subject_id')) {
                try {
                    $table->dropForeign(['academic_year_strand_subject_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('academic_year_strand_subject_id');
            }
        });
    }
};
