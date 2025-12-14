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

        // If checkbox is checked, it will be "1", otherwise the hidden field sends "0"
        $allowAccess = $request->input('allow_guardian_access', 0);

        $student->update([
            'allow_guardian_access' => (bool)$allowAccess,
        ]);

        $message = $allowAccess 
            ? 'Guardian access to your grades and enhancement has been enabled.'
            : 'Guardian access to your grades and enhancement has been disabled.';

        return redirect()->route('student.privacy.index')
            ->with('success', $message);
    }
}
