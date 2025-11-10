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
                <li class="breadcrumb-item active" aria-current="page">Guardian List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.guardians.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Guardian</a>
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

    <div class="card">
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Guardian Number</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guardians as $guardian)
                        <tr>
                            <td class="font-monospace">{{ $guardian->guardian_number }}</td>
                            <td>{{ $guardian->last_name }}, {{ $guardian->first_name }} {{ $guardian->middle_name }}</td>
                            <td>{{ $guardian->email }}</td>
                            <td>{{ $guardian->mobile_number }}</td>
                            <td><span class="badge bg-{{ $guardian->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($guardian->status) }}</span></td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="{{ route('admin.guardians.show', $guardian) }}" class="me-2" title="View"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.guardians.edit', $guardian) }}" class="me-2" title="Edit"><i class="ti ti-edit"></i></a>
                                    <form action="{{ route('admin.guardians.destroy', $guardian) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this guardian? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link p-0 text-danger" title="Delete" style="border: none; background: none;">
                                            <i class="ti ti-trash"></i>
                                        </button>
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
