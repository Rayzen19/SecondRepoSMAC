<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Strand;
use App\Models\SubjectRecordResult;
// use App\Models\Event; // Uncomment if Event model exists
// use App\Models\Announcement; // Uncomment if Announcement model exists

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch counts
        $studentsCount = Student::count();
        $teachersCount = Teacher::count();
        $sectionsCount = Section::count();
        // $eventsCount = Event::count(); // Uncomment if Event model exists
        $eventsCount = 12; // Placeholder

        // Recent Announcements (replace with Announcement::latest()->take(3)->get() if model exists)
        $announcements = [
            'Math Olympiad Winners Announced',
            'New Library Opening Next Week',
            'Community Outreach Program',
        ];

        // Student Performance Analytics (average grades by strand)
        $strands = Strand::all();
        $performance = [];
        foreach ($strands as $strand) {
            $avg = SubjectRecordResult::whereHas('subjectRecord.assignment.strand', function($q) use ($strand) {
                $q->where('id', $strand->id);
            })->avg('final_score'); // Use correct score column
            $performance[$strand->name] = round($avg ?? 0, 1);
        }

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
            'eventsCount',
            'announcements',
            'performance',
            'attendance',
            'calendar'
        ));
    }
}
