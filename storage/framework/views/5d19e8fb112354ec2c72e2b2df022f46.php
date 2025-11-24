<?php
    $routeIs = fn($name) => request()->routeIs($name);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Teacher Portal</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('assets/images/Image.png')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('assets/img/apple-touch-icon.png')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/icons/feather/feather.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/tabler-icons/tabler-icons.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/fontawesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/all.min.css')); ?>">
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dataTables.bootstrap5.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive-sidebar.css')); ?>">
</head>

<body>
    <div class="main-wrapper">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <a href="<?php echo e(route('teacher.dashboard')); ?>" class="logo logo-normal">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo" style="max-height: 40px; width: auto;">
                </a>
                <a href="<?php echo e(route('teacher.dashboard')); ?>" class="logo-small">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo" style="max-height: 35px; width: auto;">
                </a>
                <a href="<?php echo e(route('teacher.dashboard')); ?>" class="dark-logo">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo" style="max-height: 40px; width: auto;">
                </a>
            </div>
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title"><span>Teacher</span></li>
                        <li>
                            <ul>
                                <li class="<?php echo e($routeIs('teacher.profile.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('teacher.profile.*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.profile.show')); ?>">
                                        <i class="ti ti-user"></i><span>My Profile</span>
                                    </a>
                                </li>
                                <?php
                                    $teacherUser = auth('teacher')->user();
                                    $teacherModel = $teacherUser ? \App\Models\Teacher::find($teacherUser->user_pk_id) : null;
                                    $isActive = $teacherModel && $teacherModel->status === 'active';
                                ?>
                                <?php if($isActive): ?>
                                <li class="<?php echo e($routeIs('teacher.dashboard') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('teacher.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.dashboard')); ?>">
                                        <i class="ti ti-layout-navbar"></i><span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('teacher.subjects.index') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('teacher.subjects.index') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.subjects.index')); ?>">
                                        <i class="ti ti-books"></i><span>Classes</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('teacher.class-records.index') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('teacher.class-records.index') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.class-records.index')); ?>">
                                        <i class="ti ti-notebook"></i><span>Class Records</span>
                                    </a>
                                </li>

                                <li class="submenu <?php echo e($routeIs('teacher.students.*') || $routeIs('teacher.scores.index') ? 'active subdrop' : ''); ?>">
                                    <a href="javascript:void(0);" class="<?php echo e($routeIs('teacher.students.*') || $routeIs('teacher.scores.index') ? 'active subdrop' : ''); ?>">
                                        <i class="ti ti-users"></i><span>Student Lists</span><span class="menu-arrow"></span>
                                    </a>
                                    <ul style="<?php echo e($routeIs('teacher.students.*') || $routeIs('teacher.scores.index') ? 'display: block;' : 'display: none;'); ?>">
                                        <li>
                                            <a href="<?php echo e(route('teacher.students.index')); ?>" class="<?php echo e($routeIs('teacher.students.index') ? 'active' : ''); ?>">
                                                <i class="ti ti-list"></i><span>My Students</span>
                                            </a>
                                        </li>
                                        <li class="<?php echo e($routeIs('teacher.scores.index') ? 'active' : ''); ?>" style="margin-top: 8px;">
                                            <a class="<?php echo e($routeIs('teacher.scores.index') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.scores.index')); ?>">
                                                <i class="ti ti-list-numbers"></i><span>Student Scores</span>
                                            </a>
                                        </li>
                                        <li class="menu-title" style="margin-top: 10px; padding-left: 15px; font-size: 11px; color: #999;">
                                            <span>Quick Access</span>
                                        </li>
                                        <?php
                                            $teacher = auth('teacher')->user();
                                            $currentAcademicYear = \App\Models\AcademicYear::where('is_active', true)->first();
                                            $mySections = collect();
                                            if ($teacher && $currentAcademicYear) {
                                                $mySections = \App\Models\AcademicYearStrandSection::with(['section', 'strand'])
                                                    ->where('adviser_teacher_id', $teacher->id)
                                                    ->where('academic_year_id', $currentAcademicYear->id)
                                                    ->get()
                                                    ->sortBy('section.name');
                                            }
                                        ?>
                                        <?php $__empty_1 = true; $__currentLoopData = $mySections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mySection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <li>
                                                <a href="<?php echo e(route('teacher.students.section', $mySection->id)); ?>" class="<?php echo e(request()->route('sectionAssignment') == $mySection->id ? 'active' : ''); ?>">
                                                    <i class="ti ti-point"></i><span>Section <?php echo e($mySection->section->name ?? 'N/A'); ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <li style="padding-left: 30px;">
                                                <small class="text-muted">No sections assigned</small>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>

                                <li class="<?php echo e($routeIs('teacher.messages.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('teacher.messages.*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.messages.messenger')); ?>">
                                        <i class="ti ti-mail"></i><span>Messages</span>
                                    </a>
                                </li>
                                <?php else: ?>
                                <li>
                                    <div class="alert alert-warning mx-3 my-2" style="font-size: 0.85rem; padding: 0.75rem;">
                                        <i class="ti ti-lock me-1"></i>
                                        <strong>Account <?php echo e($teacherModel ? ucfirst($teacherModel->status) : 'Inactive'); ?></strong><br>
                                        <small>Limited access - Profile only</small>
                                    </div>
                                </li>
                                <?php endif; ?>

                                <li>
                                    <form action="<?php echo e(route('teacher.auth.logout')); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 mt-2">
                                            <i class="ti ti-logout me-1"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="sidebar-overlay"></div>
        <div class="page-wrapper">
            <header class="portal-mobile-header">
                <button type="button" class="mobile-menu-btn" aria-label="Toggle navigation" aria-controls="sidebar" aria-expanded="false">
                    <i class="ti ti-menu-2"></i>
                </button>
                <a href="<?php echo e(route('teacher.dashboard')); ?>" class="portal-mobile-logo">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo">
                </a>
            </header>
            <div class="content">
                <?php echo $__env->yieldContent('content'); ?>
            </div>

            <div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
                <p class="mb-0">&copy; <?php echo e(date('Y')); ?> St. Matthew Senior High School</p>
            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('assets/js/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/feather.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/jquery.slimscroll.min.js')); ?>"></script>
    <!-- Datatable JS -->
    <script src="<?php echo e(asset('assets/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/dataTables.bootstrap5.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/script.js')); ?>"></script>
    <script>
        // Enforce fresh load on back/forward (handles bfcache)
        window.addEventListener('pageshow', function (event) {
            try {
                const nav = performance.getEntriesByType('navigation')[0];
                const fromBFCache = event.persisted || (nav && nav.type === 'back_forward');
                if (fromBFCache) {
                    window.location.reload();
                }
            } catch (e) {
                if (event.persisted) window.location.reload();
            }
        });

        // Gentle fallback: redirect to login on back
        (function() {
            if (window.history && window.history.pushState) {
                window.history.pushState('forward', null, '');
                window.onpopstate = function() {
                    window.location.href = "<?php echo e(route('teacher.auth.loginForm')); ?>";
                };
            }
        })();
    </script>
    <script src="<?php echo e(asset('assets/js/responsive-sidebar.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('modals'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/teacher/components/template.blade.php ENDPATH**/ ?>