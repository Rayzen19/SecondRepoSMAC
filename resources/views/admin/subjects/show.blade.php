@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Subject</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Subject</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.subjects.index') }}">Subject Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Subject Details</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-warning d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
        <div class="mb-2 ms-2">
            <a href="{{ route('admin.strand-subjects.create', ['subject' => $subject->id]) }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add to Strand</a>
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
                        {{ strtoupper(substr($subject->name ?? '', 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-0">{{ $subject->name }} <small class="text-muted">({{ $subject->code }})</small></h4>
                        <div class="mt-1">
                            <span class="badge bg-secondary me-1">{{ $subject->units }} units</span>
                            <span class="badge bg-info text-dark me-1 text-white">{{ ucfirst($subject->type ?? '') }}</span>
                            <span class="badge bg-light text-muted">Semester: {{ $subject->semester }}</span>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-pills rounded-pill bg-light p-1" id="subjectTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 rounded-pill active d-flex align-items-center" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                            <i class="ti ti-info-circle me-2"></i>
                            General
                        </button>
                    </li>
                    <li class="nav-item ms-2" role="presentation">
                        <button class="nav-link px-4 rounded-pill d-flex align-items-center" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab" aria-controls="subjects" aria-selected="false">
                            <i class="ti ti-book me-2"></i>
                            Strands
                            <span class="badge bg-white text-muted ms-2">{{ $subject->strandSubjects->count() ?? 0 }}</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content pt-4" id="subjectTabContent">
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Code</label>
                                    <input type="text" class="form-control" value="{{ $subject->code }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" value="{{ $subject->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea rows="3" class="form-control" disabled>{{ $subject->description }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Units</label>
                                    <input type="text" class="form-control" value="{{ $subject->units }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($subject->type) }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Semester</label>
                                    <input type="text" class="form-control" value="{{ $subject->semester }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
                        @php $pivots = $subject->strandSubjects ?? collect(); @endphp

                        @if($pivots->isNotEmpty())
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Strand</th>
                                            <th>Semestral Period</th>
                                            <th>Written %</th>
                                            <th>Performance %</th>
                                            <th>Quarterly %</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pivots as $pivot)
                                            <tr>
                                            <td>{{ $pivot->strand?->code }} — {{ $pivot->strand?->name }}</td>
                                            <td>{{ $pivot->semestral_period }}</td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->written_works_percentage, 2), '0'), '.') }}%</td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->performance_tasks_percentage, 2), '0'), '.') }}%</td>
                                            <td>{{ rtrim(rtrim(number_format($pivot->quarterly_assessment_percentage, 2), '0'), '.') }}%</td>
                                            <td>
                                                <span class="badge bg-{{ $pivot->is_active ? 'success' : 'secondary' }}">{{ $pivot->is_active ? 'Active' : 'Inactive' }}</span>
                        <a href="{{ route('admin.strand-subjects.edit', $pivot->id) }}" class="btn btn-sm btn-outline-primary ms-2">Edit</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mt-3 mb-0">Not linked to any strands yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection