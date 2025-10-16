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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
            $table->string('student_number')->nullable();
            $table->string('student_name')->nullable();
            $table->string('strand')->nullable();
            $table->string('year_level')->nullable();
            $table->string('section')->nullable();
            $table->string('semester')->nullable();
            $table->string('subject')->nullable();
            $table->string('assessment_type')->nullable(); // e.g., 'quiz', 'exam', 'attendance'
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->text('remarks')->nullable();
            $table->string('recorded_by')->nullable(); // teacher or admin who recorded
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better query performance
            $table->index(['student_id', 'date']);
            $table->index(['student_number', 'date']);
            $table->index(['academic_year_id', 'date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
