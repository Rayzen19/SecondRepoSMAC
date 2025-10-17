@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Subject</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Subject</li>
                <li class="breadcrumb-item active" aria-current="page">Subject List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Subject</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <form method="GET" action="{{ route('admin.subjects.index') }}" id="filterForm">
                        <select name="strand_id" class="form-select" id="strandFilter" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Strands</option>
                            @foreach($strands as $strand)
                                <option value="{{ $strand->id }}" {{ request('strand_id') == $strand->id ? 'selected' : '' }}>
                                    {{ $strand->code }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-8 text-end">
                    @if(request('strand_id'))
                        <span class="text-muted me-2">
                            Showing subjects for: <strong>{{ $strands->firstWhere('id', request('strand_id'))->code ?? '' }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                
                {{-- Core Curriculum Subjects --}}
                @if($coreSubjects->count() > 0)
                <div class="px-4 py-3 bg-light border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-book me-2"></i>Senior High School Core Curriculum Subjects
                    </h5>
                    <small class="text-muted">{{ $coreSubjects->count() }} subjects</small>
                </div>
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Strand(s)</th>
                            <th>Grade Level</th>
                            <th>Semester</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coreSubjects as $subject)
                        <tr>
                            <td class="font-monospace"><strong>{{ $subject->code }}</strong></td>
                            <td>{{ $subject->name }}</td>
                            <td>
                                @if($subject->strandSubjects && $subject->strandSubjects->count() > 0)
                                    @foreach($subject->strandSubjects as $ss)
                                        <span class="badge bg-primary me-1">{{ $ss->strand->code ?? 'N/A' }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Not linked</span>
                                @endif
                            </td>
                            <td>
                                @if($subject->strandSubjects && $subject->strandSubjects->count() > 0)
                                    @php
                                        $gradeLevels = $subject->strandSubjects->pluck('grade_level')->filter()->unique()->sort()->values();
                                    @endphp
                                    @if($gradeLevels->count() > 0)
                                        @foreach($gradeLevels as $level)
                                            <span class="badge bg-success me-1">Grade {{ $level }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $subject->semester }}</span></td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="{{ route('admin.subjects.teachers', $subject) }}" class="me-2" title="View Teachers"><i class="ti ti-users"></i></a>
                                    <a href="{{ route('admin.subjects.show', $subject) }}" class="me-2"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="me-2"><i class="ti ti-edit"></i></a>
                                    <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" style="vertical-align: baseline;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                {{-- Applied Track Subjects --}}
                @if($appliedSubjects->count() > 0)
                <div class="px-4 py-3 bg-light border-bottom">
                    <h5 class="mb-0 text-info">
                        <i class="ti ti-briefcase me-2"></i>Senior High School Applied Track Subjects
                    </h5>
                    <small class="text-muted">{{ $appliedSubjects->count() }} subjects</small>
                </div>
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Strand(s)</th>
                            <th>Grade Level</th>
                            <th>Semester</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appliedSubjects as $subject)
                        <tr>
                            <td class="font-monospace"><strong>{{ $subject->code }}</strong></td>
                            <td>{{ $subject->name }}</td>
                            <td>
                                @if($subject->strandSubjects && $subject->strandSubjects->count() > 0)
                                    @foreach($subject->strandSubjects as $ss)
                                        <span class="badge bg-primary me-1">{{ $ss->strand->code ?? 'N/A' }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Not linked</span>
                                @endif
                            </td>
                            <td>
                                @if($subject->strandSubjects && $subject->strandSubjects->count() > 0)
                                    @php
                                        $gradeLevels = $subject->strandSubjects->pluck('grade_level')->filter()->unique()->sort()->values();
                                    @endphp
                                    @if($gradeLevels->count() > 0)
                                        @foreach($gradeLevels as $level)
                                            <span class="badge bg-success me-1">Grade {{ $level }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $subject->semester }}</span></td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="{{ route('admin.subjects.teachers', $subject) }}" class="me-2" title="View Teachers"><i class="ti ti-users"></i></a>
                                    <a href="{{ route('admin.subjects.show', $subject) }}" class="me-2"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="me-2"><i class="ti ti-edit"></i></a>
                                    <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" style="vertical-align: baseline;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                {{-- Specialized Subjects --}}
                @if($specializedSubjects->count() > 0)
                <div class="px-4 py-3 bg-light border-bottom">
                    <h5 class="mb-0 text-warning">
                        <i class="ti ti-star me-2"></i>Senior High School Specialized Subjects
                    </h5>
                    <small class="text-muted">{{ $specializedSubjects->count() }} subjects</small>
                </div>
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Strand(s)</th>
                            <th>Grade Level</th>
                            <th>Semester</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($specializedSubjects as $subject)
                        <tr>
                            <td class="font-monospace"><strong>{{ $subject->code }}</strong></td>
                            <td>{{ $subject->name }}</td>
                            <td>
                                @if($subject->strandSubjects && $subject->strandSubjects->count() > 0)
                                    @foreach($subject->strandSubjects as $ss)
                                        <span class="badge bg-primary me-1">{{ $ss->strand->code ?? 'N/A' }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Not linked</span>
                                @endif
                            </td>
                            <td>
                                @if($subject->strandSubjects && $subject->strandSubjects->count() > 0)
                                    @php
                                        $gradeLevels = $subject->strandSubjects->pluck('grade_level')->filter()->unique()->sort()->values();
                                    @endphp
                                    @if($gradeLevels->count() > 0)
                                        @foreach($gradeLevels as $level)
                                            <span class="badge bg-success me-1">Grade {{ $level }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $subject->semester }}</span></td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="{{ route('admin.subjects.teachers', $subject) }}" class="me-2" title="View Teachers"><i class="ti ti-users"></i></a>
                                    <a href="{{ route('admin.subjects.show', $subject) }}" class="me-2"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="me-2"><i class="ti ti-edit"></i></a>
                                    <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" style="vertical-align: baseline;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                {{-- Empty state --}}
                @if($coreSubjects->count() == 0 && $appliedSubjects->count() == 0 && $specializedSubjects->count() == 0)
                <div class="text-center py-5">
                    <i class="ti ti-books" style="font-size: 48px; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No subjects found</p>
                </div>
                @endif
                
            </div>
        </div>
    </div>
</div>
@endsection
