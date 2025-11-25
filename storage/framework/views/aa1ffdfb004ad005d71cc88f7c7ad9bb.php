<?php
    $routeIs = fn($name) => request()->routeIs($name);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Student Portal</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('assets/images/Image.png')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('assets/img/apple-touch-icon.png')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/icons/feather/feather.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/tabler-icons/tabler-icons.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/fontawesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive-sidebar.css')); ?>">
</head>
<body>
    <div class="main-wrapper">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <a href="<?php echo e(route('student.dashboard')); ?>" class="logo logo-normal">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo" style="max-height: 40px; width: auto;">
                </a>
                <a href="<?php echo e(route('student.dashboard')); ?>" class="logo-small">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo" style="max-height: 35px; width: auto;">
                </a>
                <a href="<?php echo e(route('student.dashboard')); ?>" class="dark-logo">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo" style="max-height: 40px; width: auto;">
                </a>
            </div>
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title"><span>Student</span></li>
                        <li>
                            <ul>
                                <li class="<?php echo e($routeIs('student.dashboard') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('student.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('student.dashboard')); ?>">
                                        <i class="ti ti-layout-navbar"></i><span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('student.academic-years.index') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('student.academic-years.index') ? 'active' : ''); ?>" href="<?php echo e(route('student.academic-years.index')); ?>">
                                        <i class="ti ti-calendar"></i><span>Academic Years</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('student.subjects.index') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('student.subjects.index') ? 'active' : ''); ?>" href="<?php echo e(route('student.subjects.index')); ?>">
                                        <i class="ti ti-books"></i><span>Subjects</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('student.grades.index') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('student.grades.index') ? 'active' : ''); ?>" href="<?php echo e(route('student.grades.index')); ?>">
                                        <i class="ti ti-report-analytics"></i><span>Grades</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('student.scores.index') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('student.scores.index') ? 'active' : ''); ?>" href="<?php echo e(route('student.scores.index')); ?>">
                                        <i class="ti ti-list-numbers"></i><span>Scores</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('student.performance.index') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('student.performance.index') ? 'active' : ''); ?>" href="<?php echo e(route('student.performance.index')); ?>">
                                        <i class="ti ti-chart-line"></i><span>Performance</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('student.enhancement.index') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('student.enhancement.index') ? 'active' : ''); ?>" href="<?php echo e(route('student.enhancement.index')); ?>">
                                        <i class="ti ti-brain"></i><span>Enhancement</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('student.profile.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('student.profile.*') ? 'active' : ''); ?>" href="<?php echo e(route('student.profile.show')); ?>">
                                        <i class="ti ti-user"></i><span>My Profile</span>
                                    </a>
                                </li>
                                <li class="<?php echo e($routeIs('student.messages.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e($routeIs('student.messages.*') ? 'active' : ''); ?>" href="<?php echo e(route('student.messages.messenger')); ?>">
                                        <i class="ti ti-mail"></i><span>Messages</span>
                                    </a>
                                </li>
                                <li class="<?php echo e(isset($preEnrollmentEnabled) && $preEnrollmentEnabled && $routeIs('student.pre-enrollment.*') ? 'active' : ''); ?>">
                                    <?php if(isset($preEnrollmentEnabled) && $preEnrollmentEnabled): ?>
                                        <a class="<?php echo e($routeIs('student.pre-enrollment.*') ? 'active' : ''); ?>" href="<?php echo e(route('student.pre-enrollment.index')); ?>" title="Pre-enrollment is now available">
                                            <i class="ti ti-file-check"></i><span>Pre-Enrollment</span>
                                            <span class="badge bg-success ms-1" style="font-size: 0.65rem;">Available</span>
                                        </a>
                                    <?php else: ?>
                                        <a class="disabled" style="pointer-events: none; opacity: 0.6; cursor: not-allowed;" title="Pre-enrollment is not yet available">
                                            <i class="ti ti-file-check"></i><span>Pre-Enrollment</span>
                                        </a>
                                    <?php endif; ?>
                                </li>
                                <li>
                                    <form action="<?php echo e(route('student.auth.logout')); ?>" method="POST" class="d-inline">
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
                <a href="<?php echo e(route('student.dashboard')); ?>" class="portal-mobile-logo">
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
                    window.location.href = "<?php echo e(route('student.auth.loginForm')); ?>";
                };
            }
        })();
    </script>
    <script src="<?php echo e(asset('assets/js/responsive-sidebar.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('modals'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/student/components/template.blade.php ENDPATH**/ ?>