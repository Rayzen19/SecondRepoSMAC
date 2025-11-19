@extends('student.components.template')

@section('title', 'Pre-Enrollment')

@section('content')
<div class="container-fluid p-0">
    <h1>Pre-Enrollment Page Works!</h1>
    <p>If you see this, the route and view are working correctly.</p>
    
    <div class="card mt-3">
        <div class="card-body">
            <h3>Debug Info:</h3>
            <p><strong>Current User:</strong> {{ Auth::guard('student')->user()->first_name ?? 'Not logged in' }} {{ Auth::guard('student')->user()->last_name ?? '' }}</p>
            <p><strong>Academic Year:</strong> {{ $currentAcademicYear->name ?? 'N/A' }}</p>
            <p><strong>Pre-enrollment Enabled:</strong> {{ $currentAcademicYear->pre_enrollment_enabled ?? false ? 'Yes' : 'No' }}</p>
        </div>
    </div>
    
    <a href="{{ route('student.dashboard') }}" class="btn btn-info mt-3">Back to Dashboard</a>
</div>
@endsection
