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
        Schema::table('academic_year_strand_subjects', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign('fk_ays_adviser');
            
            // Make the column nullable
            $table->unsignedBigInteger('academic_year_strand_adviser_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('academic_year_strand_adviser_id', 'fk_ays_adviser')
                  ->references('id')
                  ->on('academic_year_strand_advisers')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_year_strand_subjects', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign('fk_ays_adviser');
            
            // Make the column not nullable again
            $table->unsignedBigInteger('academic_year_strand_adviser_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint with cascade
            $table->foreign('academic_year_strand_adviser_id', 'fk_ays_adviser')
                  ->references('id')
                  ->on('academic_year_strand_advisers')
                  ->onDelete('cascade');
        });
    }
};
