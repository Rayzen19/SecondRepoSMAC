<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE students SET gender = IF(RAND() < 0.5, 'male', 'female') WHERE gender = 'other'");
        } elseif ($driver === 'pgsql') {
            DB::statement("UPDATE students SET gender = CASE WHEN RANDOM() < 0.5 THEN 'male' ELSE 'female' END WHERE gender = 'other'");
        } elseif ($driver === 'sqlite') {
            DB::statement("UPDATE students SET gender = CASE WHEN ABS(RANDOM()) % 2 = 0 THEN 'male' ELSE 'female' END WHERE gender = 'other'");
        } elseif ($driver === 'sqlsrv') {
            DB::statement("UPDATE students SET gender = CASE WHEN ABS(CHECKSUM(NEWID())) % 2 = 0 THEN 'male' ELSE 'female' END WHERE gender = 'other'");
        } else {
            // Fallback: loop in PHP if driver unknown
            DB::table('students')->where('gender', 'other')->orderBy('id')->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('students')->where('id', $row->id)->update([
                        'gender' => (mt_rand(0, 1) === 0 ? 'male' : 'female'),
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        // Non-destructive; nothing to revert deterministically.
    }
};
