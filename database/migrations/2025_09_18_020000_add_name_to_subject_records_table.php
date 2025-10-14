<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subject_records')) {
            return;
        }
        if (Schema::hasColumn('subject_records', 'name')) {
            return;
        }

        Schema::table('subject_records', function (Blueprint $table) {
            $table->string('name')->nullable()->after('subject_enrollment_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('subject_records')) {
            return;
        }
        if (!Schema::hasColumn('subject_records', 'name')) {
            return;
        }

        Schema::table('subject_records', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
