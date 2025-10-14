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
        Schema::create('subject_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_enrollment_id');

            $table->enum('quarter', ['1st', '2nd'])->nullable();
            $table->enum('type', ['written work', 'performance task', 'quarterly assessment'])->nullable();
            $table->decimal('max_score', 5, 2)->nullable();
            $table->date('date_given')->nullable();
            $table->text('remarks')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subject_enrollment_id')->references('id')->on('subject_enrollments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_records');
    }
};
