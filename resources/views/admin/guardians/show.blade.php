@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Guardian</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Guardian</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.guardians.index') }}">Guardian Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Guardian Details</li>
            </ol>
        </nav>
    </div>
     <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.guardians.edit', $guardian) }}" class="btn btn-warning d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')

<div class="content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    @endif

    <div class="card">
        <div class="card-body p-5">
            <div class="max-w-2xl mx-auto bg-white p-6">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Guardian Number</label>
                            <input type="text" class="form-control" value="{{ $guardian->guardian_number }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{ $guardian->last_name }}, {{ $guardian->first_name }} {{ $guardian->middle_name }} {{ $guardian->suffix }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" value="{{ $guardian->email }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Mobile</label>
                            <input type="text" class="form-control" value="{{ $guardian->mobile_number }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" value="{{ $guardian->address }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ ucfirst($guardian->status) }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Last Updated</label>
                            <input type="text" class="form-control" value="{{ $guardian->updated_at }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
