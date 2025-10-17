<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Strand;
use Illuminate\Http\Request;

class StrandController extends Controller
{
    public function index()
    {
        $strands = Strand::orderBy('code')->paginate(15);
        return view('admin.strands.index', compact('strands'));
    }

    public function create()
    {
        return view('admin.strands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:strands,code',
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);
        $strand = Strand::create($data);

        return redirect()->route('admin.strands.show', $strand)->with('success', 'Strand created successfully.');
    }

    public function show(Strand $strand)
    {
    // Eager-load related pivot rows and subjects for tabular display
    $strand->load(['strandSubjects.subject']);

    return view('admin.strands.show', compact('strand'));
    }

    public function edit(Strand $strand)
    {
        return view('admin.strands.edit', compact('strand'));
    }

    public function update(Request $request, Strand $strand)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:strands,code,' . $strand->id,
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $strand->update($data);

        return redirect()->route('admin.strands.show', $strand)->with('success', 'Strand updated successfully.');
    }

    public function destroy(Strand $strand)
    {
        try {
            $strand->delete();
            return redirect()
                ->route('admin.strands.index')
                ->with('success', 'Strand deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.strands.index')
                ->with('error', 'Unable to delete strand. It may be linked to other records.');
        }
    }
}
