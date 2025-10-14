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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->unsignedBigInteger('student_id');          // student
            $table->unsignedBigInteger('strand_id')->nullable(); // SHS strand
            $table->unsignedBigInteger('academic_year_id');    // academic year + semester
            $table->unsignedBigInteger('academic_year_strand_section_id');    // academic year + semester
            // Registration number (auto-generated per year)
            $table->string('registration_number')->unique();
            // Enrollment info
            $table->enum('status', ['enrolled', 'dropped', 'completed'])->default('enrolled');
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('academic_year_strand_section_id')->references('id')->on('academic_year_strand_sections')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('strand_id')->references('id')->on('strands')->onDelete('set null');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
