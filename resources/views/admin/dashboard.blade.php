@extends('admin.components.template')

@section('content')
<div class="container-fluid py-2 py-md-4">
	<div class="row mb-3 mb-md-4">
		<div class="col-12">
			<h2 class="fw-bold mb-3" style="color:#313131; font-size: 1.75rem;"><i class="ti ti-dashboard me-2" style="color:#313131"></i>Admin Dashboard</h2>
			<div class="row g-2 g-md-3">
				   <!-- Students -->
				   <div class="col-6 col-lg-3 d-flex">
					   <div class="card flex-fill">
						   <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-between">
							   <div class="d-flex align-items-center overflow-hidden w-100">
								   <div class="d-none d-sm-block">
									   <span class="avatar avatar-lg bg-dark rounded-circle"><i class="ti ti-users"></i></span>
								   </div>
								   <div class="ms-0 ms-sm-2 overflow-hidden w-100">
									   <p class="fs-12 fw-medium mb-1 text-truncate">Students</p>
									<h4 class="text-dark mb-0" style="font-size: 1.5rem;">{{ $studentsCount }}</h4>
								   </div>
							   </div>
						   </div>
					   </div>
				   </div>
				   <!-- Teachers -->
				   <div class="col-6 col-lg-3 d-flex">
					   <div class="card flex-fill">
						   <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-between">
							   <div class="d-flex align-items-center overflow-hidden w-100">
								   <div class="d-none d-sm-block">
									   <span class="avatar avatar-lg bg-success rounded-circle"><i class="ti ti-chalkboard"></i></span>
								   </div>
								   <div class="ms-0 ms-sm-2 overflow-hidden w-100">
									   <p class="fs-12 fw-medium mb-1 text-truncate">Teachers</p>
									<h4 class="mb-0" style="color:#313131; font-size: 1.5rem;">{{ $teachersCount }}</h4>
								   </div>
							   </div>
						   </div>
					   </div>
				   </div>
				   <!-- Sections -->
				   <div class="col-6 col-lg-3 d-flex">
					   <div class="card flex-fill">
						   <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-between">
							   <div class="d-flex align-items-center overflow-hidden w-100">
								   <div class="d-none d-sm-block">
									   <span class="avatar avatar-lg bg-danger rounded-circle"><i class="ti ti-building"></i></span>
								   </div>
								   <div class="ms-0 ms-sm-2 overflow-hidden w-100">
									   <p class="fs-12 fw-medium mb-1 text-truncate">Sections</p>
									<h4 class="mb-0" style="color:#313131; font-size: 1.5rem;">{{ $sectionsCount }}</h4>
								   </div>
							   </div>
						   </div>
				   </div>
				   </div>
				   <!-- Announcements -->
				   <div class="col-6 col-lg-3 d-flex">
					   <div class="card flex-fill">
						   <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-between">
							   <div class="d-flex align-items-center overflow-hidden w-100">
								   <div class="d-none d-sm-block">
									   <span class="avatar avatar-lg bg-info rounded-circle"><i class="ti ti-speakerphone"></i></span>
								   </div>
								   <div class="ms-0 ms-sm-2 overflow-hidden w-100">
									   <p class="fs-12 fw-medium mb-1 text-truncate">Announcements</p>
									<h4 class="mb-0" style="color:#313131; font-size: 1.5rem;">{{ $announcementsCount }}</h4>
								   </div>
							   </div>
						   </div>
					   </div>
				   </div>
			</div>
		</div>
	</div>
	
	<!-- Academic Performance Section -->
	<div class="row mb-3 mb-md-4">
		<div class="col-12">
			<h4 class="fw-bold mb-3" style="color:#313131; font-size: 1.35rem;">
				<i class="ti ti-school me-2" style="color:#313131"></i>Academic Performance Overview
			</h4>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-8">
			<!-- Pass/Fail Statistics -->
			<div class="card shadow-sm rounded-4 mb-3 mb-md-4">
				<div class="card-header bg-opacity-10 rounded-top-4 p-2 p-md-3">
					<h5 class="mb-0 fw-bold" style="color:#313131; font-size: 1.1rem;">
						<i class="ti ti-chart-pie me-2" style="color:#313131"></i>Pass/Fail Statistics
					</h5>
				</div>
				<div class="card-body p-2 p-md-3">
					<div class="row text-center">
						<div class="col-12 col-sm-4 mb-3 mb-sm-0">
							<div class="p-2 p-md-3 border rounded-3" style="border-color:#059669 !important; background-color:#d1fae5;">
								<div class="fw-bold mb-1" style="color:#059669; font-size: 2rem;">{{ $passFailStats['passed'] }}</div>
								<div class="text-muted small">Passed (≥75)</div>
								<div class="badge mt-2" style="background-color:#059669;">{{ $passFailStats['pass_rate'] }}%</div>
							</div>
						</div>
						<div class="col-12 col-sm-4 mb-3 mb-sm-0">
							<div class="p-2 p-md-3 border rounded-3" style="border-color:#dc2626 !important; background-color:#fee2e2;">
								<div class="fw-bold mb-1" style="color:#dc2626; font-size: 2rem;">{{ $passFailStats['failed'] }}</div>
								<div class="text-muted small">Failed (<75)</div>
								<div class="badge mt-2" style="background-color:#dc2626;">{{ $passFailStats['fail_rate'] }}%</div>
							</div>
						</div>
						<div class="col-12 col-sm-4">
							<div class="p-2 p-md-3 border rounded-3" style="border-color:#6b7280 !important; background-color:#f3f4f6;">
								<div class="fw-bold mb-1" style="color:#374151; font-size: 2rem;">{{ $passFailStats['total'] }}</div>
								<div class="text-muted small">Total Records</div>
							</div>
						</div>
					</div>
			</div>
		</div>

		<!-- User Registration Trends -->
		<div class="card shadow-sm rounded-4 mb-3 mb-md-4">
			<div class="card-header bg-opacity-10 rounded-top-4 p-2 p-md-3">
				<h5 class="mb-0 fw-bold" style="color:#313131; font-size: 1.1rem;">
					<i class="ti ti-chart-line me-2" style="color:#3b82f6"></i>Student Registration Trends
				</h5>
			</div>
			<div class="card-body p-2 p-md-3">
				<div id="registrationTrendsChart"></div>
			</div>
		</div>

		<!-- Students Per Strand -->
		<div class="card shadow-sm rounded-4 mb-3 mb-md-4">
			<div class="card-header bg-opacity-10 rounded-top-4 p-2 p-md-3">
				<h5 class="mb-0 fw-bold" style="color:#313131; font-size: 1.1rem;">
					<i class="ti ti-chart-bar me-2" style="color:#10b981"></i>Students per Strand
				</h5>
			</div>
			<div class="card-body p-2 p-md-3">
				<div id="studentsPerStrandChart"></div>
			</div>
		</div>
		</div>
				<div class="col-lg-4 d-flex flex-column">
			<!-- Top Performing Students -->
			<div class="card shadow-sm rounded-4 mb-3 mb-md-4">
				<div class="card-header bg-opacity-10 rounded-top-4 p-2 p-md-3">
					<h5 class="mb-0 fw-bold" style="color:#313131; font-size: 1.1rem;">
						<i class="ti ti-trophy me-2" style="color:#f59e0b"></i>Top Performing Students
					</h5>
				</div>
				<div class="card-body p-2 p-md-3">
					@if($topStudents->count() > 0)
						<div class="table-responsive">
							<table class="table table-hover align-middle mb-0 small">
								<thead class="table-light">
									<tr>
										<th class="py-2">Rank</th>
										<th class="py-2 d-none d-sm-table-cell">Student ID</th>
										<th class="py-2">Name</th>
										<th class="py-2">Avg</th>
									</tr>
								</thead>
								<tbody>
									@foreach($topStudents as $index => $student)
										<tr>
											<td class="py-2">
												@if($index === 0)
													<span class="badge" style="background-color:#f59e0b;">🥇 <span class="d-none d-sm-inline">#1</span></span>
												@elseif($index === 1)
													<span class="badge" style="background-color:#94a3b8;">🥈 <span class="d-none d-sm-inline">#2</span></span>
												@elseif($index === 2)
													<span class="badge" style="background-color:#cd7f32;">🥉 <span class="d-none d-sm-inline">#3</span></span>
												@else
													<span class="badge bg-secondary">#{{ $index + 1 }}</span>
												@endif
											</td>
											<td class="py-2 d-none d-sm-table-cell"><strong>{{ $student['student_number'] }}</strong></td>
											<td class="py-2">{{ $student['name'] }}</td>
											<td class="py-2">
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
			<div class="card shadow-sm rounded-4 mb-3 mb-md-4">
				<div class="card-header bg-opacity-10 rounded-top-4 p-2 p-md-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
					<h5 class="mb-0 fw-bold" style="color:#313131; font-size: 1.1rem;">
						<i class="ti ti-speakerphone me-2" style="color:#10b981"></i>Recent Announcements
					</h5>
					<div class="d-flex gap-2">
						<a href="{{ route('admin.announcements.create') }}" class="btn btn-sm bg-info">
							<i class="ti ti-plus me-1 d-none d-sm-inline"></i><span class="d-sm-none">+</span><span class="d-none d-sm-inline">New</span>
						</a>
						<a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-primary">
							<i class="ti ti-eye me-1 d-none d-sm-inline"></i><span class="d-sm-none">📋</span><span class="d-none d-sm-inline">Manage</span>
						</a>
					</div>
				</div>
				<div class="card-body p-2 p-md-3">
					@if($recentMessages->count() > 0)
						<div class="list-group">
							@foreach($recentMessages as $message)
								<div class="list-group-item mb-2 rounded-3 border-0 bg-opacity-10 p-2" style="background-color:#e5e7eb">
									<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2">
										<div class="flex-grow-1 w-100">
											<div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 mb-1">
												<h6 class="mb-0 fw-bold small">{{ Str::limit($message->title, 30) }}</h6>
												@if($message->is_active)
													@if($message->isExpired())
														<span class="badge bg-secondary">Expired</span>
													@elseif($message->isPublished())
														<span class="badge bg-success">Active</span>
													@else
														<span class="badge bg-warning">Scheduled</span>
													@endif
												@else
													<span class="badge bg-danger">Inactive</span>
												@endif
											</div>
											<p class="mb-1 text-muted small d-none d-sm-block">{{ Str::limit($message->content, 60) }}</p>
											<small class="text-muted">
												<i class="ti ti-clock me-1"></i>{{ $message->created_at->diffForHumans() }}
												@if($message->creator)
													<span class="ms-2 d-none d-md-inline">
														<i class="ti ti-user me-1"></i>{{ $message->creator->name }}
													</span>
												@endif
											</small>
										</div>
										<div class="ms-0 ms-sm-2">
											<a href="{{ route('admin.announcements.edit', $message) }}" class="btn btn-sm btn-outline-primary">
												<i class="ti ti-edit"></i>
											</a>
										</div>
									</div>
								</div>
							@endforeach
						</div>
						<div class="text-center mt-3">
							<a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-secondary">
								<i class="ti ti-list me-1"></i>View All {{ $announcementStats['total'] }} Announcements
							</a>
						</div>
					@else
						<div class="text-center py-4">
							<i class="ti ti-speakerphone text-muted" style="font-size: 3rem;"></i>
							<p class="text-muted mb-2">No announcements yet.</p>
							<a href="{{ route('admin.announcements.create') }}" class="btn btn-sm bg-info">
								<i class="ti ti-plus me-1"></i>Create First Announcement
							</a>
						</div>
					@endif
				</div>
			</div>

				   <!-- Academic Calendar -->
				   <div class="card shadow-sm rounded-4 mb-3 mb-md-4">
					   <div class="card-header bg-opacity-10 rounded-top-4 p-2 p-md-3">
						   <h5 class="mb-0 fw-bold" style="color:#313131; font-size: 1.1rem;">
							   <i class="ti ti-calendar me-2" style="color:#3b82f6"></i>Academic Calendar
						   </h5>
					   </div>
					   <div class="card-body p-2 p-md-3">
						   <div id="academicCalendar"></div>
					   </div>
				   </div>
			   </div>
	</div>
