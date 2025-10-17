@extends('admin.components.template')

@section('content')
<div class="container-fluid py-4">
	<div class="row mb-4">
		<div class="col-12">
			<h2 class="fw-bold  mb-3" style="color:#313131"><i class="ti ti-dashboard me-2" style="color:#313131"></i>Admin Dashboard</h2>
			<div class="row">
				   <!-- Students -->
				   <div class="col-lg-3 col-md-6 d-flex">
					   <div class="card flex-fill">
						   <div class="card-body d-flex align-items-center justify-content-between">
							   <div class="d-flex align-items-center overflow-hidden">
								   <div>
									   <span class="avatar avatar-lg bg-dark rounded-circle"><i class="ti ti-users"></i></span>
								   </div>
								   <div class="ms-2 overflow-hidden">
									   <p class="fs-12 fw-medium mb-1 text-truncate">Students</p>
									<h4 class="text-dark mb-0">{{ $studentsCount }}</h4>
								   </div>
							   </div>
						   </div>
					   </div>
				   </div>
				   <!-- Teachers -->
				   <div class="col-lg-3 col-md-6 d-flex">
					   <div class="card flex-fill">
						   <div class="card-body d-flex align-items-center justify-content-between">
							   <div class="d-flex align-items-center overflow-hidden">
								   <div>
									   <span class="avatar avatar-lg bg-success rounded-circle"><i class="ti ti-chalkboard"></i></span>
								   </div>
								   <div class="ms-2 overflow-hidden">
									   <p class="fs-12 fw-medium mb-1 text-truncate">Teachers</p>
									<h4 class="mb-0"style="color:#313131">{{ $teachersCount }}</h4>
								   </div>
							   </div>
						   </div>
					   </div>
				   </div>
				   <!-- Sections -->
				   <div class="col-lg-3 col-md-6 d-flex">
					   <div class="card flex-fill">
						   <div class="card-body d-flex align-items-center justify-content-between">
							   <div class="d-flex align-items-center overflow-hidden">
								   <div>
									   <span class="avatar avatar-lg bg-danger rounded-circle"><i class="ti ti-building"></i></span>
								   </div>
								   <div class="ms-2 overflow-hidden">
									   <p class="fs-12 fw-medium mb-1 text-truncate">Sections</p>
									<h4 class="mb-0" style="color:#313131">{{ $sectionsCount }}</h4>
								   </div>
							   </div>
						   </div>
				   </div>
				   </div>
				   <!-- Events -->
				   <div class="col-lg-3 col-md-6 d-flex">
					   <div class="card flex-fill">
						   <div class="card-body d-flex align-items-center justify-content-between">
							   <div class="d-flex align-items-center overflow-hidden">
								   <div>
									   <span class="avatar avatar-lg bg-info rounded-circle"><i class="ti ti-calendar-event"></i></span>
								   </div>
								   <div class="ms-2 overflow-hidden">
									   <p class="fs-12 fw-medium mb-1 text-truncate">Events</p>
									<h4 class="mb-0" style="color:#313131">{{ $eventsCount }}</h4>
								   </div>
							   </div>
						   </div>
					   </div>
				   </div>
			</div>
		</div>
	</div>
	
	<!-- Academic Performance Section -->
	<div class="row mb-4">
		<div class="col-12">
			<h4 class="fw-bold mb-3" style="color:#313131">
				<i class="ti ti-school me-2" style="color:#313131"></i>Academic Performance Overview
			</h4>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-8">
			<!-- Student Performance Analytics by Strand -->
			<div class="card shadow-sm rounded-4 mb-4">
				<div class="card-header bg-opacity-10 rounded-top-4">
					<h5 class="mb-0 fw-bold" style="color:#313131"><i class="ti ti-chart-bar me-2" style="color:#313131"></i>Performance by Strand</h5>
				</div>
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col-12 col-lg-7 mb-3 mb-lg-0">
							<!-- Placeholder for chart -->
							<div class="bg-light rounded-4 p-3 d-flex align-items-center justify-content-center w-100" style="min-height:180px;">
								<span class="text-muted">[Chart: Average Grades by Strand]</span>
							</div>
						</div>
						<div class="col-12 col-lg-5">
							<div class="d-flex flex-wrap gap-2 justify-content-between">
								<div class="flex-grow-1 flex-basis-0 min-w-0" style="min-width:160px; max-width:160px; width:160px;">
									<div class="d-flex justify-content-between align-items-center border" style="border-color:#0b2e13 !important; background-color:#d1fae5; border-radius:0.75rem; padding:0.5rem 1rem; min-width:160px; max-width:160px; width:160px;">
										<span class="fw-semibold" style="color:#313131">STEM</span>
										<span class="badge text-white" style="background-color:#0b2e13;">{{ $performance['STEM'] ?? 'N/A' }}</span>
									</div>
								</div>
								<div class="flex-grow-1 flex-basis-0 min-w-0" style="min-width:160px; max-width:160px; width:160px;">
									<div class="d-flex justify-content-between align-items-center border" style="border-color:#0b2e13 !important; background-color:#d1fae5; border-radius:0.75rem; padding:0.5rem 1rem; min-width:160px; max-width:160px; width:160px;">
										<span class="fw-semibold" style="color:#313131">ABM</span>
										<span class="badge text-white" style="background-color:#0b2e13;">{{ $performance['ABM'] ?? 'N/A' }}</span>
									</div>
								</div>
								<div class="flex-grow-1 flex-basis-0 min-w-0" style="min-width:160px; max-width:160px; width:160px;">
									<div class="d-flex justify-content-between align-items-center border" style="border-color:#0b2e13 !important; background-color:#d1fae5; border-radius:0.75rem; padding:0.5rem 1rem; min-width:160px; max-width:160px; width:160px;">
										<span class="fw-semibold" style="color:#313131">HUMSS</span>
										<span class="badge text-white" style="background-color:#0b2e13;">{{ $performance['HUMSS'] ?? 'N/A' }}</span>
									</div>
								</div>
								<div class="flex-grow-1 flex-basis-0 min-w-0" style="min-width:160px; max-width:160px; width:160px;">
									<div class="d-flex justify-content-between align-items-center border" style="border-color:#0b2e13 !important; background-color:#d1fae5; border-radius:0.75rem; padding:0.5rem 1rem; min-width:160px; max-width:160px; width:160px;">
										<span class="fw-semibold" style="color:#313131">TVL</span>
										<span class="badge text-white" style="background-color:#0b2e13;">{{ $performance['TVL'] ?? 'N/A' }}</span>
									</div>
								</div>
							</div>
							<div class="mt-2">
								<span class="text-muted small">* Data based on latest grading period</span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Pass/Fail Statistics -->
			<div class="card shadow-sm rounded-4 mb-4">
				<div class="card-header bg-opacity-10 rounded-top-4">
					<h5 class="mb-0 fw-bold" style="color:#313131">
						<i class="ti ti-chart-pie me-2" style="color:#313131"></i>Pass/Fail Statistics
					</h5>
				</div>
				<div class="card-body">
					<div class="row text-center">
						<div class="col-md-4 mb-3 mb-md-0">
							<div class="p-3 border rounded-3" style="border-color:#059669 !important; background-color:#d1fae5;">
								<div class="h2 fw-bold mb-1" style="color:#059669;">{{ $passFailStats['passed'] }}</div>
								<div class="text-muted small">Passed (≥75)</div>
								<div class="badge mt-2" style="background-color:#059669;">{{ $passFailStats['pass_rate'] }}%</div>
							</div>
						</div>
						<div class="col-md-4 mb-3 mb-md-0">
							<div class="p-3 border rounded-3" style="border-color:#dc2626 !important; background-color:#fee2e2;">
								<div class="h2 fw-bold mb-1" style="color:#dc2626;">{{ $passFailStats['failed'] }}</div>
								<div class="text-muted small">Failed (<75)</div>
								<div class="badge mt-2" style="background-color:#dc2626;">{{ $passFailStats['fail_rate'] }}%</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="p-3 border rounded-3" style="border-color:#6b7280 !important; background-color:#f3f4f6;">
								<div class="h2 fw-bold mb-1" style="color:#374151;">{{ $passFailStats['total'] }}</div>
								<div class="text-muted small">Total Records</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Attendance Overview Card -->
			<div class="card shadow-sm rounded-4 mb-4">
    <div class="card-header bg-opacity-10 rounded-top-4">
        <h5 class="mb-0 fw-bold" style="color:#313131"><i class="ti ti-calendar-check me-2" style="color:#313131"></i>Attendance Overview</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="fw-semibold mb-1"style="color:#313131">Daily Attendance</div>
					<span class="display-6 fw-bold "style="color:#313131">{{ $attendance['daily']['students'] }}%</span>
                    <div class="small text-muted">Students</div>
					<span class="fw-bold">{{ $attendance['daily']['teachers'] }}%</span>
                    <div class="small text-muted">Teachers</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="fw-semibold mb-1" style="color:#313131">Weekly Attendance</div>
					<span class="display-6 fw-bold"style="color:#313131">{{ $attendance['weekly']['students'] }}%</span>
                    <div class="small text-muted">Students</div>
					<span class="fw-bold">{{ $attendance['weekly']['teachers'] }}%</span>
                    <div class="small text-muted">Teachers</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="fw-semibold mb-1" style="color:#313131">Monthly Attendance</div>
					<span class="display-6 fw-bold"style="color:#313131">{{ $attendance['monthly']['students'] }}%</span>
                    <div class="small text-muted">Students</div>
					<span class="fw-bold">{{ $attendance['monthly']['teachers'] }}%</span>
                    <div class="small text-muted">Teachers</div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Section</th>
                        <th>Absentee %</th>
                    </tr>
                </thead>
                <tbody>
					@foreach($attendance['absentee'] as $abs)
					<tr>
						<td>{{ $abs['section'] }}</td>
						<td><span class="badge" style="background-color:#14532d">{{ $abs['percent'] }}%</span></td>
					</tr>
					@endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
		</div>
		
		<div class="col-lg-4 d-flex flex-column">
			<!-- Top Performing Students -->
			<div class="card shadow-sm rounded-4 mb-4">
				<div class="card-header bg-opacity-10 rounded-top-4">
					<h5 class="mb-0 fw-bold" style="color:#313131">
						<i class="ti ti-trophy me-2" style="color:#f59e0b"></i>Top Performing Students
					</h5>
				</div>
				<div class="card-body">
					@if($topStudents->count() > 0)
						<div class="table-responsive">
							<table class="table table-hover align-middle mb-0">
								<thead class="table-light">
									<tr>
										<th>Rank</th>
										<th>Student ID</th>
										<th>Name</th>
										<th>Average</th>
									</tr>
								</thead>
								<tbody>
									@foreach($topStudents as $index => $student)
										<tr>
											<td>
												@if($index === 0)
													<span class="badge" style="background-color:#f59e0b;">🥇 #1</span>
												@elseif($index === 1)
													<span class="badge" style="background-color:#94a3b8;">🥈 #2</span>
												@elseif($index === 2)
													<span class="badge" style="background-color:#cd7f32;">🥉 #3</span>
												@else
													<span class="badge bg-secondary">#{{ $index + 1 }}</span>
												@endif
											</td>
											<td><strong>{{ $student['student_number'] }}</strong></td>
											<td>{{ $student['name'] }}</td>
											<td>
												<span class="badge bg-success">{{ $student['average'] }}</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<p class="text-muted text-center mb-0">No performance data available yet.</p>
					@endif
				</div>
			</div>

			<!-- Recent Announcements -->
			<div class="card shadow-sm rounded-4 mb-4">
				<div class="card-header bg-opacity-10 rounded-top-4">
					<h5 class="mb-0 fw-bold" style="color:#313131">
						<i class="ti ti-speakerphone me-2" style="color:#10b981"></i>Recent Announcements
					</h5>
				</div>
				<div class="card-body">
					@if($recentMessages->count() > 0)
						<div class="list-group">
							@foreach($recentMessages as $message)
								<div class="list-group-item mb-2 rounded-3 border-0 bg-opacity-10" style="background-color:#e5e7eb">
									<div class="d-flex justify-content-between align-items-start">
										<div>
											<h6 class="mb-1 fw-bold">{{ Str::limit($message->title, 40) }}</h6>
											<p class="mb-1 text-muted small">{{ Str::limit($message->content, 60) }}</p>
											<small class="text-muted">
												<i class="ti ti-clock me-1"></i>{{ $message->created_at->diffForHumans() }}
											</small>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<p class="text-muted text-center mb-0">No announcements yet.</p>
					@endif
				</div>
			</div>

				   <!-- Academic Calendar -->
				   <div class="card shadow-sm rounded-4">
					   <div class="card-header bg-opacity-10 rounded-top-4">
						   <h5 class="mb-0 fw-bold" style="color:#313131">
							   <i class="ti ti-calendar me-2" style="color:#3b82f6"></i>Academic Calendar
						   </h5>
					   </div>
					   <div class="card-body">
						   <div id="academicCalendar"></div>
					   </div>
				   </div>
			   </div>
	</div>
</div>
@endsection