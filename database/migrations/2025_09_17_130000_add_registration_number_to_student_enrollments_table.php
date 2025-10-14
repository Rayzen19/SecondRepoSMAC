<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('student_enrollments', 'registration_number')) {
                $table->string('registration_number')->nullable()->unique()->after('academic_year_strand_section_id');
            }
        });

        // Backfill existing rows if any
        try {
            $rows = DB::table('student_enrollments')->whereNull('registration_number')->orderBy('id')->get();
            foreach ($rows as $row) {
                // Fetch AY name and derive year prefix
                $ay = DB::table('academic_years')->where('id', $row->academic_year_id)->first();
                $prefix = $ay ? substr((string) $ay->name, 0, 4) : date('Y');
                // Count existing non-null reg numbers for this AY to determine next sequence
                $count = DB::table('student_enrollments')
                    ->where('academic_year_id', $row->academic_year_id)
                    ->whereNotNull('registration_number')
                    ->count();
                $seq = $count + 1;
                $reg = sprintf('%s-%04d', $prefix, $seq);

                // Retry until unique
                $tries = 0;
                do {
                    try {
                        DB::table('student_enrollments')->where('id', $row->id)->update(['registration_number' => $reg]);
                        $ok = true;
                    } catch (\Throwable $e) {
                        $tries++;
                        $seq++;
                        $reg = sprintf('%s-%04d', $prefix, $seq);
                        $ok = $tries < 5;
                    }
                } while (!$ok);
            }
        } catch (\Throwable $e) {
            // swallow backfill errors to avoid failing migration on legacy data
        }
    }

    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('student_enrollments', 'registration_number')) {
                $table->dropUnique(['registration_number']);
                $table->dropColumn('registration_number');
            }
        });
    }
};
