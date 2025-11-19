<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('teacher')->user();
        
        // Get teacher with relationships
        $teacher = Teacher::with([
            'advisedSections' => function ($query) {
                $query->where('is_active', true)
                    ->with(['academicYear', 'strand', 'section']);
            },
            'teachingAssignments' => function ($query) {
                $query->with([
                    'subject',
                    'sectionAssignment.section',
                    'sectionAssignment.strand'
                ]);
            }
        ])->find($user->user_pk_id);

        // If teacher not found, use empty defaults
        if (!$teacher) {
            $teacher = new Teacher();
        }

        // Get statistics
        $stats = [
            'total_sections' => $teacher->teachingAssignments ? $teacher->teachingAssignments->pluck('sectionAssignment.section_pk_id')->unique()->count() : 0,
            'total_subjects' => $teacher->teachingAssignments ? $teacher->teachingAssignments->count() : 0,
            'advised_sections' => $teacher->advisedSections ? $teacher->advisedSections->count() : 0,
            'total_students' => $this->getTotalStudents($teacher),
        ];

        // Get recent messages (last 5)
        $recentMessages = MessageRecipient::where('recipient_id', $user->id)
            ->with(['message.sender'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Count unread messages
        $unreadMessagesCount = MessageRecipient::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        // Get recent announcements (last 5)
        $recentAnnouncements = Announcement::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get sections handled with details
        $sectionsHandled = collect();
        
        if ($teacher->teachingAssignments) {
            $sectionsHandled = $teacher->teachingAssignments->groupBy('sectionAssignment.section_pk_id')->map(function ($assignments, $sectionId) use ($teacher) {
                $firstAssignment = $assignments->first();
                $section = $firstAssignment->sectionAssignment;
                
                if (!$section) {
                    return null;
                }
                
                return [
                    'section_id' => $sectionId,
                    'section_name' => $section->section->name ?? 'N/A',
                    'strand_code' => $section->strand->strand_code ?? 'N/A',
                    'grade_level' => $section->grade_level ?? 'N/A',
                    'subjects' => $assignments->pluck('subject.name')->toArray(),
                    'subject_count' => $assignments->count(),
                    'is_adviser' => $teacher->advisedSections && $teacher->advisedSections->contains('section_pk_id', $sectionId),
                ];
            })->filter()->values();
        }

        // Get today's schedule (if you have a schedule table)
        $todaySchedule = $this->getTodaySchedule($teacher);

        return view('teacher.dashboard', compact(
            'teacher',
            'stats',
            'recentMessages',
            'unreadMessagesCount',
            'recentAnnouncements',
            'sectionsHandled',
            'todaySchedule'
        ));
    }

    private function getTotalStudents($teacher)
    {
        // Get unique students from all teaching assignments
        $studentIds = collect();
        
        foreach ($teacher->teachingAssignments as $assignment) {
            if ($assignment->subjectEnrollments) {
                $ids = $assignment->subjectEnrollments->pluck('student_pk_id')->unique();
                $studentIds = $studentIds->merge($ids);
            }
        }
        
        return $studentIds->unique()->count();
    }

    private function getTodaySchedule($teacher)
    {
        // This is a placeholder - adjust based on your schedule structure
        // If you don't have a schedule table, return an empty collection
        return collect();
        
        // Example if you have a schedule:
        // $dayOfWeek = now()->format('l'); // Monday, Tuesday, etc.
        // return Schedule::where('teacher_id', $teacher->teacher_pk_id)
        //     ->where('day_of_week', $dayOfWeek)
        //     ->orderBy('start_time')
        //     ->get();
    }
}
