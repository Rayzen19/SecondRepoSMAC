<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Strand;
use App\Models\SubjectRecordResult;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;

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

        // Announcement Statistics
        $announcementStats = [
            'total' => Announcement::count(),
            'active' => Announcement::where('is_active', true)->count(),
            'scheduled' => Announcement::where('is_active', true)
                ->where('published_at', '>', now())
                ->count(),
            'expired' => Announcement::where('expires_at', '<', now())->count(),
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
        $totalResults = SubjectRecordResult::count();
        $passedCount = SubjectRecordResult::where('final_score', '>=', 75)->count();
        $failedCount = $totalResults - $passedCount;
        
        $passFailStats = [
            'total' => $totalResults,
            'passed' => $passedCount,
            'failed' => $failedCount,
            'pass_rate' => $totalResults > 0 ? round(($passedCount / $totalResults) * 100, 1) : 0,
            'fail_rate' => $totalResults > 0 ? round(($failedCount / $totalResults) * 100, 1) : 0,
        ];

        // Attendance Overview (placeholder logic)
        $attendance = [
            'daily' => ['students' => 97, 'teachers' => 98],
            'weekly' => ['students' => 95, 'teachers' => 97],
            'monthly' => ['students' => 94, 'teachers' => 96],
            'absentee' => [
                ['section' => 'STEM 11-A', 'percent' => 3.2],
                ['section' => 'ABM 12-B', 'percent' => 2.5],
                ['section' => 'HUMSS 11-C', 'percent' => 4.1],
                ['section' => 'TVL 12-D', 'percent' => 1.8],
            ]
        ];

        // Academic Calendar (placeholder)
        $calendar = [
            ['date' => 'Sep 2', 'event' => 'First Day of Classes'],
            ['date' => 'Oct 15', 'event' => 'Midterm Exams'],
            ['date' => 'Dec 20', 'event' => 'Christmas Break Starts'],
            ['date' => 'Jan 6', 'event' => 'Classes Resume'],
            ['date' => 'Mar 10', 'event' => 'Final Exams'],
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
            'attendance',
            'calendar'
        ));
    }
}
