@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Create Academic Year</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('admin.academic-years.store') }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="e.g., 2025-2026"
                        required
                        maxlength="9"
                        pattern="^\d{4}-\d{4}$"
                        aria-describedby="nameHelp"
                    >
                    <div id="nameHelp" class="form-text small">Format: YYYY-YYYY (end year must be start year + 1)</div>
                    <div id="nameError" class="text-danger small mt-1"></div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const input = document.getElementById('name');

                            function maskYearRange() {
                                let v = input.value.replace(/\D/g, '');
                                if (v.length > 8) v = v.slice(0, 8);
                                if (v.length > 4) {
                                    v = v.slice(0, 4) + '-' + v.slice(4);
                                }
                                input.value = v;
                                validateYears();
                            }

                            function validateYears() {
                                const errEl = document.getElementById('nameError');
                                errEl.textContent = '';
                                input.setCustomValidity('');

                                const val = input.value;
                                if (!/^\d{4}-\d{4}$/.test(val)) {
                                    input.setCustomValidity('Invalid format');
                                    // don't show message immediately; show on blur
                                    return false;
                                }

                                const parts = val.split('-');
                                const y1 = parseInt(parts[0], 10);
                                const y2 = parseInt(parts[1], 10);

                                if (isNaN(y1) || isNaN(y2)) {
                                    input.setCustomValidity('Invalid year values');
                                    return false;
                                }

                                if (y2 !== y1 + 1) {
                                    input.setCustomValidity('End year must be start year + 1');
                                    errEl.textContent = 'End year must be start year + 1';
                                    return false;
                                }

                                if (y1 < 1900 || y1 > 2100) {
                                    input.setCustomValidity('Start year out of allowed range');
                                    errEl.textContent = 'Start year out of allowed range (1900-2100)';
                                    return false;
                                }

                                input.setCustomValidity('');
                                return true;
                            }

                            input.addEventListener('input', maskYearRange);
                            input.addEventListener('blur', function () {
                                // show message on blur if invalid
                                if (!validateYears() && input.validationMessage) {
                                    document.getElementById('nameError').textContent = input.validationMessage;
                                }
                            });

                            // prevent form submit when invalid
                            const form = input.closest('form');
                            if (form) {
                                form.addEventListener('submit', function (e) {
                                    if (!validateYears()) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                    }
                                });
                            }
                        });
                    </script>
                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Semester (optional)</label>
                    <select name="semester" class="form-select">
                        <option value="">Select Semester</option>
                        <option value="1st" @selected(old('semester')==='1st')>1st</option>
                        <option value="2nd" @selected(old('semester')==='2nd')>2nd</option>
                    </select>
                    @error('semester')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Academic Status <span class="text-danger">*</span></label>
                    <select name="academic_status" class="form-select" required>
                        <option value="pending" @selected(old('academic_status')==='pending')>Pending</option>
                        <option value="completed" @selected(old('academic_status')==='completed')>Completed</option>
                        <option value="ongoing enrollment" @selected(old('academic_status')==='ongoing enrollment')>Ongoing Enrollment</option>
                        <option value="ongoing school year" @selected(old('academic_status')==='ongoing school year')>Ongoing School Year</option>
                    </select>
                    @error('academic_status')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Set as Active</label>
                </div>

                <a href="{{ url()->previous() }}" class="btn btn-outline-light border me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection
