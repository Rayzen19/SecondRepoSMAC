@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Create Announcement</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.announcements.index') }}">Announcements</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">New Announcement</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image (Optional)</label>
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-tabs mb-3" id="imageTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">
                                                <i class="ti ti-upload me-1"></i>Upload Image
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="url-tab" data-bs-toggle="tab" data-bs-target="#url" type="button" role="tab">
                                                <i class="ti ti-link me-1"></i>Image URL
                                            </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="imageTabContent">
                                        <div class="tab-pane fade show active" id="upload" role="tabpanel">
                                            <input type="file" class="form-control @error('image_file') is-invalid @enderror" 
                                                   id="image_file" name="image_file" accept="image/*" onchange="previewImage(event)">
                                            <small class="text-muted">Supported: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                                            @error('image_file')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div id="imagePreview" class="mt-3" style="display: none;">
                                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="url" role="tabpanel">
                                            <input type="url" class="form-control @error('image_url') is-invalid @enderror" 
                                                   id="image_url" name="image_url" value="{{ old('image_url') }}" 
                                                   placeholder="https://example.com/image.jpg">
                                            <small class="text-muted">Enter a URL for an image from the web</small>
                                            @error('image_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="published_at" class="form-label">Publish Date (Optional)</label>
                                <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                       id="published_at" name="published_at" value="{{ old('published_at') }}">
                                <small class="text-muted">Leave empty to publish immediately</small>
                                @error('published_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                <small class="text-muted">Leave empty for no expiration</small>
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (visible on landing page)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>Create Announcement
                            </button>
                            <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x me-1"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
