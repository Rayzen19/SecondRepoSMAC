<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Strand;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with('strand')->orderBy('grade')->orderBy('name')->paginate(15);
        return view('admin.sections.index', compact('sections'));
    }

    public function create()
    {
        $grades = ['G-11', 'G-12'];
        $strands = Strand::where('is_active', true)->orderBy('name')->get();
        return view('admin.sections.create', compact('grades', 'strands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'grade' => ['required', 'in:G-11,G-12'],
            'strand_id' => ['nullable', 'exists:strands,id'],
        ]);

        Section::create($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section created.');
    }

    public function edit(Section $section)
    {
        $grades = ['G-11', 'G-12'];
        $strands = Strand::where('is_active', true)->orderBy('name')->get();
        return view('admin.sections.edit', compact('section', 'grades', 'strands'));
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'grade' => ['required', 'in:G-11,G-12'],
            'strand_id' => ['nullable', 'exists:strands,id'],
        ]);

        $section->update($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section updated.');
    }
}
