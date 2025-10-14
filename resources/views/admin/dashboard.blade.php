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
	   <div class="row">
		   <div class="col-lg-8">
			   <!-- Student Performance Analytics -->
			   <div class="card shadow-sm rounded-4 mb-4">
				<div class="card-header bg-opacity-10 rounded-top-4">
					<h5 class="mb-0 fw-bold" style="color:#313131"><i class="ti ti-chart-bar me-2" style="color:#313131"></i>Student Performance Analytics</h5>
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
				   <div class="col-lg-3 d-flex flex-column align-items-end">
					   <div class="card shadow-sm rounded-4 mb-4" style="max-width:400px; min-width:300px; width:100%;">
						   <div class="card-header bg-opacity-10 rounded-top-4">
							   <h5 class="mb-0 fw-bold" style="color:#313131"><i class="ti ti-news me-2" style="color:#313131"></i>Recent Announcements</h5>
						   </div>
						   <div class="card-body">
							   <ul class="list-group list-group-flush">
								   @foreach($announcements as $announcement)
									   <li class="list-group-item">{{ $announcement }}</li>
								   @endforeach
							   </ul>
						   </div>
					   </div>

				   <!-- Academic Calendar Card -->
				   <div class="card shadow-sm rounded-4 mb-4" style="width:383px; margin-right:0">
					   <div class="card-header bg-opacity-10 rounded-top-4">
						   <h5 class="mb-0 fw-bold" style="color:#313131"><i class="ti ti-calendar-event me-2" style="color:#313131"></i>Academic Calendar</h5>
					   </div>
					   <div class="card-body">
						   <ul class="list-group list-group-flush">
							   @foreach($calendar as $item)
								   <li class="list-group-item d-flex align-items-center">
									   <span class="badge me-2" style="background-color:#14532d">{{ $item['date'] }}</span> {{ $item['event'] }}
								   </li>
							   @endforeach
						   </ul>
					   </div>
				   </div>
			   </div>
	</div>
</div>
@endsection