</div>

<style>
/* Mobile Responsive Styles */
@media (max-width: 576px) {
    .card-body {
        font-size: 0.875rem;
    }
    
    .avatar {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
    
    .h2, h2 {
        font-size: 1.5rem !important;
    }
    
    .h3, h3 {
        font-size: 1.25rem !important;
    }
    
    .h4, h4 {
        font-size: 1.1rem !important;
    }
    
    .fs-12 {
        font-size: 0.7rem !important;
    }
    
    /* Make charts more responsive */
    #registrationTrendsChart,
    #studentsPerStrandChart {
        min-height: 250px;
    }
    
    /* Adjust table for mobile */
    .table {
        font-size: 0.8rem;
    }
    
    .table th, .table td {
        padding: 0.5rem 0.25rem;
    }
    
    /* Make badges smaller on mobile */
    .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.4rem;
    }
    
    /* Reduce icon sizes */
    .ti {
        font-size: 1rem;
    }
}

@media (min-width: 577px) and (max-width: 768px) {
    .h2, h2 {
        font-size: 1.75rem !important;
    }
    
    .h3, h3 {
        font-size: 1.5rem !important;
    }
}
</style>
@endsection

@push('scripts')
<script>
// User Registration Trends Chart
var registrationOptions = {
    series: [{
        name: 'New Students',
        data: {!! json_encode($registrationData['counts']) !!}
    }],
    chart: {
        type: 'line',
        height: window.innerWidth < 576 ? 250 : 350,
        toolbar: {
            show: window.innerWidth >= 768,
            tools: {
                download: true,
                selection: false,
                zoom: false,
                zoomin: false,
                zoomout: false,
                pan: false,
                reset: false
            }
        }
    },
    colors: ['#3b82f6'],
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth',
        width: window.innerWidth < 576 ? 2 : 3
    },
    grid: {
        borderColor: '#e7e7e7',
        row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.5
        },
    },
    markers: {
        size: window.innerWidth < 576 ? 3 : 5,
        colors: ['#3b82f6'],
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
            size: window.innerWidth < 576 ? 5 : 7
        }
    },
    xaxis: {
        categories: {!! json_encode($registrationData['months']) !!},
        title: {
            text: window.innerWidth >= 768 ? 'Month' : '',
            style: {
                fontSize: '12px',
                fontWeight: 600
            }
        },
        labels: {
            rotate: window.innerWidth < 576 ? -90 : -45,
            style: {
                fontSize: window.innerWidth < 576 ? '9px' : '11px'
            }
        }
    },
    yaxis: {
        title: {
            text: window.innerWidth >= 768 ? 'Number of Registrations' : 'Registrations',
            style: {
                fontSize: window.innerWidth < 576 ? '10px' : '12px',
                fontWeight: 600
            }
        },
        labels: {
            formatter: function (val) {
                return Math.floor(val);
            },
            style: {
                fontSize: window.innerWidth < 576 ? '9px' : '11px'
            }
        }
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + ' student' + (val !== 1 ? 's' : '');
            }
        }
    },
    responsive: [{
        breakpoint: 576,
        options: {
            chart: {
                height: 250
            },
            legend: {
                show: false
            }
        }
    }]
};

