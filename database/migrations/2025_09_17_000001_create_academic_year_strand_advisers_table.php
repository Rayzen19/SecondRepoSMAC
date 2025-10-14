<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_year_strand_advisers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('strand_id');
            $table->unsignedBigInteger('teacher_id');
            $table->timestamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('strand_id')->references('id')->on('strands')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->unique(['academic_year_id', 'strand_id'], 'unique_ay_strand_adviser');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_year_strand_advisers');
    }
};
