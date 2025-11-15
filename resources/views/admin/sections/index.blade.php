@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Grade & Section</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Grade & Section</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.sections.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>Add Section</a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Fix massive arrow overlay issue */
    .pagination * {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }
    
    .pagination {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
        gap: 0.25rem !important;
        list-style: none !important;
        padding-left: 0 !important;
    }
    
    .page-item {
        display: inline-block !important;
    }
    
    .page-link {
        position: relative !important;
        display: inline-block !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        text-decoration: none !important;
        background-color: #fff !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.25rem !important;
        color: #0d6efd !important;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out !important;
        width: auto !important;
        height: auto !important;
        max-width: none !important;
        max-height: none !important;
    }
    
    .page-link i,
    .page-link svg,
    .page-link::before,
    .page-link::after {
        display: none !important;
        content: none !important;
        width: 0 !important;
        height: 0 !important;
        font-size: 0 !important;
    }
    
    .page-link:hover {
        z-index: 2 !important;
        color: #0a58ca !important;
        background-color: #e9ecef !important;
        border-color: #dee2e6 !important;
    }
    
    .page-link:focus {
        z-index: 3 !important;
        color: #0a58ca !important;
        background-color: #e9ecef !important;
        outline: 0 !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
    
    .page-item.active .page-link {
        z-index: 3 !important;
        color: #fff !important;
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
    }
    
    .page-item.disabled .page-link {
        color: #6c757d !important;
        pointer-events: none !important;
        background-color: #fff !important;
        border-color: #dee2e6 !important;
        opacity: 0.65 !important;
    }
    
    /* Hide any icon classes that might be injected */
    .pagination [class*="ti-"],
    .pagination [class*="icon-"],
    .pagination [class*="fa-"] {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="ti ti-x"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="ti ti-x"></i></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-5">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Name</th>
                            <th>Strand</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                            <tr>
                                <td>{{ $section->grade }}</td>
                                <td>{{ $section->name }}</td>
                                <td>{{ $section->strand ? $section->strand->name : 'N/A' }}</td>
                                <td>{{ $section->created_at?->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $section->id }}">Delete</button>
                                    
                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="deleteModal{{ $section->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $section->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $section->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete section <strong>{{ $section->name }}</strong> ({{ $section->grade }})?
                                                    <br><br>
                                                    <span class="text-danger">This action cannot be undone.</span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No sections found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <nav aria-label="Page navigation">
                    @if ($sections->hasPages())
                        <ul class="pagination justify-content-center">
                            {{-- Previous Page Link --}}
                            @if ($sections->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $sections->previousPageUrl() }}" rel="prev">Previous</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($sections->links()->elements[0] as $page => $url)
                                @if ($page == $sections->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($sections->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $sections->nextPageUrl() }}" rel="next">Next</a>
                                </li>
                            @else
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">Next</span>
                                </li>
                            @endif
                        </ul>
                    @endif
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection
