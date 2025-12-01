<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Strand;
use App\Models\SubjectRecordResult;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch counts
        $studentsCount = Student::count();
        $teachersCount = Teacher::count();
        $sectionsCount = Section::count();
        $announcementsCount = Announcement::count();
        $eventsCount = 12; // Placeholder

        // Announcement Statistics (guard against missing columns)
        $announcementTable = (new Announcement())->getTable();
        $hasIsActive = Schema::hasTable($announcementTable) && Schema::hasColumn($announcementTable, 'is_active');
        $hasPublishedAt = Schema::hasTable($announcementTable) && Schema::hasColumn($announcementTable, 'published_at');
        $hasExpiresAt = Schema::hasTable($announcementTable) && Schema::hasColumn($announcementTable, 'expires_at');

        $announcementStats = [
            'total' => Announcement::count(),
            'active' => $hasIsActive ? Announcement::where('is_active', true)->count() : Announcement::count(),
            'scheduled' => ($hasPublishedAt ? (
                    $hasIsActive ? Announcement::where('is_active', true)->where('published_at', '>', now())->count()
                                  : Announcement::where('published_at', '>', now())->count()
                ) : 0),
            'expired' => $hasExpiresAt ? Announcement::where('expires_at', '<', now())->count() : 0,
        ];

        // Recent Announcements (real data from database)
        $recentMessages = Announcement::with('creator')
            ->latest()
            ->take(5)
            ->get();

        // Student Performance Analytics (average grades by strand)
        $strands = Strand::all();
        $performance = [];
        foreach ($strands as $strand) {
            $avg = SubjectRecordResult::whereHas('subjectRecord.assignment.strand', function($q) use ($strand) {
                $q->where('id', $strand->id);
            })->avg('final_score'); // Use correct score column
            $performance[$strand->name] = round($avg ?? 0, 1);
        }

        // Top Performing Students (Top 5 by average final score)
        $topStudents = SubjectRecordResult::select(
            'student_id',
            DB::raw('AVG(final_score) as average_score')
        )
        ->with('student:id,student_number,first_name,last_name')
        ->groupBy('student_id')
        ->orderByDesc('average_score')
        ->limit(5)
        ->get()
        ->map(function($result) {
            return [
                'student_number' => $result->student->student_number ?? 'N/A',
                'name' => $result->student ? $result->student->first_name . ' ' . $result->student->last_name : 'N/A',
                'average' => round($result->average_score, 2)
            ];
        });

        // Pass/Fail Statistics (passing grade = 75)
        // Based on student averages, not individual subject records
        $studentAverages = SubjectRecordResult::select(
            'student_id',
            DB::raw('AVG(final_score) as avg_score')
        )
        ->whereNotNull('final_score')
        ->groupBy('student_id')
        ->get();
        
        $totalStudents = $studentAverages->count();
        $passedStudents = $studentAverages->where('avg_score', '>=', 75)->count();
        $failedStudents = $studentAverages->where('avg_score', '<', 75)->count();
        
        $passFailStats = [
            'total' => $totalStudents,
            'passed' => $passedStudents,
            'failed' => $failedStudents,
            'pass_rate' => $totalStudents > 0 ? round(($passedStudents / $totalStudents) * 100, 1) : 0,
            'fail_rate' => $totalStudents > 0 ? round(($failedStudents / $totalStudents) * 100, 1) : 0,
        ];

        // Academic Calendar (placeholder - would need an events table)
        $calendar = [
            ['date' => 'Sep 2', 'event' => 'First Day of Classes'],
            ['date' => 'Oct 15', 'event' => 'Midterm Exams'],
            ['date' => 'Dec 20', 'event' => 'Christmas Break Starts'],
            ['date' => 'Jan 6', 'event' => 'Classes Resume'],
            ['date' => 'Mar 10', 'event' => 'Final Exams'],
        ];

        // User Registration Trends (last 12 months) - Students Only
        $registrationTrends = Student::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

        // Fill in missing months with 0 registrations
        $months = [];
        $counts = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M Y');
            
            $found = $registrationTrends->firstWhere('month', $monthKey);
            $months[] = $monthLabel;
            $counts[] = $found ? $found->count : 0;
        }

        $registrationData = [
            'months' => $months,
            'counts' => $counts
        ];

        // Students Per Strand (Bar Chart Data) - Count unique students only
        $strandsWithCounts = Strand::leftJoin('student_enrollments', 'strands.id', '=', 'student_enrollments.strand_id')
            ->select('strands.name', DB::raw('COUNT(DISTINCT student_enrollments.student_id) as student_count'))
            ->groupBy('strands.id', 'strands.name')
            ->orderBy('strands.name')
            ->get();

        $strandData = [
            'names' => $strandsWithCounts->pluck('name')->toArray(),
            'counts' => $strandsWithCounts->pluck('student_count')->toArray()
        ];

        return view('admin.dashboard', compact(
            'studentsCount',
            'teachersCount',
            'sectionsCount',
            'announcementsCount',
            'eventsCount',
            'announcementStats',
            'recentMessages',
            'performance',
            'topStudents',
            'passFailStats',
            'calendar',
            'registrationData',
            'strandData'
        ));
    }
}
