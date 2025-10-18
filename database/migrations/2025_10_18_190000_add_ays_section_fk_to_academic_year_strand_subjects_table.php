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
            if (!Schema::hasColumn('academic_year_strand_subjects', 'academic_year_strand_section_id')) {
                $table->unsignedBigInteger('academic_year_strand_section_id')->nullable()->after('academic_year_strand_adviser_id');
                $table->foreign('academic_year_strand_section_id', 'fk_ays_section')
                    ->references('id')->on('academic_year_strand_sections')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('academic_year_strand_subjects')) {
            return;
        }
        Schema::table('academic_year_strand_subjects', function (Blueprint $table) {
            if (Schema::hasColumn('academic_year_strand_subjects', 'academic_year_strand_section_id')) {
                $table->dropForeign('fk_ays_section');
                $table->dropColumn('academic_year_strand_section_id');
            }
        });
    }
};
