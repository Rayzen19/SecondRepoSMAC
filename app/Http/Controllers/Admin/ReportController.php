<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\Strand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index()
    {
        // Total counts
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalGuardians = Guardian::count();

        // Students registered per month (current year)
        $studentsPerMonth = Student::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->mapWithKeys(function ($item) {
            $monthNames = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];
            return [$monthNames[$item->month] => $item->count];
        });

        // Fill in missing months with 0
        $allMonths = [
            'January' => 0, 'February' => 0, 'March' => 0, 'April' => 0,
            'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0,
            'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0
        ];
        $studentsPerMonth = array_merge($allMonths, $studentsPerMonth->toArray());

        // Students per strand - get all strands with their unique student counts
        $allStrands = Strand::all();
        $enrollmentCounts = DB::table('student_enrollments')
            ->select('strand_id', DB::raw('COUNT(DISTINCT student_id) as count'))
            ->groupBy('strand_id')
            ->pluck('count', 'strand_id');

        $studentsPerStrand = $allStrands->mapWithKeys(function ($strand) use ($enrollmentCounts) {
            $count = $enrollmentCounts->get($strand->id, 0);
            return [$strand->name => $count];
        });

        return view('admin.reports.index', compact(
            'totalStudents',
            'totalTeachers',
            'totalGuardians',
            'studentsPerMonth',
            'studentsPerStrand'
        ));
    }

    public function export()
    {
        // Get all data
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalGuardians = Guardian::count();

        $studentsPerMonth = Student::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->mapWithKeys(function ($item) {
            $monthNames = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];
            return [$monthNames[$item->month] => $item->count];
        });

        $allMonths = [
            'January' => 0, 'February' => 0, 'March' => 0, 'April' => 0,
            'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0,
            'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0
        ];
        $studentsPerMonth = array_merge($allMonths, $studentsPerMonth->toArray());

        $allStrands = Strand::all();
        $enrollmentCounts = DB::table('student_enrollments')
            ->select('strand_id', DB::raw('COUNT(DISTINCT student_id) as count'))
            ->groupBy('strand_id')
            ->pluck('count', 'strand_id');

        $studentsPerStrand = $allStrands->mapWithKeys(function ($strand) use ($enrollmentCounts) {
            $count = $enrollmentCounts->get($strand->id, 0);
            return [$strand->name => $count];
        });

        // Generate CSV content
        $filename = 'system_reports_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // System Summary Section
        fputcsv($handle, ['SYSTEM SUMMARY STATISTICS']);
        fputcsv($handle, ['Category', 'Total Count']);
        fputcsv($handle, ['Total Number of Students', $totalStudents]);
        fputcsv($handle, ['Total Number of Teachers', $totalTeachers]);
        fputcsv($handle, ['Total Number of Guardians', $totalGuardians]);
        fputcsv($handle, ['Total System Users', $totalStudents + $totalTeachers + $totalGuardians]);
        fputcsv($handle, []);

        // Monthly Registration Section
        fputcsv($handle, ['STUDENT REGISTRATION PER MONTH (' . date('Y') . ')']);
        fputcsv($handle, ['Month', 'Number of Students Registered']);
        foreach ($studentsPerMonth as $month => $count) {
            fputcsv($handle, [$month, $count]);
        }
        fputcsv($handle, ['Total for ' . date('Y'), array_sum($studentsPerMonth)]);
        fputcsv($handle, []);

        // Strand Distribution Section
        fputcsv($handle, ['STUDENT DISTRIBUTION PER STRAND']);
        fputcsv($handle, ['Strand', 'Number of Students', 'Percentage']);
        $totalStrandStudents = array_sum($studentsPerStrand->toArray());
        foreach ($studentsPerStrand as $strand => $count) {
            $percentage = $totalStrandStudents > 0 ? number_format(($count / $totalStrandStudents) * 100, 1) : 0;
            fputcsv($handle, [$strand, $count, $percentage . '%']);
        }
        fputcsv($handle, ['Total Enrolled', $totalStrandStudents, '100%']);

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
