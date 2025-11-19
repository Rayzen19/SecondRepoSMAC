<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::guard('guardian')->user();
        $guardian = Guardian::find($user->user_pk_id);
        
        if (!$guardian) {
            abort(404, 'Guardian profile not found');
        }

        // Load students associated with this guardian
        $students = $guardian->students()
            ->with(['studentEnrollments' => function($query) {
                $query->latest();
            }])
            ->get();

        return view('guardian.profile.show', compact('guardian', 'students'));
    }

    public function edit()
    {
        $user = Auth::guard('guardian')->user();
        $guardian = Guardian::find($user->user_pk_id);
        
        if (!$guardian) {
            abort(404, 'Guardian profile not found');
        }

        return view('guardian.profile.edit', compact('guardian'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('guardian')->user();
        $guardian = Guardian::find($user->user_pk_id);
        
        if (!$guardian) {
            abort(404, 'Guardian profile not found');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female',
            'mobile_number' => 'required|string|unique:guardians,mobile_number,' . $guardian->id,
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($guardian->profile_picture) {
                $oldPath = storage_path('app/public/' . $guardian->profile_picture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $path;
        }

        $guardian->update($validated);
        
        // Update auth user name
        DB::table('users')
            ->where('id', $user->id)
            ->update(['name' => $guardian->first_name . ' ' . $guardian->last_name]);

        return redirect()->route('guardian.profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('guardian')->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required', 
                'min:12', 
                'confirmed',
                'regex:/[a-z]/',      // must contain lowercase
                'regex:/[A-Z]/',      // must contain uppercase
                'regex:/[0-9]/',      // must contain number
                'regex:/[@$!%*#?&]/', // must contain symbol
            ],
        ], [
            'new_password.min' => 'Password must be at least 12 characters long.',
            'new_password.regex' => 'Password must contain uppercase letters, lowercase letters, numbers, and symbols.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update(['password' => Hash::make($validated['new_password'])]);

        return redirect()->route('guardian.profile.show')->with('success', 'Password updated successfully.');
    }

    public function removeProfilePicture()
    {
        $user = Auth::guard('guardian')->user();
        $guardian = Guardian::find($user->user_pk_id);
        
        if (!$guardian) {
            abort(404, 'Guardian profile not found');
        }

        if ($guardian->profile_picture) {
            // Delete the file from storage
            Storage::disk('public')->delete($guardian->profile_picture);
            
            // Update the database
            $guardian->update(['profile_picture' => null]);
        }

        return redirect()->route('guardian.profile.edit')->with('success', 'Profile picture removed successfully.');
    }
}
