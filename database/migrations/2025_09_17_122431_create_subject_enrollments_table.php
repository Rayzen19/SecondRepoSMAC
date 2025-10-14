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
        Schema::create('subject_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_enrollment_id');
            $table->unsignedBigInteger('academic_year_strand_subject_id');

            $table->decimal('fq_grade', 5, 2)->nullable(); // First Quarter Grade
            $table->decimal('sq_grade', 5, 2)->nullable(); // Second Quarter Grade
            $table->decimal('a_grade', 5, 2)->nullable(); // Average Grade
            $table->decimal('f_grade', 5, 2)->nullable(); // Final Grade

            $table->text('remarks')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_enrollment_id')->references('id')->on('student_enrollments')->onDelete('cascade');
            $table->foreign('academic_year_strand_subject_id')->references('id')->on('academic_year_strand_subjects')->onDelete('cascade');
            $table->unique(['student_enrollment_id', 'academic_year_strand_subject_id'], 'unique_student_subject_enrollment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_enrollments');
    }
};
