<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\Request;

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
        return view('admin.guardians.show', compact('guardian'));
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
}
