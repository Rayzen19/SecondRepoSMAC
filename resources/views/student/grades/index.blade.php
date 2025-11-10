@extends('student.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Grades</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Grades</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('student.grades.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} ({{ $year->semester }} Semester)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select" onchange="this.form.submit()">
                            <option value="midterm" {{ ($selectedTerm ?? '') === 'midterm' ? 'selected' : '' }}>Midterm</option>
                            <option value="finals" {{ ($selectedTerm ?? '') === 'finals' ? 'selected' : '' }}>Finals</option>
                            <option value="final" {{ ($selectedTerm ?? '') === 'final' ? 'selected' : '' }}>Final Grade</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" class="form-select" onchange="this.form.submit()">
                            @foreach($gradeLevels as $value => $label)
                                <option value="{{ $value }}" {{ ($selectedGradeLevel ?? 'all') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Grades Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #ddddf6ff;">
                <h5 class="mb-0">Grades</h5>
            </div>
            <div class="card-body">
                @if($grades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th class="text-center">1st Sem</th>
                                    <th class="text-center">2nd Sem</th>
                                    <th class="text-center">Average</th>
                                    <th class="text-center">Final Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grades as $grade)
                                    <tr>
                                        <td>{{ $grade['subject_code'] }}</td>
                                        <td>{{ $grade['subject_name'] }}</td>
                                        <td class="text-center">
                                            @if($grade['fq_grade'])
                                                <span class="badge grade-badge {{ $grade['fq_grade'] >= 90 ? 'bg-success' : ($grade['fq_grade'] >= 80 ? 'bg-primary' : ($grade['fq_grade'] >= 75 ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ number_format($grade['fq_grade'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($grade['sq_grade'])
                                                <span class="badge grade-badge {{ $grade['sq_grade'] >= 90 ? 'bg-success' : ($grade['sq_grade'] >= 80 ? 'bg-primary' : ($grade['sq_grade'] >= 75 ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ number_format($grade['sq_grade'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($grade['a_grade'])
                                                <span class="badge grade-badge {{ $grade['a_grade'] >= 90 ? 'bg-success' : ($grade['a_grade'] >= 80 ? 'bg-primary' : ($grade['a_grade'] >= 75 ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ number_format($grade['a_grade'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($grade['f_grade'])
                                                <span class="badge grade-badge {{ $grade['f_grade'] >= 90 ? 'bg-success' : ($grade['f_grade'] >= 80 ? 'bg-primary' : ($grade['f_grade'] >= 75 ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ number_format($grade['f_grade'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($average)
                        <div class="text-end mt-3">
                            <div class="d-inline-block bg-primary text-white px-4 py-2 rounded">
                                <strong>Average: {{ $average }}</strong>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="alert alert-info">
                        No grades available for this selection.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    /* Larger font for grade badges */
    .grade-badge {
        font-size: 19px !important;
    }
</style>
@endsection
