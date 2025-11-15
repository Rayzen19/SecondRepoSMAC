<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CoAdminController extends Controller
{
    /**
     * Display a listing of co-admins.
     */
    public function index()
    {
        $coAdmins = User::where('type', 'co-admin')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.co-admins.index', compact('coAdmins'));
    }

    /**
     * Show the form for creating a new co-admin.
     */
    public function create()
    {
        return view('admin.co-admins.create');
    }

    /**
     * Store a newly created co-admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'type' => 'co-admin',
            'user_pk_id' => null,
        ]);

        return redirect()->route('admin.co-admins.index')
            ->with('success', 'Co-Admin created successfully.');
    }

    /**
     * Display the specified co-admin.
     */
    public function show(User $coAdmin)
    {
        if ($coAdmin->type !== 'co-admin') {
            abort(404);
        }

        return view('admin.co-admins.show', compact('coAdmin'));
    }

    /**
     * Show the form for editing the specified co-admin.
     */
    public function edit(User $coAdmin)
    {
        if ($coAdmin->type !== 'co-admin') {
            abort(404);
        }

        return view('admin.co-admins.edit', compact('coAdmin'));
    }

    /**
     * Update the specified co-admin.
     */
    public function update(Request $request, User $coAdmin)
    {
        if ($coAdmin->type !== 'co-admin') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $coAdmin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $coAdmin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $coAdmin->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.co-admins.index')
            ->with('success', 'Co-Admin updated successfully.');
    }

    /**
     * Remove the specified co-admin.
     */
    public function destroy(User $coAdmin)
    {
        if ($coAdmin->type !== 'co-admin') {
            abort(404);
        }

        $coAdmin->delete();

        return redirect()->route('admin.co-admins.index')
            ->with('success', 'Co-Admin deleted successfully.');
    }
}