var registrationChart = new ApexCharts(document.querySelector("#registrationTrendsChart"), registrationOptions);
registrationChart.render();

// Students Per Strand Bar Chart
var strandOptions = {
    series: [{
        name: 'Students',
        data: {!! json_encode($strandData['counts']) !!}
    }],
    chart: {
        type: 'bar',
        height: window.innerWidth < 576 ? 250 : 350,
        toolbar: {
            show: window.innerWidth >= 768,
            tools: {
                download: true,
                selection: false,
                zoom: false,
                zoomin: false,
                zoomout: false,
                pan: false,
                reset: false
            }
        }
    },
    colors: ['#10b981'],
    plotOptions: {
        bar: {
            borderRadius: window.innerWidth < 576 ? 4 : 6,
            dataLabels: {
                position: 'top'
            },
            distributed: false,
            horizontal: false,
        }
    },
    dataLabels: {
        enabled: window.innerWidth >= 576,
        formatter: function (val) {
            return val;
        },
        offsetY: -20,
        style: {
            fontSize: window.innerWidth < 576 ? '9px' : '12px',
            colors: ["#304758"]
        }
    },
    grid: {
        borderColor: '#e7e7e7',
        row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.5
        },
    },
    xaxis: {
        categories: {!! json_encode($strandData['names']) !!},
        title: {
            text: window.innerWidth >= 768 ? 'Strand' : '',
            style: {
                fontSize: window.innerWidth < 576 ? '10px' : '12px',
                fontWeight: 600
            }
        },
        labels: {
            style: {
                fontSize: window.innerWidth < 576 ? '9px' : '11px'
            },
            rotate: window.innerWidth < 576 ? -90 : -45,
            rotateAlways: false
        }
    },
    yaxis: {
        title: {
            text: window.innerWidth >= 768 ? 'Number of Students' : 'Students',
            style: {
                fontSize: window.innerWidth < 576 ? '10px' : '12px',
                fontWeight: 600
            }
        },
        labels: {
            formatter: function (val) {
                return Math.floor(val);
            },
            style: {
                fontSize: window.innerWidth < 576 ? '9px' : '11px'
            }
        }
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + ' student' + (val !== 1 ? 's' : '');
            }
        }
    },
    responsive: [{
        breakpoint: 576,
        options: {
            chart: {
                height: 250
            },
            legend: {
                show: false
            }
        }
    }]
};

