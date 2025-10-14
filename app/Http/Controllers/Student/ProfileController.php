<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
 
     
    public function show()
    {
        $user = Auth::guard('student')->user();
        $student = Student::findOrFail($user->user_pk_id);
        return view('student.profile.show', compact('student'));
    }

    public function edit()
    {
        $user = Auth::guard('student')->user();
        $student = Student::findOrFail($user->user_pk_id);
        return view('student.profile.edit', compact('student'));
    }

    /**
     * Update the student's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::guard('student')->user();
        $student = Student::findOrFail($user->user_pk_id);

        $validated = $request->validate([
            'email' => 'required|email|unique:students,email,' . $student->id,
            'mobile_number' => 'required|string|max:20|unique:students,mobile_number,' . $student->id,
            'address' => 'nullable|string|max:500',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'required|string|max:20|unique:students,guardian_contact,' . $student->id,
            'guardian_email' => 'required|email|unique:students,guardian_email,' . $student->id,
        ]);

        $student->update($validated);

        return redirect()->route('student.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the student's profile picture.
     */
    public function updateProfilePicture(Request $request)
    {
        $user = Auth::guard('student')->user();
        $student = Student::findOrFail($user->user_pk_id);

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        // Delete old profile picture if exists
        if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
            Storage::disk('public')->delete($student->profile_picture);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        
        $student->update([
            'profile_picture' => $path,
        ]);

        return redirect()->route('student.profile.show')
            ->with('success', 'Profile picture updated successfully!');
    }

    /**
     * Remove the student's profile picture.
     */
    public function deleteProfilePicture()
    {
        $user = Auth::guard('student')->user();
        $student = Student::findOrFail($user->user_pk_id);

        if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
            Storage::disk('public')->delete($student->profile_picture);
        }

        $student->update([
            'profile_picture' => null,
        ]);

        return redirect()->route('student.profile.show')
            ->with('success', 'Profile picture removed successfully!');
    }

    /**
     * Show the form for changing password.
     */
    public function editPassword()
    {
        return view('student.profile.change-password');
    }

    /**
     * Update the student's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('student')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('student.profile.show')
            ->with('success', 'Password changed successfully!');
    }
}
