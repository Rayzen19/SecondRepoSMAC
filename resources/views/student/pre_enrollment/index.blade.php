@extends('student.components.template')

@section('title', 'Pre-Enrollment')

@push('head')
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-0">Pre-Enrollment for Next Academic Year</h1>
                    <p class="text-muted mb-0">Submit your enrollment preferences for the upcoming school year</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Current Enrollment Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="ti ti-info-circle me-2"></i>Your Current Enrollment</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Academic Year:</strong>
                            <p class="mb-0">{{ $currentAcademicYear->display_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Grade Level:</strong>
                            <p class="mb-0">{{ $currentEnrollment->academicYearStrandSection?->section?->grade ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Strand:</strong>
                            <p class="mb-0">{{ $currentEnrollment->strand?->code ?? 'N/A' }} - {{ $currentEnrollment->strand?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Section:</strong>
                            <p class="mb-0">{{ $currentEnrollment->academicYearStrandSection?->section?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($existingPreEnrollment)
        <!-- Existing Pre-Enrollment Submission -->
        <div class="row">
            <div class="col-12">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="ti ti-check-circle me-2"></i>Your Pre-Enrollment Submission</h5>
                    </div>
                    <div class="card-body">
                        @if($existingPreEnrollment->status === 'pending')
                            <div class="alert alert-warning mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-clock" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1"><strong>For Approval</strong></h4>
                                        <p class="mb-0">Your pre-enrollment application has been submitted successfully and is currently awaiting administrator review. You will be notified once your application has been processed.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($existingPreEnrollment->status === 'approved')
                            <div class="alert alert-success mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-circle-check" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1"><strong>You Are Already Enrolled</strong></h4>
                                        <p class="mb-0">Congratulations! Your pre-enrollment application has been approved. You are now enrolled for the next academic year.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($existingPreEnrollment->status === 'enrolled')
                            <div class="alert alert-success mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-circle-check" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1"><strong>You Are Already Enrolled</strong></h4>
                                        <p class="mb-0">Congratulations! You have been successfully enrolled for the next academic year. Your enrollment has been processed and finalized.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($existingPreEnrollment->status === 'rejected')
                            <div class="alert alert-danger mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-circle-x" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1"><strong>Application Rejected</strong></h4>
                                        <p class="mb-0">Unfortunately, your pre-enrollment application has been rejected. Please contact the administrator for more information.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info mb-4">
                                <i class="ti ti-info-circle me-2"></i>
                                You have already submitted your pre-enrollment. Below are the details:
                            </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                <p class="mb-0">
                                    @if($existingPreEnrollment->status === 'pending')
                                        <span class="badge bg-warning">Pending Review</span>
                                    @elseif($existingPreEnrollment->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($existingPreEnrollment->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @elseif($existingPreEnrollment->status === 'enrolled')
                                        <span class="badge bg-primary">Enrolled</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <strong>Submitted On:</strong>
                                <p class="mb-0">{{ $existingPreEnrollment->submitted_at?->format('M d, Y h:i A') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Grade Level:</strong>
                                <p class="mb-0">{{ $existingPreEnrollment->grade_level }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Strand:</strong>
                                <p class="mb-0">{{ $existingPreEnrollment->strand?->code ?? 'N/A' }} - {{ $existingPreEnrollment->strand?->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Preferred Section:</strong>
                                <p class="mb-0">{{ $existingPreEnrollment->section?->name ?? 'No preference' }}</p>
                            </div>
                        </div>

                        @if($existingPreEnrollment->remarks)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Remarks:</strong>
                                    <p class="mb-0">{{ $existingPreEnrollment->remarks }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                                <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>

                        {{-- Student cancel button removed because cancellation is no longer allowed from the UI --}}
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Pre-Enrollment Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-file-check me-2"></i>Pre-Enrollment Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('student.pre-enrollment.store') }}" method="POST" id="preEnrollmentForm">
                            @csrf

                            <div class="alert alert-info mb-4">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Note:</strong> Please select your preferred grade level, strand, and section for the next academic year.
                                The section selection is optional and will be considered as a preference only. Each section has a maximum capacity of 30 students.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="grade_level" class="form-label">Grade Level <span class="text-danger">*</span></label>
                                    <select class="form-select @error('grade_level') is-invalid @enderror" 
                                            id="grade_level" 
                                            name="grade_level" 
                                            required>
                                        <option value="">Select Grade Level</option>
                                        <option value="G-12" {{ old('grade_level') === 'G-12' ? 'selected' : '' }}>Grade 12</option>
                                    </select>
                                    @error('grade_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="strand_id" class="form-label">Strand <span class="text-danger">*</span></label>
                                    <select class="form-select @error('strand_id') is-invalid @enderror" 
                                            id="strand_id" 
                                            name="strand_id" 
                                            required>
                                        <option value="">Select Strand</option>
                                        @foreach($strands as $strand)
                                            <option value="{{ $strand->id }}" {{ old('strand_id') == $strand->id ? 'selected' : '' }}>
                                                {{ $strand->code }} - {{ $strand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('strand_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="section_id" class="form-label">Preferred Section (Optional)</label>
                                    <select class="form-select @error('section_id') is-invalid @enderror" 
                                            id="section_id" 
                                            name="section_id">
                                        <option value="">No preference</option>
                                    </select>
                                    <small class="text-muted">Sections will load based on your grade level and strand selection. Only sections with available spots (max 30 students) are shown.</small>
                                    @error('section_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-warning mt-4">
                                <i class="ti ti-alert-triangle me-2"></i>
                                <strong>Important:</strong> Please review your selections carefully before submitting. 
                                You can only submit your pre-enrollment once. If you need to make changes, you will need to cancel and resubmit.
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>Submit Pre-Enrollment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeSelect = document.getElementById('grade_level');
    const strandSelect = document.getElementById('strand_id');
    const sectionSelect = document.getElementById('section_id');

    function loadSections() {
        const gradeLevel = gradeSelect.value;
        const strandId = strandSelect.value;

        if (!gradeLevel || !strandId) {
            sectionSelect.innerHTML = '<option value="">No preference</option>';
            return;
        }

        // Show loading
        sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
        sectionSelect.disabled = true;

        // Fetch sections
        fetch('{{ route("student.pre-enrollment.sections") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                grade_level: gradeLevel,
                strand_id: strandId
            })
        })
        .then(response => response.json())
        .then(data => {
            sectionSelect.innerHTML = '<option value="">No preference</option>';
            
            if (data.sections && data.sections.length > 0) {
                data.sections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.display;
                    sectionSelect.appendChild(option);
                });
            }
            
            sectionSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading sections:', error);
            sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
            sectionSelect.disabled = false;
        });
    }

    gradeSelect.addEventListener('change', loadSections);
    strandSelect.addEventListener('change', loadSections);

    // Load sections on page load if values are pre-selected
    if (gradeSelect.value && strandSelect.value) {
        loadSections();
    }
});
</script>
@endpush
@endsection
