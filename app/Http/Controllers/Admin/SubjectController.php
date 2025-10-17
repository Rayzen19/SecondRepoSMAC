<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Strand;
use App\Models\StrandSubject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['strandSubjects.strand'])->orderBy('code');
        
        // Filter by strand if specified
        if ($request->filled('strand_id')) {
            $query->whereHas('strandSubjects', function($q) use ($request) {
                $q->where('strand_id', $request->strand_id);
            });
        }
        
        $allSubjects = $query->get();
        
        // Group subjects by type
        $coreSubjects = $allSubjects->where('type', 'core');
        $appliedSubjects = $allSubjects->where('type', 'applied');
        $specializedSubjects = $allSubjects->where('type', 'specialized');
        
        $strands = Strand::where('is_active', true)->orderBy('code')->get();
        
        return view('admin.subjects.index', compact('coreSubjects', 'appliedSubjects', 'specializedSubjects', 'strands'));
    }

    public function create()
    {
        $strands = Strand::where('is_active', true)->orderBy('code')->get();
        return view('admin.subjects.create', compact('strands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:subjects,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:0',
            'type' => 'required|in:core,applied,specialized',
            'semester' => 'required|in:1st,2nd',
            'strand_id' => 'required|exists:strands,id',
        ]);

        // Create the subject
        $subject = Subject::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'units' => $data['units'],
            'type' => $data['type'],
            'semester' => $data['semester'],
        ]);

        // Automatically link to the selected strand with default percentages
        StrandSubject::create([
            'strand_id' => $data['strand_id'],
            'subject_id' => $subject->id,
            'semestral_period' => $data['semester'],
            'written_works_percentage' => 20,
            'performance_tasks_percentage' => 60,
            'quarterly_assessment_percentage' => 20,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.subjects.show', $subject)
            ->with('success', 'Subject created and linked to strand successfully.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:0',
            'type' => 'required|in:core,applied,specialized',
            'semester' => 'required|in:1st,2nd',
        ]);

        $subject->update($data);

        return redirect()->route('admin.subjects.show', $subject)->with('success', 'Subject updated successfully.');
    }

    public function show(Subject $subject)
    {
        return view('admin.subjects.show', compact('subject'));
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return redirect()
                ->route('admin.subjects.index')
                ->with('success', 'Subject deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.subjects.index')
                ->with('error', 'Unable to delete subject. It may be linked to other records.');
        }
    }
}
