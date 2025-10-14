<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::orderBy('grade')->orderBy('name')->paginate(15);
        return view('admin.sections.index', compact('sections'));
    }

    public function create()
    {
        $grades = ['G-11', 'G-12'];
        return view('admin.sections.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'grade' => ['required', 'in:G-11,G-12'],
        ]);

        Section::create($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section created.');
    }

    public function edit(Section $section)
    {
        $grades = ['G-11', 'G-12'];
        return view('admin.sections.edit', compact('section', 'grades'));
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'grade' => ['required', 'in:G-11,G-12'],
        ]);

        $section->update($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section updated.');
    }
}
