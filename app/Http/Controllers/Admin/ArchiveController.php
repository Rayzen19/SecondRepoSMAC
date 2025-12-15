<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    /**
     * Display the archive page with inactive teachers and students.
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'teachers');
        
        // Get inactive teachers
        $inactiveTeachers = Teacher::where('status', 'inactive')
            ->orderBy('updated_at', 'desc')
            ->paginate(15, ['*'], 'teachers_page');
        
        // Get inactive students
        $inactiveStudents = Student::where('status', 'inactive')
            ->orderBy('updated_at', 'desc')
            ->paginate(15, ['*'], 'students_page');
        
        return view('admin.archive.index', compact('inactiveTeachers', 'inactiveStudents', 'activeTab'));
    }

    /**
     * Restore a teacher from archive.
     */
    public function restoreTeacher(Teacher $teacher)
    {
        if ($teacher->status !== 'inactive') {
            if (request()->ajax()) {
                return response()->json(['message' => 'Teacher is not archived.'], 400);
            }
            return back()->with('error', 'Teacher is not archived.');
        }

        $teacher->update(['status' => 'active']);

        if (request()->ajax()) {
            return response()->json(['message' => 'Teacher restored successfully.'], 200);
        }
        return back()->with('success', 'Teacher restored successfully.');
    }

    /**
     * Restore a student from archive.
     */
    public function restoreStudent(Student $student)
    {
        if ($student->status !== 'inactive') {
            if (request()->ajax()) {
                return response()->json(['message' => 'Student is not archived.'], 400);
            }
            return back()->with('error', 'Student is not archived.');
        }

        $student->update(['status' => 'active']);

        if (request()->ajax()) {
            return response()->json(['message' => 'Student restored successfully.'], 200);
        }
        return back()->with('success', 'Student restored successfully.');
    }

    /**
     * Permanently delete a teacher.
     */
    public function deleteTeacher(Teacher $teacher)
    {
        if ($teacher->status !== 'inactive') {
            if (request()->ajax()) {
                return response()->json(['message' => 'Only archived teachers can be permanently deleted.'], 400);
            }
            return back()->with('error', 'Only archived teachers can be permanently deleted.');
        }

        $teacherName = $teacher->name;
        $teacher->delete();

        if (request()->ajax()) {
            return response()->json(['message' => "Teacher {$teacherName} has been permanently deleted."], 200);
        }
        return back()->with('success', "Teacher {$teacherName} has been permanently deleted.");
    }

    /**
     * Permanently delete a student.
     */
    public function deleteStudent(Student $student)
    {
        if ($student->status !== 'inactive') {
            if (request()->ajax()) {
                return response()->json(['message' => 'Only archived students can be permanently deleted.'], 400);
            }
            return back()->with('error', 'Only archived students can be permanently deleted.');
        }

        $studentName = $student->name;
        
        // Permanently delete the student and linked data
        DB::transaction(function () use ($student) {
            // Detach guardians from student
            $student->guardians()->detach();

            // Delete linked auth user (student portal account)
            $user = \App\Models\User::where('type', 'student')->where('user_pk_id', $student->id)->first();
            if ($user) {
                $user->delete();
            }

            // Optionally remove any profile picture from storage
            try {
                if (!empty($student->profile_picture) && Storage::disk('public')->exists($student->profile_picture)) {
                    Storage::disk('public')->delete($student->profile_picture);
                }
            } catch (\Throwable $e) {
                // Non-blocking
            }

            // Force delete student (bypass soft deletes)
            if (method_exists($student, 'forceDelete')) {
                $student->forceDelete();
            } else {
                $student->delete();
            }
        });

        if (request()->ajax()) {
            return response()->json(['message' => "Student {$studentName} has been permanently deleted."], 200);
        }
        return back()->with('success', "Student {$studentName} has been permanently deleted.");
    }
}
