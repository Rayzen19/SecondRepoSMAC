<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the grade_level column if it doesn't exist
        if (!Schema::hasColumn('strand_subjects', 'grade_level')) {
            Schema::table('strand_subjects', function (Blueprint $table) {
                $table->string('grade_level', 10)->nullable()->after('subject_id');
            });
        }
        
        // Check for duplicates and handle them before changing the unique constraint
        $duplicates = DB::select("
            SELECT strand_id, subject_id, COUNT(*) as count 
            FROM strand_subjects 
            GROUP BY strand_id, subject_id 
            HAVING count > 1
        ");
        
        if (!empty($duplicates)) {
            foreach ($duplicates as $duplicate) {
                // Keep only the first record for each duplicate, delete the rest
                $records = DB::table('strand_subjects')
                    ->where('strand_id', $duplicate->strand_id)
                    ->where('subject_id', $duplicate->subject_id)
                    ->orderBy('id')
                    ->get();
                
                // Delete all but the first record
                foreach ($records->slice(1) as $record) {
                    DB::table('strand_subjects')->where('id', $record->id)->delete();
                }
            }
        }
        
        Schema::table('strand_subjects', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['strand_id']);
            $table->dropForeign(['subject_id']);
            
            // Drop the old unique constraint
            $table->dropUnique(['strand_id', 'subject_id']);
            
            // Add new unique constraint that includes grade_level
            $table->unique(['strand_id', 'subject_id', 'grade_level'], 'strand_subjects_unique');
            
            // Re-add foreign keys
            $table->foreign('strand_id')->references('id')->on('strands')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('strand_subjects', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['strand_id']);
            $table->dropForeign(['subject_id']);
            
            // Drop the new unique constraint
            $table->dropUnique('strand_subjects_unique');
            
            // Restore the old unique constraint
            $table->unique(['strand_id', 'subject_id']);
            
            // Re-add foreign keys
            $table->foreign('strand_id')->references('id')->on('strands')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }
};