var strandChart = new ApexCharts(document.querySelector("#studentsPerStrandChart"), strandOptions);
strandChart.render();

// Redraw charts on window resize for better responsiveness
window.addEventListener('resize', function() {
    if (registrationChart) {
        registrationChart.updateOptions({
            chart: {
                height: window.innerWidth < 576 ? 250 : 350,
                toolbar: {
                    show: window.innerWidth >= 768
                }
            },
            xaxis: {
                title: {
                    text: window.innerWidth >= 768 ? 'Month' : ''
                },
                labels: {
                    rotate: window.innerWidth < 576 ? -90 : -45,
                    style: {
                        fontSize: window.innerWidth < 576 ? '9px' : '11px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: window.innerWidth >= 768 ? 'Number of Registrations' : 'Registrations'
                },
                labels: {
                    style: {
                        fontSize: window.innerWidth < 576 ? '9px' : '11px'
                    }
                }
            }
        });
    }
    
    if (strandChart) {
        strandChart.updateOptions({
            chart: {
                height: window.innerWidth < 576 ? 250 : 350,
                toolbar: {
                    show: window.innerWidth >= 768
                }
            },
            xaxis: {
                title: {
                    text: window.innerWidth >= 768 ? 'Strand' : ''
                },
                labels: {
                    rotate: window.innerWidth < 576 ? -90 : -45,
                    style: {
                        fontSize: window.innerWidth < 576 ? '9px' : '11px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: window.innerWidth >= 768 ? 'Number of Students' : 'Students'
                },
                labels: {
                    style: {
                        fontSize: window.innerWidth < 576 ? '9px' : '11px'
                    }
                }
            },
            dataLabels: {
                enabled: window.innerWidth >= 576
            }
        });
    }
});
</script>
@endpush