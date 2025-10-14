@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Strand Details</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.show', $adviser->academic_year_id) }}">Academic Year Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Strand Details</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.academic-year-strand-advisers.edit', $adviser) }}" class="btn btn-warning d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
        <div class="mb-2 ms-2">
            <a href="{{ route('admin.academic-years.show', $adviser->academic_year_id) }}#advisers" class="btn btn-secondary d-flex align-items-center"><i class="ti ti-arrow-left me-2"></i>Back</a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="ti ti-x"></i></button>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">General Information</h5>
        <div class="card-body p-5">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="form-label">Academic Year</label>
                        <input type="text" class="form-control" value="{{ $adviser->academicYear?->name ?? '—' }}" disabled>
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="mb-2">
                        <label class="form-label">Semester</label>
                        <input type="text" class="form-control" value="{{ $adviser->academicYear?->semester ?? '—' }}" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="form-label">Strand</label>
                        <input type="text" class="form-control" value="{{ $adviser->strand?->code }} — {{ $adviser->strand?->name }}" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="form-label">Adviser Teacher</label>
                        <input type="text" class="form-control" value="{{ $adviser->teacher?->employee_number }} — {{ $adviser->teacher?->first_name }} {{ $adviser->teacher?->last_name }}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Subjects</h5>
        <div class="card-body p-5">
            <div class="mb-3 d-flex justify-content-end">
                <a href="{{ route('admin.academic-year-strand-subjects.create', ['academic_year' => $adviser->academic_year_id, 'strand' => $adviser->strand_id, 'academic_year_strand_adviser_id' => $adviser->id]) }}" class="btn btn-primary"><i class="ti ti-book-plus me-2"></i>Add Subject</a>
            </div>

            @php $assignments = $assignments ?? collect(); @endphp
            @if($assignments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Written Works %</th>
                                <th>Performance Task %</th>
                                <th>Quarterly Assessment %</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $row)
                                <tr>
                                    <td>{{ $row->subject?->code }} — {{ $row->subject?->name }}</td>
                                    <td>{{ $row->teacher?->employee_number }} — {{ $row->teacher?->first_name }} {{ $row->teacher?->last_name }}</td>
                                    <td>{{ rtrim(rtrim(number_format($row->written_works_percentage, 2), '0'), '.') }}%</td>
                                    <td>{{ rtrim(rtrim(number_format($row->performance_tasks_percentage, 2), '0'), '.') }}%</td>
                                    <td>{{ rtrim(rtrim(number_format($row->quarterly_assessment_percentage, 2), '0'), '.') }}%</td>
                                    <td>
                                        <a href="{{ route('admin.academic-year-strand-subjects.edit', $row) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mb-0 text-center">No subjects assigned to this strand for the academic year.</p>
            @endif
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Sections</h5>
        <div class="card-body p-5">
            <div class="mb-3 d-flex justify-content-end">
                <a href="{{ route('admin.academic-year-strand-sections.create', ['academic_year' => $adviser->academic_year_id, 'strand' => $adviser->strand_id, 'academic_year_strand_adviser_id' => $adviser->id]) }}" class="btn btn-primary"><i class="ti ti-plus me-2"></i>Assign Section</a>
            </div>

            @if($sections->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Section</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sections as $row)
                                <tr>
                                    <td>{{ $row->section?->name }}</td>
                                    <td>{{ $row->section?->grade }}</td>
                                    <td>
                                        @if($row->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.academic-year-strand-sections.edit', $row) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mb-0 text-center">No sections assigned to this strand for the academic year.</p>
            @endif
        </div>
    </div>

</div>
@endsection
