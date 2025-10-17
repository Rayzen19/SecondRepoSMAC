@extends('teacher.components.template')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"><i class="ti ti-building me-2"></i>All Sections</h4>
                    <p class="text-muted mb-0">Organized by Strand and Grade Level</p>
                </div>
            </div>
        </div>
    </div>

    @if(!$currentAcademicYear)
        <div class="alert alert-info">
            <i class="ti ti-info-circle me-2"></i>No active academic year found.
        </div>
    @elseif($groupedSections->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="display-4 text-muted mb-3"><i class="ti ti-building-community"></i></div>
                <h5 class="mb-2">No Sections Available</h5>
                <p class="text-muted mb-0">There are no sections configured for the current academic year.</p>
            </div>
        </div>
    @else
        @foreach($groupedSections as $strandCode => $strandData)
            <div class="card mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ti ti-certificate me-2"></i>
                            {{ $strandData['strand_code'] }} - {{ $strandData['strand_name'] }}
                        </h5>
                        @php
                            $totalStudents = 0;
                            foreach ($strandData['grades'] as $sections) {
                                $totalStudents += $sections->sum('students_count');
                            }
                        @endphp
                        <span class="badge bg-white text-dark">{{ $totalStudents }} Total Students</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @foreach($strandData['grades'] as $grade => $sections)
                        <div class="border-bottom">
                            <div class="p-3 bg-light">
                                <h6 class="mb-0 text-primary">
                                    <i class="ti ti-school me-2"></i>{{ $grade }}
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 120px;">Section</th>
                                            <th>Adviser</th>
                                            <th style="width: 150px;">Students</th>
                                            <th style="width: 120px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sections as $section)
                                            <tr class="{{ $section['is_my_section'] ? 'table-success' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            {{ $section['section_name'] }}
                                                        </div>
                                                        <span class="fw-bold">Section {{ $section['section_name'] }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        {{ $section['adviser_name'] }}
                                                        @if($section['is_my_section'])
                                                            <span class="badge bg-success-subtle text-success ms-2">
                                                                <i class="ti ti-check me-1"></i>You
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary" style="font-size: 0.9rem;">
                                                        <i class="ti ti-users me-1"></i>{{ $section['students_count'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($section['is_my_section'])
                                                        <a href="{{ route('teacher.students.section', $section['assignment_id']) }}" class="btn btn-sm btn-primary">
                                                            <i class="ti ti-eye me-1"></i>View
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Adviser Only</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>

<style>
.card-header h5 {
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
.table-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}
.table-success:hover {
    background-color: rgba(25, 135, 84, 0.15) !important;
}
</style>
@endsection
