<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_year_strand_advisers', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_year_strand_advisers', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable()->after('teacher_id');
                $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('academic_year_strand_advisers', function (Blueprint $table) {
            if (Schema::hasColumn('academic_year_strand_advisers', 'section_id')) {
                $table->dropForeign(['section_id']);
                $table->dropColumn('section_id');
            }
        });
    }
};
