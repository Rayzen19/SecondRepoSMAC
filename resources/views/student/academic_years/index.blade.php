@extends('student.components.template')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">My Academic Years</h4>
        <div class="text-muted small">Your enrollment history and current status</div>
    </div>
    <div></div>
</div>

@if($rows->isEmpty())
    <div class="card shadow-none border-0 bg-transparent">
        <div class="card-body text-center py-5">
            <div class="display-4 text-muted mb-3"><i class="ti ti-school"></i></div>
            <h5 class="mb-2">No enrollment records</h5>
            <p class="text-muted mb-0">When you are enrolled in an academic year, it will appear here.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Strand</th>
                            <th>Section</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $r)
                            <tr>
                                <td>{{ $r['display_name'] ?? $r['year'] ?? '—' }}</td>
                                <td>{{ $r['semester'] ?? '—' }}</td>
                                <td>{{ $r['strand'] ?? '—' }}</td>
                                <td>{{ $r['section'] ?? '—' }}</td>
                                <td>
                                    @php $st = strtolower($r['status'] ?? ''); @endphp
                                    @switch($st)
                                        @case('active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-secondary-subtle text-secondary">Completed</span>
                                            @break
                                        @case('dropped')
                                            <span class="badge bg-warning-subtle text-warning">Dropped</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-muted border">N/A</span>
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
