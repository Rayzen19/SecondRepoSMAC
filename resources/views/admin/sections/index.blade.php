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

@section('content')
<div class="content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
                {{ $sections->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
