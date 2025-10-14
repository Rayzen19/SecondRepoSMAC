@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4"><i class="ti ti-eye me-2"></i>Assessment Type</h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.assessment-types.index') }}">Assessment Types</a></li>
                <li class="breadcrumb-item active" aria-current="page">View</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.assessment-types.edit', $item) }}" class="btn btn-outline-primary d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
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
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Name</strong><div>{{ $item->name }}</div></div>
                <div class="col-md-4"><strong>Key</strong><div><code>{{ $item->key }}</code></div></div>
                <div class="col-md-4"><strong>Active</strong><div><span class="badge bg-{{ $item->is_active ? 'success':'secondary' }}">{{ $item->is_active ? 'Yes':'No' }}</span></div></div>
                <div class="col-12"><strong>Description</strong><div>{{ $item->description }}</div></div>
            </div>
        </div>
    </div>
</div>
@endsection
