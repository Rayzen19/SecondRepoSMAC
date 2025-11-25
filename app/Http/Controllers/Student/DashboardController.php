<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Announcement;
use App\Models\SubjectEnrollment;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('student')->user();

        // Try to get the student model referenced by the auth guard
        $student = null;
        if ($user && isset($user->user_pk_id)) {
            $student = Student::with('studentEnrollments')->find($user->user_pk_id);
        }

        if (!$student) {
            // If session contains student snapshot, use it for display
            $sessionStudent = $request->session()->get('student');
            if ($sessionStudent) {
                $student = (object) $sessionStudent;
            }
        }

        // Recent announcements
        $announcements = Announcement::active()->latest()->take(5)->get();

        // Try to get current enrollment and subjects/grades if possible
        $enrollment = null;
        $subjects = collect();
        if (isset($student->id)) {
            $enrollment = $student->studentEnrollments()->with('academicYear', 'section')->latest()->first();
            if ($enrollment) {
                $subjects = SubjectEnrollment::where('student_enrollment_id', $enrollment->id)->get();
            }
        }

        return view('student.dashboard', compact('student', 'announcements', 'enrollment', 'subjects'));
    }
}
