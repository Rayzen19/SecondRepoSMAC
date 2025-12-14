<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivacyController extends Controller
{
    /**
     * Show the privacy settings page.
     */
    public function index()
    {
        $user = Auth::guard('student')->user();
        $student = Student::findOrFail($user->user_pk_id);
        
        return view('student.privacy.index', compact('student'));
    }

    /**
     * Update the guardian access privacy setting.
     */
    public function updateGuardianAccess(Request $request)
    {
        $user = Auth::guard('student')->user();
        $student = Student::findOrFail($user->user_pk_id);

        $request->validate([
            'allow_guardian_access' => 'required|boolean',
        ]);

        $student->update([
            'allow_guardian_access' => $request->allow_guardian_access,
        ]);

        $message = $request->allow_guardian_access 
            ? 'Guardian access to your grades and enhancement has been enabled.'
            : 'Guardian access to your grades and enhancement has been disabled.';

        return redirect()->route('student.privacy.index')
            ->with('success', $message);
    }
}
