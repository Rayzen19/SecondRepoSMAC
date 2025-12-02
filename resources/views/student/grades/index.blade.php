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
                    <div class="col-md-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} ({{ $year->semester }} Semester)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select" onchange="this.form.submit()">
                            <option value="midterm" {{ ($selectedTerm ?? '') === 'midterm' ? 'selected' : '' }}>Midterm</option>
                            <option value="finals" {{ ($selectedTerm ?? '') === 'finals' ? 'selected' : '' }}>Finals</option>
                            <option value="final" {{ ($selectedTerm ?? '') === 'final' ? 'selected' : '' }}>Final Grade</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" class="form-select" onchange="this.form.submit()">
                            @foreach($gradeLevels as $value => $label)
                                <option value="{{ $value }}" {{ ($selectedGradeLevel ?? 'all') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Section</label>
                        <select name="section_id" class="form-select" onchange="this.form.submit()">
                            @foreach($sections as $section)
                                <option value="{{ $section['id'] }}" {{ ($selectedSectionId ?? 'all') == $section['id'] ? 'selected' : '' }}>
                                    {{ $section['name'] }}
                                </option>
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
        @if($grades->count() > 0)
            <div class="card">
                <div class="card-body p-0">
                    <div class="alert alert-info m-3 mb-2" role="alert">
                        <i class="fas fa-info-circle"></i>
                        INC or 4.00 grade can only be edited within one (1) academic year; otherwise, grade will be marked 5.00
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <tr>
                                    <th style="padding: 12px;">Subject Code</th>
                                    <th>Subject Title</th>
                                    <th class="text-center">Section</th>
                                    <th class="text-center">Schedule Code</th>
                                    <th class="text-center">Lec Unit</th>
                                    <th class="text-center">Lab Unit</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Completion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalLec = 0;
                                    $totalLab = 0;
                                    $totalUnits = 0;
                                @endphp
                                @foreach($grades as $grade)
                                    @php
                                        $lecUnit = $grade['lec_unit'] ?? 0;
                                        $labUnit = $grade['lab_unit'] ?? 0;
                                        $total = $lecUnit + $labUnit;
                                        $totalLec += $lecUnit;
                                        $totalLab += $labUnit;
                                        $totalUnits += $total;
                                        
                                        // Determine grade display based on selected term
                                        $displayGrade = null;
                                        if($selectedTerm === 'midterm' || $selectedTerm === 'finals') {
                                            $displayGrade = $selectedTerm === 'midterm' ? $grade['fq_grade'] : $grade['sq_grade'];
                                        } else {
                                            $displayGrade = $grade['f_grade'] ?? $grade['a_grade'];
                                        }
                                    @endphp
                                    <tr>
                                        <td style="padding: 10px;">{{ $grade['subject_code'] }}</td>
                                        <td>{{ $grade['subject_name'] }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-white">{{ $grade['section'] ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-center">{{ $grade['schedule_code'] ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $lecUnit }}</td>
                                        <td class="text-center">{{ $labUnit }}</td>
                                        <td class="text-center"><strong>{{ $total }}</strong></td>
                                        <td class="text-center">
                                            @if($displayGrade)
                                                <span class="badge {{ $displayGrade >= 90 ? 'bg-success' : ($displayGrade >= 80 ? 'bg-primary' : ($displayGrade >= 75 ? 'bg-warning text-dark' : 'bg-danger')) }}" style="font-size: 14px; padding: 6px 12px;">
                                                    {{ number_format($displayGrade, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted">—</span>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6;">
                                    <td colspan="4" class="text-end" style="padding: 12px;">Total:</td>
                                    <td class="text-center">{{ $totalLec }}</td>
                                    <td class="text-center">{{ $totalLab }}</td>
                                    <td class="text-center">{{ $totalUnits }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($average)
                        <div class="p-3 text-end" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                            <span class="badge bg-warning text-dark" style="font-size: 16px; padding: 8px 20px;">
                                Average: {{ number_format($average, 2) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No grades available for this selection.
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
