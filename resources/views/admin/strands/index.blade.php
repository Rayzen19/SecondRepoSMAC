@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Strand</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Strand</li>
                <li class="breadcrumb-item active" aria-current="page">Strand List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.strands.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Strand</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($strands as $strand)
                        <tr class="clickable-row" data-href="{{ route('admin.strands.show', $strand) }}" style="cursor: pointer;">
                            <td class="font-monospace">{{ $strand->code }}</td>
                            <td>{{ $strand->name }}</td>
                            <td>
                                <span class="badge bg-{{ $strand->is_active ? 'success' : 'secondary' }}">{{ $strand->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td>
                                <div class="action-icon d-inline-flex" onclick="event.stopPropagation();">
                                    <a href="{{ route('admin.strands.show', $strand) }}" class="me-2"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.strands.edit', $strand) }}" class="me-2"><i class="ti ti-edit"></i></a>
                                    <form action="{{ route('admin.strands.destroy', $strand) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this strand?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link p-0 text-danger" title="Delete">
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

<style>
    .clickable-row:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    
    .clickable-row:active {
        background-color: #e9ecef;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.clickable-row');
        rows.forEach(row => {
            row.addEventListener('click', function() {
                window.location.href = this.dataset.href;
            });
        });
    });
</script>
@endsection
