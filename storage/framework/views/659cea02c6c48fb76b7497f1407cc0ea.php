

<?php $__env->startSection('breadcrumb'); ?>
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Student</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    Student
                </li>
                <li class="breadcrumb-item active" aria-current="page">Student List</li>
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
            <a href="<?php echo e(route('admin.students.create')); ?>" class="btn bg-info d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Student</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="row">
        <!-- Total Student -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-dark rounded-circle"><i class="ti ti-users"></i></span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Total Students</p>
                            <h4><?php echo e($no_students); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Total Student -->

        <!-- Total Student -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-success rounded-circle"><i class="ti ti-user-share"></i></span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Active</p>
                            <h4><?php echo e($no_active_students); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Total Student -->

        <!-- Inactive Student -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-danger rounded-circle"><i class="ti ti-user-pause"></i></span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Dropped</p>
                            <h4><?php echo e($no_dropped_students); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Inactive Companies -->

        <!-- No of Student  -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-info rounded-circle"><i class="ti ti-user-plus"></i></span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">New Students (<?php echo e(date('Y')); ?>)</p>
                            <h4><?php echo e($no_new_students); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /No of Student -->

    </div>
    
    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.students.index')); ?>" class="row g-3" id="filterForm">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                        <option value="dropped" <?php echo e(request('status') == 'dropped' ? 'selected' : ''); ?>>Dropped</option>
                        <option value="graduated" <?php echo e(request('status') == 'graduated' ? 'selected' : ''); ?>>Graduated</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="strand" class="form-label">Strand/Track</label>
                    <select name="strand" id="strand" class="form-select">
                        <option value="">All Strands</option>
                        <?php $__currentLoopData = $strands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $strand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($strand->id); ?>" <?php echo e(request('strand') == $strand->id ? 'selected' : ''); ?>>
                                <?php echo e($strand->name); ?> (<?php echo e($strand->code); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="grade_level" class="form-label">Grade Level</label>
                    <select name="grade_level" id="grade_level" class="form-select">
                        <option value="">All Grades</option>
                        <option value="11" <?php echo e(request('grade_level') == '11' ? 'selected' : ''); ?>>Grade 11</option>
                        <option value="12" <?php echo e(request('grade_level') == '12' ? 'selected' : ''); ?>>Grade 12</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="section" class="form-label">Section</label>
                    <select name="section" id="section" class="form-select">
                        <option value="">All Sections</option>
                        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($section->id); ?>" <?php echo e(request('section') == $section->id ? 'selected' : ''); ?>>
                                <?php echo e($section->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn bg-info me-2">
                        <i class="ti ti-filter me-1"></i>Filter
                    </button>
                    <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-secondary">
                        <i class="ti ti-x me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Student</th>
                            <th>Guardian</th>
                            <th>Program</th>
                            <th>Grade Level</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">

                                    <a href="#" class="avatar avatar-md" data-bs-toggle="modal" data-bs-target="#view_details">
                                        <img src="<?php echo e($student->avatar ? asset($student->avatar) : asset('assets/img/users/user-32.jpg')); ?>" class="img-fluid rounded-circle" alt="img">
                                    </a>
                                    <div class="ms-2">
                                        <p class="text-dark mb-0">
                                            <a href="<?php echo e(route('admin.students.show', $student)); ?>"><?php echo e($student->name); ?></a> |
                                            <span class="fs-12"> <?php echo e($student->student_number); ?> </span>
                                            <?php if(isset($student->is_new) && $student->is_new): ?>
                                            <span class="badge bg-success text-white me-2">New</span>
                                            <?php endif; ?>
                                        </p>
                                        <span class="fs-12"><?php echo e($student->contact ?? ''); ?> </span>
                                        <br>
                                        <span class="fs-12"><?php echo e($student->address ?? ''); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <p class="text-dark mb-0">
                                            <?php echo e($student->guardian_name); ?>

                                        </p>
                                        <span class="fs-12"><?php echo e($student->guardian_contact ?? ''); ?> | <?php echo e($student->guardian_email ?? ''); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="ms-2">
                                    <span class="fs-12"><?php echo e($student->program ?? ''); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="ms-2">
                                    <?php if($student->grade_level): ?>
                                        <span class="badge badge-primary badge-xs">
                                            <?php echo e($student->grade_level); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted fs-12">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="ms-2">
                                    <?php
                                        $activeEnrollment = $student->studentEnrollments->first();
                                        $section = $activeEnrollment && $activeEnrollment->academicYearStrandSection 
                                            ? $activeEnrollment->academicYearStrandSection->section 
                                            : null;
                                    ?>
                                    <?php if($section): ?>
                                        <span class="badge badge-info badge-xs">
                                            <?php echo e($section->name); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted fs-12">Not Assigned</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge 
                                    <?php if($student->status == 'active'): ?> badge-success
                                    <?php elseif($student->status == 'graduated'): ?> badge-primary
                                    <?php elseif($student->status == 'dropped'): ?> badge-warning
                                    <?php else: ?> badge-secondary
                                    <?php endif; ?>
                                    d-inline-flex align-items-center badge-xs">
                                    <i class="ti ti-point-filled me-1"></i><?php echo e(ucfirst($student->status)); ?>

                                </span>
                            </td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="<?php echo e(route('admin.students.show', $student)); ?>" class="me-2"><i class="ti ti-eye"></i></a>
                                    <a href="<?php echo e(route('admin.students.edit', $student)); ?>" class="me-2"><i class="ti ti-edit"></i></a>
                                    <form action="<?php echo e(route('admin.students.destroy', $student)); ?>" method="POST" onsubmit="return confirm('Delete this student?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline text-danger"><i class="ti ti-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const strandSelect = document.getElementById('strand');
    const gradeLevelSelect = document.getElementById('grade_level');
    const filterForm = document.getElementById('filterForm');
    
    // Auto-submit form when strand or grade level changes to update section dropdown
    strandSelect.addEventListener('change', function() {
        filterForm.submit();
    });
    
    gradeLevelSelect.addEventListener('change', function() {
        filterForm.submit();
    });
});

function exportToExcel() {
    // Get the DataTable instance
    const dataTable = $('.datatable').DataTable();
    
    // Prepare data array for export
    const exportData = [];
    
    // Add headers
    exportData.push(['Student Name', 'Student Number', 'Contact', 'Address', 'Guardian Name', 'Guardian Contact', 'Program', 'Grade Level', 'Section', 'Status']);
    
    // Get all rows data (not just visible ones)
    dataTable.rows({ search: 'applied' }).every(function() {
        const rowNode = this.node();
        const cells = $(rowNode).find('td');
        
        // Extract student info
        const studentCell = cells.eq(0);
        const nameLink = studentCell.find('a').first();
        const studentName = nameLink.length ? nameLink.text().trim() : '';
        
        // Extract student number from the first .fs-12 span
        const studentNumberSpan = studentCell.find('.fs-12').first();
        const studentNumber = studentNumberSpan.length ? studentNumberSpan.text().trim() : '';
        
        // Extract contact and address from remaining .fs-12 spans
        const allSmallText = studentCell.find('.fs-12');
        const contact = allSmallText.length > 0 ? allSmallText.eq(0).text().trim() : '';
        const address = allSmallText.length > 1 ? allSmallText.eq(1).text().trim() : '';
        
        // Extract guardian info
        const guardianCell = cells.eq(1);
        const guardianName = guardianCell.find('.text-dark').text().trim();
        const guardianContact = guardianCell.find('.fs-12').text().trim();
        
        // Extract program
        const program = cells.eq(2).text().trim();
        
        // Extract grade level
        let gradeLevel = '';
        const gradeLevelBadge = cells.eq(3).find('.badge');
        if (gradeLevelBadge.length) {
            gradeLevel = 'Grade ' + gradeLevelBadge.text().trim();
        } else {
            gradeLevel = cells.eq(3).text().trim();
        }
        
        // Extract section
        let section = '';
        const sectionBadge = cells.eq(4).find('.badge');
        if (sectionBadge.length) {
            section = sectionBadge.text().trim();
        } else {
            section = cells.eq(4).text().trim();
        }
        
        // Extract status
        let status = '';
        const statusBadge = cells.eq(5).find('.badge');
        if (statusBadge.length) {
            status = statusBadge.text().trim();
        }
        
        // Add row to export data
        exportData.push([
            studentName,
            studentNumber,
            contact,
            address,
            guardianName,
            guardianContact,
            program,
            gradeLevel,
            section,
            status
        ]);
    });
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(exportData);
    
    // Set column widths
    ws['!cols'] = [
        { wch: 30 },  // Student Name
        { wch: 15 },  // Student Number
        { wch: 15 },  // Contact
        { wch: 30 },  // Address
        { wch: 25 },  // Guardian Name
        { wch: 30 },  // Guardian Contact
        { wch: 10 },  // Program
        { wch: 12 },  // Grade Level
        { wch: 20 },  // Section
        { wch: 10 }   // Status
    ];
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Students');
    
    // Generate filename with current date
    const filename = `Students_List_${new Date().toISOString().split('T')[0]}.xlsx`;
    
    // Download the file
    XLSX.writeFile(wb, filename);
}
</script>

<!-- SheetJS library for Excel export -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/students/index.blade.php ENDPATH**/ ?>