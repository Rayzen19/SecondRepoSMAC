@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Strand</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Strand</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.strands.index') }}">Strand Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Strand Details</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.strands.edit', $strand) }}" class="btn btn-warning d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
          <div class="mb-2 ms-2">
            <a href="{{ route('admin.strand-subjects.create', ['strand' => $strand->id]) }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add to Subject</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    @endif
    <div class="card">
        <div class="card-body p-5">
            <div class="max-w-2xl mx-auto bg-white p-6">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;font-size:20px;">
                        {{ strtoupper(substr($strand->name ?? $strand->code ?? 'S', 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-0">{{ $strand->name }} <small class="text-muted">({{ $strand->code }})</small></h4>
                    </div>
                </div>

                <ul class="nav nav-pills rounded-pill bg-light p-1" id="strandTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 rounded-pill active d-flex align-items-center" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                            <i class="ti ti-info-circle me-2"></i>
                            General
                        </button>
                    </li>
                    <li class="nav-item ms-2" role="presentation">
                        <button class="nav-link px-4 rounded-pill d-flex align-items-center" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab" aria-controls="subjects" aria-selected="false">
                            <i class="ti ti-book me-2"></i>
                            Subjects
                            <span class="badge bg-white text-muted ms-2">{{ ($strand->strandSubjects?->count()) ?? 0 }}</span>
                        </button>
                    </li>
                    <li class="nav-item ms-2" role="presentation">
                        <button class="nav-link px-4 rounded-pill d-flex align-items-center" id="first-tab" data-bs-toggle="tab" data-bs-target="#first" type="button" role="tab" aria-controls="first" aria-selected="false">
                            <i class="ti ti-book me-2"></i>
                            1st Semester
                            <span class="badge bg-white text-muted ms-2">{{ ($strand->strandSubjects?->where('semestral_period', '1st')->count()) ?? 0 }}</span>
                        </button>
                    </li>
                    <li class="nav-item ms-2" role="presentation">
                        <button class="nav-link px-4 rounded-pill d-flex align-items-center" id="second-tab" data-bs-toggle="tab" data-bs-target="#second" type="button" role="tab" aria-controls="second" aria-selected="false">
                            <i class="ti ti-book-2 me-2"></i>
                            2nd Semester
                            <span class="badge bg-white text-muted ms-2">{{ ($strand->strandSubjects?->where('semestral_period', '2nd')->count()) ?? 0 }}</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content pt-4" id="strandTabContent">
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Code</label>
                                    <input type="text" class="form-control" value="{{ $strand->code }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" value="{{ $strand->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div>
                                        <span class="badge bg-{{ $strand->is_active ? 'success' : 'secondary' }}">{{ $strand->is_active ? 'Active' : 'Inactive' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $allSubjects = $strand->strandSubjects ?? collect();
                        $firstSem = $allSubjects->where('semestral_period', '1st');
                        $secondSem = $allSubjects->where('semestral_period', '2nd');
                    @endphp

                    <!-- New Subjects Tab with Grade Filter -->
                    <div class="tab-pane fade" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
                        <div class="mb-3">
                            <label for="gradeFilter" class="form-label">Filter by Grade Level:</label>
                            <select id="gradeFilter" class="form-select" style="max-width: 200px;">
                                <option value="all">All Grades</option>
                                <option value="11">Grade 11</option>
                                <option value="12">Grade 12</option>
                            </select>
                        </div>
                        
                        @if($allSubjects->isNotEmpty())
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-bordered" id="allSubjectsTable">
                                    <thead>
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Name</th>
                                        <th>Grade Level</th>
                                        <th>Semester</th>
                                        <th>Written %</th>
                                        <th>Performance %</th>
                                        <th>Quarterly %</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($allSubjects as $pivot)
                                        <tr data-grade="{{ $pivot->grade_level ?? 'unknown' }}">
                                            <td class="font-monospace">{{ $pivot->subject?->code }}</td>
                                            <td>{{ $pivot->subject?->name }}</td>
                                            <td>
                                                <span class="badge bg-info">Grade {{ $pivot->grade_level ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $pivot->semestral_period == '1st' ? 'primary' : 'success' }}">
                                                    {{ $pivot->semestral_period }} Semester
                                                </span>
                                            </td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->written_works_percentage, 2), '0'), '.') }}%</td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->performance_tasks_percentage, 2), '0'), '.') }}%</td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->quarterly_assessment_percentage, 2), '0'), '.') }}%</td>
                                            <td>
                                                <span class="badge bg-{{ $pivot->is_active ? 'success' : 'secondary' }}">{{ $pivot->is_active ? 'Active' : 'Inactive' }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.strand-subjects.edit', ['strandSubject' => $pivot->id, 'return_to' => 'strand']) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.strand-subjects.destroy', $pivot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this subject from the strand?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mt-3 mb-0">No subjects linked to this strand yet.</p>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="first" role="tabpanel" aria-labelledby="first-tab">
                        @if($firstSem->isNotEmpty())
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Written %</th>
                                        <th>Performance %</th>
                                        <th>Quarterly %</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($firstSem as $pivot)
                                        <tr>
                                            <td>
                                                {{ $pivot->subject?->code }} — {{ $pivot->subject?->name }}
                                            </td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->written_works_percentage, 2), '0'), '.') }}%</td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->performance_tasks_percentage, 2), '0'), '.') }}%</td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->quarterly_assessment_percentage, 2), '0'), '.') }}%</td>
                                            <td>
                                                <span class="badge bg-{{ $pivot->is_active ? 'success' : 'secondary' }}">{{ $pivot->is_active ? 'Active' : 'Inactive' }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.strand-subjects.edit', ['strandSubject' => $pivot->id, 'return_to' => 'strand']) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.strand-subjects.destroy', $pivot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this subject from the strand?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mt-3 mb-0">No subjects linked for 1st Semester.</p>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="second" role="tabpanel" aria-labelledby="second-tab">
                        @if($secondSem->isNotEmpty())
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Written %</th>
                                        <th>Performance %</th>
                                        <th>Quarterly %</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($secondSem as $pivot)
                                        <tr>
                                            <td>
                                                {{ $pivot->subject?->code }} — {{ $pivot->subject?->name }}
                                            </td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->written_works_percentage, 2), '0'), '.') }}%</td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->performance_tasks_percentage, 2), '0'), '.') }}%</td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->quarterly_assessment_percentage, 2), '0'), '.') }}%</td>
                                            <td>
                                                <span class="badge bg-{{ $pivot->is_active ? 'success' : 'secondary' }}">{{ $pivot->is_active ? 'Active' : 'Inactive' }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.strand-subjects.edit', ['strandSubject' => $pivot->id, 'return_to' => 'strand']) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.strand-subjects.destroy', $pivot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this subject from the strand?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mt-3 mb-0">No subjects linked for 2nd Semester.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gradeFilter = document.getElementById('gradeFilter');
        const tableRows = document.querySelectorAll('#allSubjectsTable tbody tr');
        
        if (gradeFilter && tableRows.length > 0) {
            gradeFilter.addEventListener('change', function() {
                const selectedGrade = this.value;
                
                tableRows.forEach(row => {
                    const rowGrade = row.getAttribute('data-grade');
                    
                    if (selectedGrade === 'all') {
                        row.style.display = '';
                    } else if (rowGrade === selectedGrade) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection
