<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds a 'term' column to separate midterm and finals records
     * within each semester/quarter.
     */
    public function up(): void
    {
        Schema::table('subject_records', function (Blueprint $table) {
            if (!Schema::hasColumn('subject_records', 'term')) {
                $table->enum('term', ['midterm', 'finals'])->nullable()->after('quarter');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_records', function (Blueprint $table) {
            if (Schema::hasColumn('subject_records', 'term')) {
                $table->dropColumn('term');
            }
        });
    }
};
