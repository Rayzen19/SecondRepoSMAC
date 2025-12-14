@extends('student.components.template')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Privacy Settings</h2>
                    <p class="text-muted mb-0">Manage who can access your academic information</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Guardian Access Settings Card -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-shield-lock me-2"></i>Guardian/Parent Access Control
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-info-circle fs-1 text-info"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-2">About Guardian Access</h6>
                                        <p class="text-muted mb-0">
                                            By default, your parents/guardians can view your grades and academic performance data. 
                                            You can control this access using the setting below. When disabled, your guardians 
                                            will not be able to see your grades, enhancements, or academic analytics in their portal.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <form action="{{ route('student.privacy.guardian-access.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Guardian Access Permission</label>
                                    
                                    <!-- Hidden field to ensure 0 is sent when unchecked -->
                                    <input type="hidden" name="allow_guardian_access" value="0">
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            role="switch" 
                                            id="allowGuardianAccess" 
                                            name="allow_guardian_access" 
                                            value="1"
                                            {{ $student->allow_guardian_access ? 'checked' : '' }}
                                            onchange="updateStatusText(this)"
                                        >
                                        <label class="form-check-label" for="allowGuardianAccess">
                                            <span id="statusText" class="fw-semibold {{ $student->allow_guardian_access ? 'text-success' : 'text-danger' }}">
                                                {{ $student->allow_guardian_access ? 'Enabled' : 'Disabled' }}
                                            </span>
                                            - Allow guardians to view my grades and enhancement
                                        </label>
                                    </div>

                                    <div class="alert {{ $student->allow_guardian_access ? 'alert-success' : 'alert-warning' }} d-flex align-items-center" id="statusAlert">
                                        <i class="ti {{ $student->allow_guardian_access ? 'ti-lock-open' : 'ti-lock' }} me-2 fs-4"></i>
                                        <div>
                                            <strong id="alertTitle">
                                                {{ $student->allow_guardian_access ? 'Guardian Access is Enabled' : 'Guardian Access is Disabled' }}
                                            </strong>
                                            <p class="mb-0 small" id="alertMessage">
                                                @if($student->allow_guardian_access)
                                                    Your guardians can currently view your grades, enhancements, and academic performance data.
                                                @else
                                                    Your guardians cannot view your grades, enhancements, or academic performance data. 
                                                    They will see a privacy message instead.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i>Save Changes
                                    </button>
                                    <a href="{{ route('student.dashboard') }}" class="btn btn-light">
                                        <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Information Sidebar -->
                <div class="col-lg-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="ti ti-help me-2"></i>Frequently Asked Questions
                            </h6>
                            
                            <div class="mb-3">
                                <strong class="d-block mb-1">What happens when I disable access?</strong>
                                <p class="small text-muted mb-0">
                                    Your guardians will see a privacy notice instead of your grades and enhancements. 
                                    They will know that you've chosen to keep this information private.
                                </p>
                            </div>

                            <div class="mb-3">
                                <strong class="d-block mb-1">Can I change this anytime?</strong>
                                <p class="small text-muted mb-0">
                                    Yes! You can enable or disable guardian access at any time from this page.
                                </p>
                            </div>

                            <div class="mb-3">
                                <strong class="d-block mb-1">Will teachers know about this?</strong>
                                <p class="small text-muted mb-0">
                                    No, this privacy setting only affects what your guardians can see. 
                                    Teachers and administrators can still access all your academic information.
                                </p>
                            </div>

                            <div class="mb-0">
                                <strong class="d-block mb-1">Is this recommended?</strong>
                                <p class="small text-muted mb-0">
                                    We encourage transparency with your guardians. However, this is your personal choice, 
                                    and we respect your privacy preferences.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body text-center">
                            <i class="ti ti-shield-check text-success" style="font-size: 3rem;"></i>
                            <h6 class="mt-3 mb-2">Your Privacy Matters</h6>
                            <p class="small text-muted mb-0">
                                You have full control over who can access your academic information.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatusText(checkbox) {
    const statusText = document.getElementById('statusText');
    const statusAlert = document.getElementById('statusAlert');
    const alertTitle = document.getElementById('alertTitle');
    const alertMessage = document.getElementById('alertMessage');
    
    if (checkbox.checked) {
        statusText.textContent = 'Enabled';
        statusText.className = 'fw-semibold text-success';
        
        statusAlert.className = 'alert alert-success d-flex align-items-center';
        statusAlert.querySelector('i').className = 'ti ti-lock-open me-2 fs-4';
        
        alertTitle.textContent = 'Guardian Access is Enabled';
        alertMessage.textContent = 'Your guardians can currently view your grades, enhancements, and academic performance data.';
    } else {
        statusText.textContent = 'Disabled';
        statusText.className = 'fw-semibold text-danger';
        
        statusAlert.className = 'alert alert-warning d-flex align-items-center';
        statusAlert.querySelector('i').className = 'ti ti-lock me-2 fs-4';
        
        alertTitle.textContent = 'Guardian Access is Disabled';
        alertMessage.textContent = 'Your guardians cannot view your grades, enhancements, or academic performance data. They will see a privacy message instead.';
    }
}
</script>
@endsection
