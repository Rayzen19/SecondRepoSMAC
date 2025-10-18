@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Announcements</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Announcements</li>
                <li class="breadcrumb-item active" aria-current="page">Announcement List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
        <div class="mb-2">
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="ti ti-circle-plus me-2"></i>Add Announcement
            </a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Announcements</h5>
        </div>
        <div class="card-body p-0">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="custom-datatable-filter table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Published Date</th>
                            <th>Expires Date</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                        <tr>
                            <td>
                                <strong>{{ $announcement->title }}</strong>
                                @if($announcement->hasImage())
                                <i class="ti ti-photo text-info ms-1" title="Has image"></i>
                                @endif
                            </td>
                            <td>
                                @if($announcement->is_active)
                                    @if($announcement->isExpired())
                                        <span class="badge bg-secondary">Expired</span>
                                    @elseif($announcement->isPublished())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning">Scheduled</span>
                                    @endif
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($announcement->published_at)
                                    {{ $announcement->published_at->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-muted">Immediately</span>
                                @endif
                            </td>
                            <td>
                                @if($announcement->expires_at)
                                    {{ $announcement->expires_at->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-muted">No expiration</span>
                                @endif
                            </td>
                            <td>{{ $announcement->creator->name ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="ti ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="ti ti-speakerphone text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0">No announcements found.</p>
                                <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="ti ti-plus me-1"></i>Create your first announcement
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($announcements->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $announcements->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
