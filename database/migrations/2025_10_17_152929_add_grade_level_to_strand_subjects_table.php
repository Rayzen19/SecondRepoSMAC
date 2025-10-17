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
        Schema::table('strand_subjects', function (Blueprint $table) {
            $table->string('grade_level', 2)->nullable()->after('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('strand_subjects', function (Blueprint $table) {
            $table->dropColumn('grade_level');
        });
    }
};
