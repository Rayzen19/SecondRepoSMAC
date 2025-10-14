<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard if dependencies are missing
        if (!Schema::hasTable('subject_records') || !Schema::hasTable('students')) {
            return; // dependencies not ready
        }

        if (Schema::hasTable('subject_record_results')) {
            return; // already exists
        }

        Schema::create('subject_record_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->text('remarks')->nullable();
            $table->text('description')->nullable();
            $table->date('date_submitted')->nullable();
            $table->decimal('raw_score', 8, 2)->nullable();
            $table->decimal('base_score', 8, 2)->nullable();
            $table->decimal('final_score', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_record_results');
    }
};
