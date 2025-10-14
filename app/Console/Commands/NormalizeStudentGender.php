<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeStudentGender extends Command
{
    protected $signature = 'students:normalize-gender';
    protected $description = "Replace any 'other' student gender with a random 'male' or 'female' value";

    public function handle(): int
    {
        $driver = DB::getDriverName();
        $this->info("DB driver: {$driver}");

        $affected = 0;
        if ($driver === 'mysql') {
            $affected = DB::update("UPDATE students SET gender = IF(RAND() < 0.5, 'male', 'female') WHERE gender = 'other'");
        } elseif ($driver === 'pgsql') {
            $affected = DB::update("UPDATE students SET gender = CASE WHEN RANDOM() < 0.5 THEN 'male' ELSE 'female' END WHERE gender = 'other'");
        } elseif ($driver === 'sqlite') {
            $affected = DB::update("UPDATE students SET gender = CASE WHEN ABS(RANDOM()) % 2 = 0 THEN 'male' ELSE 'female' END WHERE gender = 'other'");
        } elseif ($driver === 'sqlsrv') {
            $affected = DB::update("UPDATE students SET gender = CASE WHEN ABS(CHECKSUM(NEWID())) % 2 = 0 THEN 'male' ELSE 'female' END WHERE gender = 'other'");
        } else {
            $this->warn('Unsupported database driver for random update; falling back to per-row update.');
            $affected = DB::table('students')->where('gender', 'other')->count();
            DB::table('students')->where('gender', 'other')->orderBy('id')->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('students')->where('id', $row->id)->update([
                        'gender' => (mt_rand(0, 1) === 0 ? 'male' : 'female'),
                    ]);
                }
            });
        }

        $this->info("Updated {$affected} student(s) with random gender.");
        return self::SUCCESS;
    }
}
