@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Attendance</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.attendance.index') }}">Attendance</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Add Attendance</li>
            </ol>
        </nav>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('admin.attendance.store') }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                <div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Student <span class="text-danger">*</span></label>
                                <select name="student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                                            {{ $student->student_number }} - {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" name="time" class="form-control" value="{{ old('time', date('H:i')) }}" required>
                                @error('time')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="">Select Status</option>
                                    <option value="present" @selected(old('status') == 'present')>Present</option>
                                    <option value="absent" @selected(old('status') == 'absent')>Absent</option>
                                    <option value="late" @selected(old('status') == 'late')>Late</option>
                                    <option value="excused" @selected(old('status') == 'excused')>Excused</option>
                                </select>
                                @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Academic Year</label>
                                <select name="academic_year_id" class="form-select">
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id || $year->is_active)>
                                            {{ $year->name }} @if($year->is_active) (Active) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="e.g., Mathematics">
                                @error('subject')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Assessment Type</label>
                                <input type="text" name="assessment_type" class="form-control" value="{{ old('assessment_type') }}" placeholder="e.g., Quiz, Exam">
                                @error('assessment_type')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" rows="3" class="form-control" placeholder="Additional notes or comments">{{ old('remarks') }}</textarea>
                                @error('remarks')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Note:</strong> Strand, year level, section, and semester information will be automatically populated based on the student's current enrollment.
                    </div>

                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
