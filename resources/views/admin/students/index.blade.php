@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Student</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    Student
                </li>
                <li class="breadcrumb-item active" aria-current="page">Student List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Student</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="row">
        <!-- Total Student -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-dark rounded-circle"><i class="ti ti-users"></i></span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Total Students</p>
                            <h4>{{ $no_students }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Total Student -->

        <!-- Total Student -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-success rounded-circle"><i class="ti ti-user-share"></i></span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Active</p>
                            <h4>{{ $no_active_students }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Total Student -->

        <!-- Inactive Student -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-danger rounded-circle"><i class="ti ti-user-pause"></i></span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Dropped</p>
                            <h4>{{ $no_dropped_students }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Inactive Companies -->

        <!-- No of Student  -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-info rounded-circle"><i class="ti ti-user-plus"></i></span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">New Students ({{ date('Y') }})</p>
                            <h4>{{ $no_new_students }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /No of Student -->

    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Student</th>
                            <th>Guardian</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">

                                    <a href="#" class="avatar avatar-md" data-bs-toggle="modal" data-bs-target="#view_details">
                                        <img src="{{ $student->avatar ? asset($student->avatar) : asset('assets/img/users/user-32.jpg') }}" class="img-fluid rounded-circle" alt="img">
                                    </a>
                                    <div class="ms-2">
                                        <p class="text-dark mb-0">
                                            <a href="{{ route('admin.students.show', $student) }}">{{ $student->name }}</a> |
                                            <span class="fs-12"> {{ $student->student_number }} </span>
                                            @if(isset($student->is_new) && $student->is_new)
                                            <span class="badge bg-success text-white me-2">New</span>
                                            @endif
                                        </p>
                                        <span class="fs-12">{{ $student->contact ?? '' }} </span>
                                        <br>
                                        <span class="fs-12">{{ $student->address ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <p class="text-dark mb-0">
                                            {{ $student->guardian_name }}
                                        </p>
                                        <span class="fs-12">{{ $student->guardian_contact ?? '' }} | {{ $student->guardian_email ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="ms-2">
                                    <span class="fs-12">{{ $student->program ?? '' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($student->status == 'active') badge-success
                                    @elseif($student->status == 'graduated') badge-primary
                                    @elseif($student->status == 'dropped') badge-warning
                                    @else badge-secondary
                                    @endif
                                    d-inline-flex align-items-center badge-xs">
                                    <i class="ti ti-point-filled me-1"></i>{{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="{{ route('admin.students.edit', $student) }}" class="me-2"><i class="ti ti-edit"></i></a>
                                    <form action="{{ route('admin.students.destroy', $student) }}" method="POST" onsubmit="return confirm('Delete this student?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline text-danger"><i class="ti ti-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection