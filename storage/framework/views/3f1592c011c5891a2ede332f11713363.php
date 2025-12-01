

<?php $__env->startSection('content'); ?>
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
									<h4 class="text-dark mb-0"><?php echo e($studentsCount); ?></h4>
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
									<h4 class="mb-0"style="color:#313131"><?php echo e($teachersCount); ?></h4>
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
									<h4 class="mb-0" style="color:#313131"><?php echo e($sectionsCount); ?></h4>
								   </div>
							   </div>
						   </div>
				   </div>
				   </div>
				   <!-- Announcements -->
				   <div class="col-lg-3 col-md-6 d-flex">
					   <div class="card flex-fill">
						   <div class="card-body d-flex align-items-center justify-content-between">
							   <div class="d-flex align-items-center overflow-hidden">
								   <div>
									   <span class="avatar avatar-lg bg-info rounded-circle"><i class="ti ti-speakerphone"></i></span>
								   </div>
								   <div class="ms-2 overflow-hidden">
									   <p class="fs-12 fw-medium mb-1 text-truncate">Announcements</p>
									<h4 class="mb-0" style="color:#313131"><?php echo e($announcementsCount); ?></h4>
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
								<div class="h2 fw-bold mb-1" style="color:#059669;"><?php echo e($passFailStats['passed']); ?></div>
								<div class="text-muted small">Passed (≥75)</div>
								<div class="badge mt-2" style="background-color:#059669;"><?php echo e($passFailStats['pass_rate']); ?>%</div>
							</div>
						</div>
						<div class="col-md-4 mb-3 mb-md-0">
							<div class="p-3 border rounded-3" style="border-color:#dc2626 !important; background-color:#fee2e2;">
								<div class="h2 fw-bold mb-1" style="color:#dc2626;"><?php echo e($passFailStats['failed']); ?></div>
								<div class="text-muted small">Failed (<75)</div>
								<div class="badge mt-2" style="background-color:#dc2626;"><?php echo e($passFailStats['fail_rate']); ?>%</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="p-3 border rounded-3" style="border-color:#6b7280 !important; background-color:#f3f4f6;">
								<div class="h2 fw-bold mb-1" style="color:#374151;"><?php echo e($passFailStats['total']); ?></div>
								<div class="text-muted small">Total Records</div>
							</div>
						</div>
					</div>
			</div>
		</div>

		<!-- User Registration Trends -->
		<div class="card shadow-sm rounded-4 mb-4">
			<div class="card-header bg-opacity-10 rounded-top-4">
				<h5 class="mb-0 fw-bold" style="color:#313131">
					<i class="ti ti-chart-line me-2" style="color:#3b82f6"></i>Student Registration Trends
				</h5>
			</div>
			<div class="card-body">
				<div id="registrationTrendsChart"></div>
			</div>
		</div>

		<!-- Students Per Strand -->
		<div class="card shadow-sm rounded-4 mb-4">
			<div class="card-header bg-opacity-10 rounded-top-4">
				<h5 class="mb-0 fw-bold" style="color:#313131">
					<i class="ti ti-chart-bar me-2" style="color:#10b981"></i>Students per Strand
				</h5>
			</div>
			<div class="card-body">
				<div id="studentsPerStrandChart"></div>
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
					<?php if($topStudents->count() > 0): ?>
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
									<?php $__currentLoopData = $topStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td>
												<?php if($index === 0): ?>
													<span class="badge" style="background-color:#f59e0b;">🥇 #1</span>
												<?php elseif($index === 1): ?>
													<span class="badge" style="background-color:#94a3b8;">🥈 #2</span>
												<?php elseif($index === 2): ?>
													<span class="badge" style="background-color:#cd7f32;">🥉 #3</span>
												<?php else: ?>
													<span class="badge bg-secondary">#<?php echo e($index + 1); ?></span>
												<?php endif; ?>
											</td>
											<td><strong><?php echo e($student['student_number']); ?></strong></td>
											<td><?php echo e($student['name']); ?></td>
											<td>
												<span class="badge bg-success"><?php echo e($student['average']); ?></span>
											</td>
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
						</div>
					<?php else: ?>
						<p class="text-muted text-center mb-0">No performance data available yet.</p>
					<?php endif; ?>
				</div>
			</div>

			<!-- Recent Announcements -->
			<div class="card shadow-sm rounded-4 mb-4">
				<div class="card-header bg-opacity-10 rounded-top-4 d-flex justify-content-between align-items-center">
					<h5 class="mb-0 fw-bold" style="color:#313131">
						<i class="ti ti-speakerphone me-2" style="color:#10b981"></i>Recent Announcements
					</h5>
					<div class="d-flex gap-2">
						<a href="<?php echo e(route('admin.announcements.create')); ?>" class="btn btn-sm bg-info">
							<i class="ti ti-plus me-1"></i>New
						</a>
						<a href="<?php echo e(route('admin.announcements.index')); ?>" class="btn btn-sm btn-outline-primary">
							<i class="ti ti-eye me-1"></i>Manage
						</a>
					</div>
				</div>
				<div class="card-body">
					<?php if($recentMessages->count() > 0): ?>
						<div class="list-group">
							<?php $__currentLoopData = $recentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<div class="list-group-item mb-2 rounded-3 border-0 bg-opacity-10" style="background-color:#e5e7eb">
									<div class="d-flex justify-content-between align-items-start">
										<div class="flex-grow-1">
											<div class="d-flex align-items-center gap-2 mb-1">
												<h6 class="mb-0 fw-bold"><?php echo e(Str::limit($message->title, 40)); ?></h6>
												<?php if($message->is_active): ?>
													<?php if($message->isExpired()): ?>
														<span class="badge bg-secondary">Expired</span>
													<?php elseif($message->isPublished()): ?>
														<span class="badge bg-success">Active</span>
													<?php else: ?>
														<span class="badge bg-warning">Scheduled</span>
													<?php endif; ?>
												<?php else: ?>
													<span class="badge bg-danger">Inactive</span>
												<?php endif; ?>
											</div>
											<p class="mb-1 text-muted small"><?php echo e(Str::limit($message->content, 60)); ?></p>
											<small class="text-muted">
												<i class="ti ti-clock me-1"></i><?php echo e($message->created_at->diffForHumans()); ?>

												<?php if($message->creator): ?>
													<span class="ms-2">
														<i class="ti ti-user me-1"></i><?php echo e($message->creator->name); ?>

													</span>
												<?php endif; ?>
											</small>
										</div>
										<div class="ms-2">
											<a href="<?php echo e(route('admin.announcements.edit', $message)); ?>" class="btn btn-sm btn-outline-primary">
												<i class="ti ti-edit"></i>
											</a>
										</div>
									</div>
								</div>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</div>
						<div class="text-center mt-3">
							<a href="<?php echo e(route('admin.announcements.index')); ?>" class="btn btn-sm btn-outline-secondary">
								<i class="ti ti-list me-1"></i>View All <?php echo e($announcementStats['total']); ?> Announcements
							</a>
						</div>
					<?php else: ?>
						<div class="text-center py-4">
							<i class="ti ti-speakerphone text-muted" style="font-size: 3rem;"></i>
							<p class="text-muted mb-2">No announcements yet.</p>
							<a href="<?php echo e(route('admin.announcements.create')); ?>" class="btn btn-sm bg-info">
								<i class="ti ti-plus me-1"></i>Create First Announcement
							</a>
						</div>
					<?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// User Registration Trends Chart
