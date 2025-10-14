<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_records', function (Blueprint $table) {
            // Drop FK first if it exists, then drop the column
            try {
                $table->dropForeign(['subject_enrollment_id']);
            } catch (\Throwable $e) {
                // FK may already be missing; proceed to drop column
            }
            if (Schema::hasColumn('subject_records', 'subject_enrollment_id')) {
                $table->dropColumn('subject_enrollment_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subject_records', function (Blueprint $table) {
            if (!Schema::hasColumn('subject_records', 'subject_enrollment_id')) {
                $table->unsignedBigInteger('subject_enrollment_id')->nullable()->after('id');
                // Recreate the foreign key if the referenced table exists
                try {
                    $table->foreign('subject_enrollment_id')
                        ->references('id')
                        ->on('subject_enrollments')
                        ->cascadeOnDelete();
                } catch (\Throwable $e) {
                    // The referenced table might not exist in this context; ignore
                }
            }
        });
    }
};
