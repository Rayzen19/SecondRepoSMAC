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
        if (Schema::hasTable('academic_year_strand_subjects')) {
            return;
        }
        $deps = ['academic_years', 'strands', 'subjects', 'teachers', 'academic_year_strand_advisers'];
        foreach ($deps as $dep) {
            if (!Schema::hasTable($dep)) {
                return;
            }
        }
        Schema::create('academic_year_strand_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_strand_adviser_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('strand_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');

            $table->float('written_works_percentage')->default(20);
            $table->float('performance_tasks_percentage')->default(60);
            $table->float('quarterly_assessment_percentage')->default(20);

            $table->float('written_works_based_grade_percentage')->default(0);
            $table->float('performance_tasks_based_grade_percentage')->default(70);
            $table->float('quarterly_assessment_based_grade_percentage')->default(0);

            $table->float('over_all_based_grade_percentage')->default(0);

            $table->timestamps();

            $table->foreign('academic_year_strand_adviser_id', 'fk_ays_adviser')->references('id')->on('academic_year_strand_advisers')->onDelete('cascade');
            $table->foreign('academic_year_id', 'fk_ays_academic_year')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('strand_id', 'fk_ays_strand')->references('id')->on('strands')->onDelete('cascade');
            $table->foreign('subject_id', 'fk_ays_subject')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id', 'fk_ays_teacher')->references('id')->on('teachers')->onDelete('cascade');
            $table->unique(['teacher_id', 'academic_year_id', 'strand_id', 'subject_id'], 'unique_academic_year_strand_subject');
        });
    }

    /**
     * Reverse the migrations.
     */ 
    public function down(): void
    {
        Schema::dropIfExists('academic_year_strand_subjects');
    }
};
