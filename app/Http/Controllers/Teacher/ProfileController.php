<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the teacher's profile.
     */
    public function show()
    {
        $user = Auth::guard('teacher')->user();
        $teacher = Teacher::with([
            'advisedSections' => function ($query) {
                $query->where('is_active', true)
                    ->with(['academicYear', 'strand', 'section']);
            },
            'teachingAssignments' => function ($query) {
                $query->with([
                    'academicYear',
                    'strand',
                    'subject',
                    'subjectEnrollments.studentEnrollment.academicYearStrandSection.section'
                ]);
            }
        ])->findOrFail($user->user_pk_id);
        
        return view('teacher.profile.show', compact('teacher'));
    }

    /**
     * Show the form for editing the teacher's profile.
     */
    public function edit()
    {
        $user = Auth::guard('teacher')->user();
        $teacher = Teacher::findOrFail($user->user_pk_id);
        return view('teacher.profile.edit', compact('teacher'));
    }

    /**
     * Update the teacher's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::guard('teacher')->user();
        $teacher = Teacher::findOrFail($user->user_pk_id);

        $validated = $request->validate([
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $teacher->update($validated);

        // Update user email to keep them in sync
        $user->email = $validated['email'];
        $user->save();

        return redirect()->route('teacher.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the teacher's profile picture.
     */
    public function updateProfilePicture(Request $request)
    {
        $user = Auth::guard('teacher')->user();
        $teacher = Teacher::findOrFail($user->user_pk_id);

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        // Delete old profile picture if exists
        if ($teacher->profile_picture && Storage::disk('public')->exists($teacher->profile_picture)) {
            Storage::disk('public')->delete($teacher->profile_picture);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile_pictures/teachers', 'public');
        
        $teacher->update([
            'profile_picture' => $path,
        ]);

        return redirect()->route('teacher.profile.show')
            ->with('success', 'Profile picture updated successfully!');
    }

    /**
     * Remove the teacher's profile picture.
     */
    public function deleteProfilePicture()
    {
        $user = Auth::guard('teacher')->user();
        $teacher = Teacher::findOrFail($user->user_pk_id);

        if ($teacher->profile_picture && Storage::disk('public')->exists($teacher->profile_picture)) {
            Storage::disk('public')->delete($teacher->profile_picture);
        }

        $teacher->update([
            'profile_picture' => null,
        ]);

        return redirect()->route('teacher.profile.show')
            ->with('success', 'Profile picture removed successfully!');
    }

    /**
     * Show the form for changing password.
     */
    public function editPassword()
    {
        return view('teacher.profile.change-password');
    }

    /**
     * Update the teacher's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('teacher')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('teacher.profile.show')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Remove adviser assignment from a section.
     */
    public function removeAdviserAssignment($sectionId)
    {
        $user = Auth::guard('teacher')->user();
        $teacher = Teacher::findOrFail($user->user_pk_id);

        // Find the section where this teacher is the adviser
        $section = \App\Models\AcademicYearStrandSection::where('id', $sectionId)
            ->where('adviser_teacher_id', $teacher->id)
            ->first();

        if (!$section) {
            return redirect()->route('teacher.profile.show')
                ->with('error', 'Section not found or you are not the adviser of this section.');
        }

        // Remove adviser assignment
        $section->adviser_teacher_id = null;
        $section->save();

        return redirect()->route('teacher.profile.show')
            ->with('success', 'Successfully removed from adviser assignment.');
    }

    /**
     * Remove teaching assignment from a subject.
     */
    public function removeTeachingAssignment($assignmentId)
    {
        $user = Auth::guard('teacher')->user();
        $teacher = Teacher::findOrFail($user->user_pk_id);

        // Find the teaching assignment
        $assignment = \App\Models\AcademicYearStrandSubject::where('id', $assignmentId)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$assignment) {
            return redirect()->route('teacher.profile.show')
                ->with('error', 'Teaching assignment not found or you are not assigned to this subject.');
        }

        // Remove teaching assignment
        $assignment->teacher_id = null;
        $assignment->save();

        return redirect()->route('teacher.profile.show')
            ->with('success', 'Successfully removed from teaching assignment.');
    }
}
