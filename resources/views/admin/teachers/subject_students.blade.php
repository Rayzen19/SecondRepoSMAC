@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
    <h2 class="mb-1">Students • {{ data_get($assignment, 'subject.name', 'Subject') }}</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Teachers</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.teachers.index') }}">Teacher Lists</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.teachers.show', $teacher) }}">{{ $teacher->last_name }}, {{ $teacher->first_name }}</a>
                </li>
                <li class="breadcrumb-item active">Students</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">{{ data_get($assignment, 'strand.name', 'Strand') }} • {{ data_get($assignment, 'subject.name', 'Subject') }}</h4>
                    <div class="text-muted">Academic Year: {{ $academicYear->display_name ?? $academicYear->name }}</div>
                </div>
                <div class="col-md-4">
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="p-2 border rounded-3 text-center">
                                <div class="text-muted small">Total</div>
                                <div class="fs-5 fw-bold">{{ $total }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded-3 text-center">
                                <div class="text-muted small">Boys</div>
                                <div class="fs-5 fw-bold">{{ $male }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded-3 text-center">
                                <div class="text-muted small">Girls</div>
                                <div class="fs-5 fw-bold">{{ $female }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-all">All <span class="badge bg-light text-dark ms-1">{{ $total }}</span></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-boys">Boys <span class="badge bg-light text-dark ms-1">{{ $male }}</span></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-girls">Girls <span class="badge bg-light text-dark ms-1">{{ $female }}</span></a></li>
                </ul>
                <div>
                    <a href="{{ route('admin.teachers.subject-students.export', [$teacher, $academicYear, $assignment]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-all">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student #</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Section</th>
                                    <th>Reg. #</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $i => $row)
                                @php $stud = $row['student']; $sec = $row['section']; @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $stud->student_number }}</td>
                                    <td>{{ $stud->last_name }}, {{ $stud->first_name }}</td>
                                    <td><span class="badge bg-light text-dark">{{ ucfirst($stud->gender) }}</span></td>
                                    <td>{{ $sec->name ?? '—' }}</td>
                                    <td>{{ $row['registration_number'] ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-boys">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student #</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Section</th>
                                    <th>Reg. #</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $j = 1; @endphp
                                @foreach($students as $row)
                                @php $stud = $row['student']; $sec = $row['section']; @endphp
                                @if(strtolower($stud->gender ?? '') === 'male')
                                <tr>
                                    <td>{{ $j++ }}</td>
                                    <td>{{ $stud->student_number }}</td>
                                    <td>{{ $stud->last_name }}, {{ $stud->first_name }}</td>
                                    <td><span class="badge bg-light text-dark">{{ ucfirst($stud->gender) }}</span></td>
                                    <td>{{ $sec->name ?? '—' }}</td>
                                    <td>{{ $row['registration_number'] ?? '—' }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-girls">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student #</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Section</th>
                                    <th>Reg. #</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $k = 1; @endphp
                                @foreach($students as $row)
                                @php $stud = $row['student']; $sec = $row['section']; @endphp
                                @if(strtolower($stud->gender ?? '') === 'female')
                                <tr>
                                    <td>{{ $k++ }}</td>
                                    <td>{{ $stud->student_number }}</td>
                                    <td>{{ $stud->last_name }}, {{ $stud->first_name }}</td>
                                    <td><span class="badge bg-light text-dark">{{ ucfirst($stud->gender) }}</span></td>
                                    <td>{{ $sec->name ?? '—' }}</td>
                                    <td>{{ $row['registration_number'] ?? '—' }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
