@extends('admin.components.template')

@section('breadcrumb')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">System Reports</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-12">
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.reports.export') }}" class="btn btn-success">
                    <i class="ti ti-file-download me-1"></i>
                    Export to Excel
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- System Summary Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">System Summary Statistics</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50%">Category</th>
                                <th width="50%" class="text-end">Total Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Total Number of Students</strong></td>
                                <td class="text-end">{{ number_format($totalStudents) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Number of Teachers</strong></td>
                                <td class="text-end">{{ number_format($totalTeachers) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Number of Guardians</strong></td>
                                <td class="text-end">{{ number_format($totalGuardians) }}</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Total System Users</strong></td>
                                <td class="text-end"><strong>{{ number_format($totalStudents + $totalTeachers + $totalGuardians) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Registration Report -->
<div class="row">
    <div class="col-md-12 col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Student Registration Per Month ({{ date('Y') }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Number of Students Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentsPerMonth as $month => $count)
                            <tr>
                                <td>{{ $month }}</td>
                                <td class="text-end">{{ number_format($count) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td><strong>Total for {{ date('Y') }}</strong></td>
                                <td class="text-end"><strong>{{ number_format(array_sum($studentsPerMonth)) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Strand Distribution Report -->
    <div class="col-md-12 col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Student Distribution Per Strand</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Strand</th>
                                <th class="text-end">Number of Students</th>
                                <th class="text-end">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalStrandStudents = array_sum($studentsPerStrand->toArray());
                            @endphp
                            @foreach($studentsPerStrand as $strand => $count)
                            <tr>
                                <td>{{ $strand }}</td>
                                <td class="text-end">{{ number_format($count) }}</td>
                                <td class="text-end">
                                    {{ $totalStrandStudents > 0 ? number_format(($count / $totalStrandStudents) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td><strong>Total Enrolled</strong></td>
                                <td class="text-end"><strong>{{ number_format($totalStrandStudents) }}</strong></td>
                                <td class="text-end"><strong>100%</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
