<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentTypeController extends Controller
{
    public function index(): View
    {
        $items = AssessmentType::orderBy('name')->paginate(20);
        return view('admin.assessment_types.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.assessment_types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/', 'unique:assessment_types,key'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $item = AssessmentType::create($data);
        return redirect()->route('admin.assessment-types.show', $item)->with('success', 'Assessment type created.');
    }

    public function show(AssessmentType $assessmentType): View
    {
        return view('admin.assessment_types.show', ['item' => $assessmentType]);
    }

    public function edit(AssessmentType $assessmentType): View
    {
        return view('admin.assessment_types.edit', ['item' => $assessmentType]);
    }

    public function update(Request $request, AssessmentType $assessmentType): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/', 'unique:assessment_types,key,' . $assessmentType->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $assessmentType->update($data);

        return redirect()->route('admin.assessment-types.show', $assessmentType)->with('success', 'Assessment type updated.');
    }

    public function destroy(AssessmentType $assessmentType): RedirectResponse
    {
        $assessmentType->delete();
        return redirect()->route('admin.assessment-types.index')->with('success', 'Assessment type deleted.');
    }
}