var registrationOptions = {
    series: [{
        name: 'New Students',
        data: <?php echo json_encode($registrationData['counts']); ?>

    }],
    chart: {
        type: 'line',
        height: 350,
        toolbar: {
            show: true,
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
        width: 3
    },
    grid: {
        borderColor: '#e7e7e7',
        row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.5
        },
    },
    markers: {
        size: 5,
        colors: ['#3b82f6'],
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
            size: 7
        }
    },
    xaxis: {
        categories: <?php echo json_encode($registrationData['months']); ?>,
        title: {
            text: 'Month',
            style: {
                fontSize: '12px',
                fontWeight: 600
            }
        },
        labels: {
            rotate: -45,
            style: {
                fontSize: '11px'
            }
        }
    },
    yaxis: {
        title: {
            text: 'Number of Registrations',
            style: {
                fontSize: '12px',
                fontWeight: 600
            }
        },
        labels: {
            formatter: function (val) {
                return Math.floor(val);
            }
        }
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + ' student' + (val !== 1 ? 's' : '');
            }
        }
    }
};

var registrationChart = new ApexCharts(document.querySelector("#registrationTrendsChart"), registrationOptions);
registrationChart.render();

// Students Per Strand Bar Chart
var strandOptions = {
    series: [{
        name: 'Students',
        data: <?php echo json_encode($strandData['counts']); ?>

    }],
    chart: {
        type: 'bar',
        height: 350,
        toolbar: {
            show: true,
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
            borderRadius: 6,
            dataLabels: {
                position: 'top'
            },
            distributed: false,
            horizontal: false,
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return val;
        },
        offsetY: -20,
        style: {
            fontSize: '12px',
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
        categories: <?php echo json_encode($strandData['names']); ?>,
        title: {
            text: 'Strand',
            style: {
                fontSize: '12px',
                fontWeight: 600
            }
        },
        labels: {
            style: {
                fontSize: '11px'
            },
            rotate: -45,
            rotateAlways: false
        }
    },
    yaxis: {
        title: {
            text: 'Number of Students',
            style: {
                fontSize: '12px',
                fontWeight: 600
            }
        },
        labels: {
            formatter: function (val) {
                return Math.floor(val);
            }
        }
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + ' student' + (val !== 1 ? 's' : '');
            }
        }
    }
};

var strandChart = new ApexCharts(document.querySelector("#studentsPerStrandChart"), strandOptions);
strandChart.render();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>