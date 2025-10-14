@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4"><i class="ti ti-category me-2"></i>Assessment Types</h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Assessment Types</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.assessment-types.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>New Type</a>
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
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Key</th>
                        <th>Active</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td><code>{{ $row->key }}</code></td>
                            <td>
                                <span class="badge bg-{{ $row->is_active ? 'success':'secondary' }}">{{ $row->is_active ? 'Yes':'No' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.assessment-types.show', $row) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('admin.assessment-types.edit', $row) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No assessment types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
