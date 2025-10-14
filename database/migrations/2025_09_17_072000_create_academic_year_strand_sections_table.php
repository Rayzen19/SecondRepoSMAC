<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure referenced tables exist first to prevent FK errors when running out of order
        if (!Schema::hasTable('academic_years') || !Schema::hasTable('strands') || !Schema::hasTable('sections')) {
            return; // Defer until dependencies are migrated
        }

        Schema::create('academic_year_strand_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('strand_id');
            $table->unsignedBigInteger('section_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('strand_id')->references('id')->on('strands')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');

            // Ensure a section is assigned only once per academic year and strand
            $table->unique(['academic_year_id', 'strand_id', 'section_id'], 'unique_ay_strand_section');
        });
    }

    public function down(): void
    {
    Schema::dropIfExists('academic_year_strand_sections');
    }
};
