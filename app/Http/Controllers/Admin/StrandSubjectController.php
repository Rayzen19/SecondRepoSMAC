<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Strand;
use App\Models\StrandSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StrandSubjectController extends Controller
{
    public function edit(StrandSubject $strandSubject)
    {
        // Eager load relations for context (subject and strand)
        $strandSubject->load(['subject', 'strand']);

        // Semestral period is a simple 1st/2nd dropdown like create
        return view('admin.strand_subjects.edit', [
            'pivot' => $strandSubject,
        ]);
    }

    public function update(Request $request, StrandSubject $strandSubject)
    {
        $data = $request->validate([
            'grade_level' => ['nullable', 'in:11,12'],
            'semestral_period' => ['nullable', 'string', 'max:100'],
            'written_works_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'performance_tasks_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'quarterly_assessment_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $sum = ($data['written_works_percentage'] ?? 0)
            + ($data['performance_tasks_percentage'] ?? 0)
            + ($data['quarterly_assessment_percentage'] ?? 0);
        if (abs($sum - 100) > 0.001) {
            return back()->withInput()->withErrors([
                'written_works_percentage' => 'The percentages must add up to 100%.',
            ]);
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $strandSubject->update($data);

        $returnTo = $request->input('return_to');
        if ($returnTo === 'strand') {
            return redirect()->route('admin.strands.show', $strandSubject->strand_id)
                ->with('success', 'Link updated successfully.');
        }

        return redirect()->route('admin.subjects.show', $strandSubject->subject_id)
            ->with('success', 'Link updated successfully.');
    }

    public function create(Request $request)
    {
    $subjectId = $request->query('subject');
    $strandId = $request->query('strand');

    $subject = $subjectId ? Subject::find($subjectId) : null;
    $strand = $strandId ? Strand::find($strandId) : null;

    // Lists for selection when one side isn't preselected
    $strands = Strand::orderBy('code')->get();
    $subjects = Subject::orderBy('code')->get();

    return view('admin.strand_subjects.create', compact('subject', 'strand', 'strands', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'strand_id' => [
                'required',
                'exists:strands,id',
                Rule::unique('strand_subjects', 'strand_id')->where(function ($q) use ($request) {
                    return $q->where('subject_id', $request->input('subject_id'));
                }),
            ],
            'grade_level' => ['nullable', 'in:11,12'],
            'semestral_period' => ['nullable', 'string', 'max:100'],
            'written_works_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'performance_tasks_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'quarterly_assessment_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ], [
            'strand_id.unique' => 'This subject is already linked to the selected strand.',
        ]);

        // Optional: ensure the percentages add up to 100
        $sum = ($data['written_works_percentage'] ?? 0)
            + ($data['performance_tasks_percentage'] ?? 0)
            + ($data['quarterly_assessment_percentage'] ?? 0);
        if (abs($sum - 100) > 0.001) {
            return back()->withInput()->withErrors([
                'written_works_percentage' => 'The percentages must add up to 100%.',
            ]);
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $pivot = StrandSubject::create($data);

        $returnTo = $request->input('return_to');
        if ($returnTo === 'strand') {
            return redirect()->route('admin.strands.show', $data['strand_id'])
                ->with('success', 'Strand linked to subject successfully.');
        }

        return redirect()->route('admin.subjects.show', $data['subject_id'])
            ->with('success', 'Subject linked to strand successfully.');
    }

    public function destroy(StrandSubject $strandSubject)
    {
        $strandId = $strandSubject->strand_id;
        
        try {
            $strandSubject->delete();
            return redirect()
                ->route('admin.strands.show', $strandId)
                ->with('success', 'Subject removed from strand successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.strands.show', $strandId)
                ->with('error', 'Unable to remove subject from strand.');
        }
    }
}
