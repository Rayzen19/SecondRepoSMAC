@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Teacher</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Teacher</li>
                <li class="breadcrumb-item active" aria-current="page">Teacher List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2 me-2">
            <button type="button" class="btn btn-outline-success d-flex align-items-center" onclick="exportToExcel()">
                <i class="ti ti-file-spreadsheet me-2"></i>Export to Excel
            </button>
        </div>
        <div class="mb-2">
            <a href="{{ route('admin.teachers.create') }}" class="btn bg-info d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Teacher</a>
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
                            <th>Emp #</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subjects Handled</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $teacher)
                        <tr>
                            <td class="font-monospace">{{ $teacher->employee_number }}</td>
                            <td>{{ $teacher->last_name }}, {{ $teacher->first_name }} {{ $teacher->middle_name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td>
                                @if($teacher->subjects->isNotEmpty())
                                    @foreach($teacher->subjects->take(3) as $subject)
                                        <span class="badge bg-info text-dark me-1">{{ $subject->name }}</span>
                                    @endforeach
                                    @if($teacher->subjects->count() > 3)
                                        <span class="badge bg-secondary">+{{ $teacher->subjects->count() - 3 }} more</span>
                                    @endif
                                @else
                                    <span class="text-muted small">No subjects</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $teacher->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($teacher->status) }}</span></td>
                            <td>
                                <div class="action-icon d-inline-flex align-items-center">
                                    <a href="{{ route('admin.teachers.show', $teacher) }}" class="me-2" title="View"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="me-2" title="Edit"><i class="ti ti-edit"></i></a>
                                    <a href="{{ route('admin.teachers.assignments', $teacher) }}" class="me-2" title="Assignment"><i class="ti ti-clipboard-text"></i></a>
                                    <a href="javascript:void(0);" class="text-danger" title="Delete" onclick="openDeleteModal('{{ route('admin.teachers.destroy', $teacher) }}', '{{ $teacher->first_name }} {{ $teacher->last_name }}')"><i class="ti ti-trash"></i></a>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pb-4">
                <h5 class="mb-3" id="deleteModalLabel">Are you sure you want to delete this teacher? This will also remove their login account.</h5>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="btn btn-warning px-4">OK</button>
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>
function openDeleteModal(deleteUrl, teacherName) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = deleteUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function exportToExcel() {
    // Get the table
    const table = document.querySelector('.datatable');
    
    // Clone the table to manipulate it
    const clonedTable = table.cloneNode(true);
    
    // Remove action column (last column) from header
    const headerRow = clonedTable.querySelector('thead tr');
    if (headerRow) {
        const lastHeaderCell = headerRow.querySelector('th:last-child');
        if (lastHeaderCell) lastHeaderCell.remove();
    }
    
    // Remove action column from all body rows
    const bodyRows = clonedTable.querySelectorAll('tbody tr');
    bodyRows.forEach(row => {
        const lastCell = row.querySelector('td:last-child');
        if (lastCell) lastCell.remove();
        
        // Clean up the subjects column - extract text from badges
        const subjectsCell = row.querySelectorAll('td')[3];
        if (subjectsCell) {
            const badges = subjectsCell.querySelectorAll('.badge');
            if (badges.length > 0) {
                const subjects = Array.from(badges).map(badge => badge.textContent.trim()).join(', ');
                subjectsCell.innerHTML = subjects;
            }
        }
        
        // Clean up status column - extract text from badge
        const statusCell = row.querySelectorAll('td')[4];
        if (statusCell) {
            const badge = statusCell.querySelector('.badge');
            if (badge) {
                statusCell.textContent = badge.textContent.trim();
            }
        }
    });
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(clonedTable);
    
    // Set column widths
    ws['!cols'] = [
        { wch: 15 },  // Emp #
        { wch: 30 },  // Name
        { wch: 30 },  // Email
        { wch: 40 },  // Subjects Handled
        { wch: 10 }   // Status
    ];
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Teachers');
    
    // Generate filename with current date
    const filename = `Teachers_List_${new Date().toISOString().split('T')[0]}.xlsx`;
    
    // Download the file
    XLSX.writeFile(wb, filename);
}
</script>
@endpush
