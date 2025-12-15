@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">System Settings</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Settings</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
        <div class="mb-2">
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="ti ti-arrow-left me-2"></i>Back to Teachers
            </a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <!-- Success/Error Messages -->
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

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ti ti-alert-circle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Teacher Assignment Limits Card -->
    <div class="card">
        <div class="card-header bg-light">
            <h4 class="card-title mb-0">
                <i class="ti ti-clipboard-text me-2"></i>Teacher Assignment Limits
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    @foreach($settings as $index => $setting)
                    <div class="col-md-6 mb-4">
                        <div class="card border">
                            <div class="card-body">
                                <label for="setting_{{ $setting->id }}" class="form-label fw-bold">
                                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                </label>
                                
                                @if($setting->description)
                                <p class="text-muted small mb-3">
                                    <i class="ti ti-info-circle me-1"></i>{{ $setting->description }}
                                </p>
                                @endif

                                @if($setting->type === 'boolean')
                                <div class="form-check form-switch">
                                    <input type="hidden" name="settings[{{ $index }}][key]" value="{{ $setting->key }}">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        id="setting_{{ $setting->id }}"
                                        name="settings[{{ $index }}][value]"
                                        value="1"
                                        {{ $setting->value == '1' ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="setting_{{ $setting->id }}">
                                        Enable
                                    </label>
                                </div>
                                @elseif($setting->type === 'integer')
                                <input type="hidden" name="settings[{{ $index }}][key]" value="{{ $setting->key }}">
                                <div class="input-group">
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="setting_{{ $setting->id }}"
                                        name="settings[{{ $index }}][value]"
                                        value="{{ old('settings.'.$index.'.value', $setting->value) }}"
                                        min="1"
                                        max="100"
                                        required
                                    >
                                    <span class="input-group-text">
                                        @if(str_contains($setting->key, 'per_section'))
                                        subjects
                                        @elseif(str_contains($setting->key, 'sections'))
                                        sections
                                        @else
                                        units
                                        @endif
                                    </span>
                                </div>
                                <small class="text-muted">Range: 1-100</small>
                                @else
                                <input type="hidden" name="settings[{{ $index }}][key]" value="{{ $setting->key }}">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="setting_{{ $setting->id }}"
                                    name="settings[{{ $index }}][value]"
                                    value="{{ old('settings.'.$index.'.value', $setting->value) }}"
                                    required
                                >
                                @endif

                                <div class="mt-3">
                                    <span class="badge bg-info">
                                        <i class="ti ti-code me-1"></i>{{ $setting->type }}
                                    </span>
                                    <span class="badge bg-secondary ms-2">
                                        Current: {{ $setting->value }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                        <i class="ti ti-x me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card border-primary">
        <div class="card-body">
            <h5 class="card-title text-primary">
                <i class="ti ti-info-circle me-2"></i>About These Settings
            </h5>
            <ul class="mb-0">
                <li><strong>Max Teacher Subjects Per Section:</strong> Controls how many subjects one teacher can be assigned to within a single section. This helps prevent teacher overload.</li>
                <li><strong>Max Teacher Sections:</strong> Limits the total number of different sections a teacher can handle across all subjects.</li>
                <li class="text-muted mt-2"><small><i class="ti ti-lock me-1"></i>These limits are enforced when creating new teacher assignments.</small></li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader me-2"></i>Saving...';
});
</script>
@endpush
