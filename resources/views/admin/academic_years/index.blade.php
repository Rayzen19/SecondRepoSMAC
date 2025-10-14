@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Academic Years</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Academic Years</li>
            </ol>
        </nav>
    </div>
    <div class="mb-2">
        <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>Add Academic Year</a>
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
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Academic Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($years as $year)
                            <tr>
                                <td>{{ $year->name }}</td>
                                <td>{{ $year->semester ?? '—' }}</td>
                                <td><span class="badge bg-{{ $year->is_active ? 'success' : 'secondary' }}">{{ $year->is_active ? 'Active' : 'Inactive' }}</span></td>
                                @php
                                    $status = $year->academic_status ?? null;
                                    $label = $status ? \Illuminate\Support\Str::title($status) : '—';
                                    // Map academic_status enum values to badge colors
                                    $map = [
                                        'pending' => 'secondary',
                                        'completed' => 'success',
                                        'ongoing enrollment' => 'warning',
                                        'ongoing school year' => 'primary',
                                    ];
                                    $badge = 'secondary';
                                    if ($status) {
                                        $badge = $map[strtolower($status)] ?? 'secondary';
                                    }
                                @endphp
                                <td><span class="badge bg-{{ $badge }} px-3 py-2">{{ $label }}</span></td>
                                <td>{{ $year->created_at }}</td>
                                <td>
                                    <a href="{{ route('admin.academic-years.show', $year) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    <a href="{{ route('admin.academic-years.edit', $year) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">No academic years yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $years->links() }}</div>
        </div>
    </div>
</div>
@endsection
