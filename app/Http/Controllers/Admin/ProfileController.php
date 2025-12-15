<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Get the currently authenticated user (admin or co-admin).
     */
    private function getAuthenticatedUser()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        }
        
        if (Auth::guard('co-admin')->check()) {
            return Auth::guard('co-admin')->user();
        }
        
        abort(401);
    }

    /**
     * Display the admin's profile.
     */
    public function show()
    {
        $admin = $this->getAuthenticatedUser();
        return view('admin.profile.show', compact('admin'));
    }

    /**
     * Show the form for editing the admin's profile.
     */
    public function edit()
    {
        $admin = $this->getAuthenticatedUser();
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Update the admin's profile information.
     */
    public function update(Request $request)
    {
        $admin = $this->getAuthenticatedUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->save();

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the form for changing password.
     */
    public function editPassword()
    {
        $admin = $this->getAuthenticatedUser();
        return view('admin.profile.password', compact('admin'));
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request)
    {
        $admin = $this->getAuthenticatedUser();

        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()],
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $admin->password = Hash::make($validated['password']);
        $admin->save();

        return redirect()->route('admin.profile.show')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Update the admin's profile picture.
     */
    public function updateProfilePicture(Request $request)
    {
        $admin = $this->getAuthenticatedUser();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        // Delete old profile picture if exists
        if ($admin->profile_picture && Storage::disk('public')->exists($admin->profile_picture)) {
            Storage::disk('public')->delete($admin->profile_picture);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile_pictures/admins', 'public');
        
        $admin->update([
            'profile_picture' => $path,
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile picture updated successfully!');
    }

    /**
     * Delete the admin's profile picture.
     */
    public function deleteProfilePicture()
    {
        $admin = $this->getAuthenticatedUser();

        if ($admin->profile_picture && Storage::disk('public')->exists($admin->profile_picture)) {
            Storage::disk('public')->delete($admin->profile_picture);
        }

        $admin->update([
            'profile_picture' => null,
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile picture removed successfully!');
    }
}
