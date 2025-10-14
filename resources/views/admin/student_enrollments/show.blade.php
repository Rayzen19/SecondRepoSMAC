@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4">
                    <i class="ti ti-user-check me-2"></i>
                    Enrollment Details
                </h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.student-enrollments.index') }}">Enrollments</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.student-enrollments.edit', $studentEnrollment) }}" class="btn btn-warning d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
        <div class="mb-2 ms-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary d-flex align-items-center"><i class="ti ti-arrow-back-up me-2"></i>Back</a>
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
        <div class="card-body p-5">
            <dl class="row mb-0">
                <dt class="col-sm-3">Registration #</dt>
                <dd class="col-sm-9">{{ $studentEnrollment->registration_number }}</dd>

                <dt class="col-sm-3">Student</dt>
                <dd class="col-sm-9">{{ $studentEnrollment->student?->student_number }} — {{ $studentEnrollment->student?->name }}</dd>

                <dt class="col-sm-3">Academic Year</dt>
                <dd class="col-sm-9">{{ $studentEnrollment->academicYear?->display_name }}</dd>

                <dt class="col-sm-3">Strand</dt>
                <dd class="col-sm-9">{{ $studentEnrollment->strand?->code }} — {{ $studentEnrollment->strand?->name }}</dd>

                <dt class="col-sm-3">Section</dt>
                <dd class="col-sm-9">{{ $studentEnrollment->academicYearStrandSection?->section?->grade }} — {{ $studentEnrollment->academicYearStrandSection?->section?->name }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ ucfirst($studentEnrollment->status) }}</dd>
            </dl>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body p-5">
            <h5 class="mb-3">Subjects</h5>
            @php $subs = $studentEnrollment->subjectEnrollments ?? collect(); @endphp
            @if($subs->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>1st Qtr</th>
                                <th>2nd Qtr</th>
                                <th>Average</th>
                                <th>Final</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subs as $se)
                                @php $ays = $se->academicYearStrandSubject; @endphp
                                <tr>
                                    <td>{{ $ays?->subject?->code }} — {{ $ays?->subject?->name }}</td>
                                    <td>{{ $ays?->teacher?->last_name }}, {{ $ays?->teacher?->first_name }}</td>
                                    <td>{{ $se->fq_grade }}</td>
                                    <td>{{ $se->sq_grade }}</td>
                                    <td>{{ $se->a_grade }}</td>
                                    <td>{{ $se->f_grade }}</td>
                                    <td>{{ $se->remarks }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mb-0">No subject enrollments yet. Use the Sync action from the Academic Year page to create them.</p>
            @endif
        </div>
    </div>
</div>
@endsection
