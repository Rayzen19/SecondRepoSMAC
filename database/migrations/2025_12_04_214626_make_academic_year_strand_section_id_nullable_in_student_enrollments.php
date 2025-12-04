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
        Schema::table('student_enrollments', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['academic_year_strand_section_id']);
            
            // Make the column nullable
            $table->unsignedBigInteger('academic_year_strand_section_id')->nullable()->change();
            
            // Re-add the foreign key
            $table->foreign('academic_year_strand_section_id')
                  ->references('id')
                  ->on('academic_year_strand_sections')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['academic_year_strand_section_id']);
            
            // Make the column NOT nullable again
            $table->unsignedBigInteger('academic_year_strand_section_id')->nullable(false)->change();
            
            // Re-add the foreign key
            $table->foreign('academic_year_strand_section_id')
                  ->references('id')
                  ->on('academic_year_strand_sections')
                  ->onDelete('cascade');
        });
    }
};
