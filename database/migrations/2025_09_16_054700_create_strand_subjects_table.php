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
        Schema::create('strand_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('strand_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('semestral_period')->nullable();
            $table->float('written_works_percentage')->default(20);
            $table->float('performance_tasks_percentage')->default(60);
            $table->float('quarterly_assessment_percentage')->default(20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('strand_id')->references('id')->on('strands')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->unique(['strand_id', 'subject_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strand_subjects');
    }
};
