@extends('teacher.components.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Student Scores Overview</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Scores</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('teacher.scores.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="academic_year_id" class="form-label">Academic Year</label>
                            <select name="academic_year_id" id="academic_year_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Select Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} - {{ ucfirst($year->semester) }} Semester
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-select" onchange="this.form.submit()" required>
                                <option value="">Select Subject</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject['id'] }}" {{ $selectedSubjectId == $subject['id'] ? 'selected' : '' }}>
                                        {{ $subject['code'] }} - {{ $subject['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="section_id" class="form-label">Section</label>
                            <select name="section_id" id="section_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Sections</option>
                                @foreach($availableSections as $section)
                                    <option value="{{ $section['id'] }}" {{ $selectedSectionId == $section['id'] ? 'selected' : '' }}>
                                        {{ $section['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="term" class="form-label">Term/Quarter</label>
                            <select name="term" id="term" class="form-select" onchange="this.form.submit()">
                                <option value="midterm" {{ $selectedTerm === 'midterm' ? 'selected' : '' }}>1st Quarter (Midterm)</option>
                                <option value="finals" {{ $selectedTerm === 'finals' ? 'selected' : '' }}>2nd Quarter (Finals)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="assessment_id" class="form-label">Assessment</label>
                            <select name="assessment_id" id="assessment_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Assessments</option>
                                @foreach($assessmentsList as $assessment)
                                    <option value="{{ $assessment['id'] }}" {{ request('assessment_id') == $assessment['id'] ? 'selected' : '' }}>
                                        {{ $assessment['name'] }} ({{ $assessment['type'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="student_id" class="form-label">Student</label>
                            <select name="student_id" id="student_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Students</option>
                                @foreach($availableStudents as $student)
                                    <option value="{{ $student['id'] }}" {{ $selectedStudentId == $student['id'] ? 'selected' : '' }}>
                                        {{ $student['student_number'] }} - {{ $student['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            @if($selectedSubjectId)
                @if($assessmentsList->isEmpty())
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        No assessments found for the selected subject and term.
                    </div>
                @elseif($studentScores->isEmpty())
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        No students enrolled in this subject.
                    </div>
                @else
                    <!-- Student Scores List -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="ti ti-list-check me-2"></i>
                                Student Scores
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student Number</th>
                                            @foreach($assessmentsList as $assessment)
                                                <th class="text-center">
                                                    {{ $assessment['name'] }}<br>
                                                    <small class="text-muted">{{ $assessment['type'] }}</small>
                                                </th>
                                            @endforeach
                                            <th class="text-center">Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($studentScores as $studentData)
                                            <tr>
                                                <td><strong>{{ $studentData['student_name'] }}</strong></td>
                                                <td>{{ $studentData['student_number'] }}</td>
                                                @foreach($studentData['scores'] as $scoreData)
                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                                            <input 
                                                                type="number" 
                                                                class="form-control form-control-sm score-input text-center" 
                                                                style="width: 70px;"
                                                                step="0.01"
                                                                min="0"
                                                                max="{{ $scoreData['max_score'] }}"
                                                                value="{{ $scoreData['score'] !== null ? $scoreData['score'] : '' }}"
                                                                placeholder="0"
                                                                data-student-id="{{ $studentData['student_id'] }}"
                                                                data-assessment-id="{{ $scoreData['assessment_id'] }}"
                                                                data-max-score="{{ $scoreData['max_score'] }}"
                                                            >
                                                            <span class="text-muted">/{{ number_format($scoreData['max_score'], 0) }}</span>
                                                        </div>
                                                    </td>
                                                @endforeach
                                                <td class="text-center">
                                                    <span class="badge 
                                                        @if($studentData['average_percentage'] >= 90) bg-success
                                                        @elseif($studentData['average_percentage'] >= 80) bg-primary
                                                        @elseif($studentData['average_percentage'] >= 75) bg-warning
                                                        @else bg-danger
                                                        @endif
                                                        fs-6
                                                    ">
                                                        {{ $studentData['average_percentage'] }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-success" id="saveScoresBtn">
                                    <i class="ti ti-device-floppy me-1"></i> Save Scores
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    Please select a subject to view student scores.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .portal-mobile-header, .footer, .page-header, .card-body form, button, .breadcrumb {
        display: none !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        page-break-inside: avoid;
    }
    .card-header {
        background-color: #0d6efd !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    table {
        font-size: 10px;
    }
    .badge {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const saveBtn = document.getElementById('saveScoresBtn');
    
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const inputs = document.querySelectorAll('.score-input');
            const scores = [];
            
            inputs.forEach(input => {
                const value = input.value;
                if (value !== '') {
                    scores.push({
                        student_id: input.dataset.studentId,
                        assessment_id: input.dataset.assessmentId,
                        raw_score: parseFloat(value),
                        max_score: parseFloat(input.dataset.maxScore)
                    });
                }
            });
            
            if (scores.length === 0) {
                alert('No scores to save.');
                return;
            }
            
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
            
            fetch('{{ route("teacher.scores.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ scores: scores })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Scores saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save scores'));
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Save Scores';
                }
            })
            .catch(error => {
                alert('Error saving scores: ' + error.message);
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Save Scores';
            });
        });
    }
});
</script>
@endsection
