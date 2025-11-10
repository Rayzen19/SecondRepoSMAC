<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\User as SystemUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GuardianController extends Controller
{
    public function index()
    {
        $guardians = Guardian::orderBy('last_name')->paginate(15);
        return view('admin.guardians.index', compact('guardians'));
    }

    public function create()
    {
        return view('admin.guardians.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'guardian_number' => 'required|string|max:50|unique:guardians,guardian_number',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|max:255|unique:guardians,email',
            'mobile_number' => 'required|string|max:20|unique:guardians,mobile_number',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|string|max:255',
        ]);

        $guardian = Guardian::create($data);

        return redirect()->route('admin.guardians.show', $guardian)->with('success', 'Guardian created successfully.');
    }

    public function show(Guardian $guardian)
    {
        // Load students associated with this guardian
        $students = $guardian->students()
            ->with(['studentEnrollments' => function($query) {
                $query->latest();
            }])
            ->get();
        
        return view('admin.guardians.show', compact('guardian', 'students'));
    }

    public function edit(Guardian $guardian)
    {
        return view('admin.guardians.edit', compact('guardian'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $data = $request->validate([
            'guardian_number' => 'required|string|max:50|unique:guardians,guardian_number,' . $guardian->id,
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|max:255|unique:guardians,email,' . $guardian->id,
            'mobile_number' => 'required|string|max:20|unique:guardians,mobile_number,' . $guardian->id,
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|string|max:255',
        ]);

        $guardian->update($data);

        return redirect()->route('admin.guardians.show', $guardian)->with('success', 'Guardian updated successfully.');
    }

    public function destroy(Guardian $guardian)
    {
        // Check if guardian has any students linked
        $studentCount = $guardian->students()->count();
        
        if ($studentCount > 0) {
            return redirect()->back()->with('error', "Cannot delete guardian. They are linked to {$studentCount} student(s). Please unlink or reassign students first.");
        }

        // Permanently delete the guardian and linked auth account
        DB::transaction(function () use ($guardian) {
            // Delete linked auth user (guardian portal account)
            $user = SystemUser::where('type', 'guardian')->where('user_pk_id', $guardian->id)->first();
            if ($user) {
                $user->delete();
            }

            // Optionally remove any profile picture from storage if using a path
            try {
                if (!empty($guardian->profile_picture) && Storage::disk('public')->exists($guardian->profile_picture)) {
                    Storage::disk('public')->delete($guardian->profile_picture);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to delete guardian profile picture', [
                    'guardian_id' => $guardian->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Force delete guardian (bypass soft deletes)
            if (method_exists($guardian, 'forceDelete')) {
                $guardian->forceDelete();
            } else {
                $guardian->delete();
            }
        });

        return redirect()->route('admin.guardians.index')->with('success', 'Guardian permanently deleted.');
    }
}
