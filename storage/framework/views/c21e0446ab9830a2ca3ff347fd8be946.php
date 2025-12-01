<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="St. Matthew Senior High School">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>St. Matthew Senior High School</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('assets/images/image.png')); ?>">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('assets/img/apple-touch-icon.png')); ?>">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/icons/feather/feather.css')); ?>">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/tabler-icons/tabler-icons.min.css')); ?>">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/select2/css/select2.min.css')); ?>">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/fontawesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/all.min.css')); ?>">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap-datetimepicker.min.css')); ?>">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/flatpickr/flatpickr.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/@simonwep/pickr/themes/nano.min.css')); ?>">

    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/daterangepicker/daterangepicker.css')); ?>">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dataTables.bootstrap5.min.css')); ?>">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/select2/css/select2.min.css')); ?>">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>?v=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive-sidebar.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>

    <!-- Global pagination UI fixes to avoid oversized arrow overlays -->
    <style>
        /* Ensure pagination layout is consistent across admin pages */
        .pagination {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 0.25rem !important;
            align-items: center !important;
            padding-left: 0 !important;
            margin: 0 !important;
            list-style: none !important;
        }

        .pagination .page-item {
            display: inline-block !important;
        }

        .pagination .page-link {
            display: inline-block !important;
            width: auto !important;
            height: auto !important;
            max-width: none !important;
            max-height: none !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
        }

        /* Remove any oversized pseudo-elements that may be injected by other styles */
        .pagination .page-link::before,
        .pagination .page-link::after {
            content: none !important;
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            font-size: 0 !important;
        }

        /* Hide unrelated icon fonts inside pagination that could overflow */
        .pagination [class*="ti-"],
        .pagination [class*="bx-"],
        .pagination [class*="ri-"],
        .pagination [class*="fa-"] {
            display: none !important;
        }
    </style>

</head>

<body>
    <div id="global-loader" style="display: none;">
        <div class="page-loader"></div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Logo -->
            <div class="sidebar-logo">
                <a href="index.html" class="logo logo-normal">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo" style="max-height: 40px; width: auto;">
                </a>
                <a href="index.html" class="logo-small">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo" style="max-height: 35px; width: auto;">
                </a>
                <a href="index.html" class="dark-logo">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo" style="max-height: 40px; width: auto;">
                </a>
            </div>
            <!-- /Logo -->
            <div class="modern-profile p-3 pb-0">
                <div class="text-center rounded bg-light p-3 mb-4 user-profile">
                    <div class="avatar avatar-lg online mb-3">
                        <img src="<?php echo e(asset('assets/img/profiles/avatar-02.jpg')); ?>" alt="Img" class="img-fluid rounded-circle">
                    </div>
                    <h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
                    <p class="fs-10">System Admin</p>
                </div>
                <div class="sidebar-nav mb-3">
                    <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent"
                        role="tablist">
                        <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                        <li class="nav-item"><a class="nav-link border-0" href="<?php echo e(route('admin.messages.messenger')); ?>">Chats</a></li>
                        <li class="nav-item">
                            <a class="nav-link border-0 position-relative" href="<?php echo e(route('admin.message-reports.index')); ?>">
                                Reports
                                <?php
                                    $pendingCount = \App\Models\MessageReport::where('status', 'pending')->count();
                                ?>
                                <?php if($pendingCount > 0): ?>
                                    <span class="badge bg-warning rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size: 9px;"><?php echo e($pendingCount); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="sidebar-header p-3 pb-0 pt-2">
                <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
                    <div class="avatar avatar-md onlin">
                        <img src="<?php echo e(asset('assets/img/profiles/avatar-02.jpg')); ?>" alt="Img" class="img-fluid rounded-circle">
                    </div>
                    <div class="text-start sidebar-profile-info ms-2">
                        <h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
                        <p class="fs-10">System Admin</p>
                    </div>
                </div>
                <div class="input-group input-group-flat d-inline-flex mb-4">
                    <span class="input-icon-addon">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search in HRMS">
                    <span class="input-group-text">
                        <kbd>CTRL + / </kbd>
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between menu-item mb-3">
                    <div class="me-3">
                        <a href="calendar.html" class="btn btn-menubar">
                            <i class="ti ti-layout-grid-remove"></i>
                        </a>
                    </div>
                    <div class="me-3">
                        <a href="<?php echo e(route('admin.messages.messenger')); ?>" class="btn btn-menubar position-relative">
                            <i class="ti ti-brand-hipchat"></i>
                            <span
                                class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
                        </a>
                    </div>
                    <div class="me-3 notification-item">
                        <a href="activity.html" class="btn btn-menubar position-relative me-1">
                            <i class="ti ti-bell"></i>
                            <span class="notification-status-dot"></span>
                        </a>
                    </div>
                    <div class="me-0">
                        <a href="<?php echo e(route('admin.messages.messenger')); ?>" class="btn btn-menubar">
                            <i class="ti ti-message"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu" style="padding-bottom: 100px;">
                    <?php
                        $isTeachers = request()->routeIs('admin.teachers.*');
                        $isStudents = request()->routeIs('admin.students.*');
                        $isGuardians = request()->routeIs('admin.guardians.*');
                        $isEnrollments = request()->routeIs('admin.student-enrollments.*');
                        $isPreEnrollments = request()->routeIs('admin.pre-enrollments.*');
                        $isManagement = request()->routeIs('admin.subjects.*') || request()->routeIs('admin.strands.*') || request()->routeIs('admin.sections.*');
                        $isAcademic = request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.subject-records.*') || request()->routeIs('admin.assessment-types.*') || request()->routeIs('admin.subject-record-results.*');
                    ?>
                    <ul>
                        <li class="menu-title"><span>Administrator</span></li>
                        <li>
                            <ul>
                                <li class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                                        <i class="ti ti-layout-navbar"></i><span>Dashboard</span>
                                    </a>
                                </li>

                                <li class="<?php echo e(request()->routeIs('admin.profile.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.profile.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.profile.show')); ?>">
                                        <i class="ti ti-user-circle"></i><span>My Profile</span>
                                    </a>
                                </li>

                                <li class="<?php echo e($isTeachers ? 'active' : ''); ?>">
                                    <a class="<?php echo e($isTeachers ? 'active' : ''); ?>" href="<?php echo e(route('admin.teachers.index')); ?>">
                                        <i class="ti ti-users"></i><span>Teachers</span>
                                    </a>
                                </li>

                                <?php if(auth()->guard('admin')->check()): ?>
                                <li class="<?php echo e(request()->routeIs('admin.co-admins.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.co-admins.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.co-admins.index')); ?>">
                                        <i class="ti ti-user-star"></i><span>Co-Admins</span>
                                    </a>
                                </li>
                                <?php endif; ?>

                                <li class="<?php echo e($isStudents ? 'active' : ''); ?>">
                                    <a class="<?php echo e($isStudents ? 'active' : ''); ?>" href="<?php echo e(route('admin.students.index')); ?>">
                                        <i class="ti ti-layout-board-split"></i><span>Student</span>
                                    </a>
                                </li>

                                <li class="<?php echo e($isGuardians ? 'active' : ''); ?>">
                                    <a class="<?php echo e($isGuardians ? 'active' : ''); ?>" href="<?php echo e(route('admin.guardians.index')); ?>">
                                        <i class="ti ti-user-shield"></i><span>Guardians</span>
                                    </a>
                                </li>

                                <li class="<?php echo e($isEnrollments ? 'active' : ''); ?>">
                                    <a class="<?php echo e($isEnrollments ? 'active' : ''); ?>" href="<?php echo e(route('admin.student-enrollments.index')); ?>">
                                        <i class="ti ti-users-group"></i><span>Enrollments</span>
                                    </a>
                                </li>

                                <li class="<?php echo e($isPreEnrollments ? 'active' : ''); ?>">
                                    <a class="<?php echo e($isPreEnrollments ? 'active' : ''); ?>" href="<?php echo e(route('admin.pre-enrollments.index')); ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <i class="ti ti-file-text"></i><span style="flex: 1; overflow: hidden; text-overflow: ellipsis;">Pre-Enrollment Submissions</span>
                                        <?php
                                            $pendingPreEnrollments = \App\Models\PreEnrollment::where('status', 'pending')->count();
                                        ?>
                                        <?php if($pendingPreEnrollments > 0): ?>
                                            <span class="badge bg-warning rounded-pill ms-2" style="flex-shrink: 0;"><?php echo e($pendingPreEnrollments); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>

                                <!-- <li class="<?php echo e(request()->routeIs('admin.attendance.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.attendance.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.attendance.index')); ?>">
                                        <i class="ti ti-calendar-check"></i><span>Attendance</span>
                                    </a>
                                </li> -->

                                <li class="<?php echo e(request()->routeIs('admin.announcements.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.announcements.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.announcements.index')); ?>">
                                        <i class="ti ti-speakerphone"></i><span>Announcements</span>
                                    </a>
                                </li>

                                <li class="<?php echo e(request()->routeIs('admin.section-advisers.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.section-advisers.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.section-advisers.index')); ?>">
                                        <i class="ti ti-users-group"></i><span>Section & Advisers</span>
                                    </a>
                                </li>

                                <li class="<?php echo e(request()->routeIs('admin.assigning-list.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.assigning-list.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.assigning-list.index')); ?>">
                                        <i class="ti ti-list-check"></i><span>Assigning List</span>
                                    </a>
                                </li>

                                <li class="<?php echo e(request()->routeIs('admin.archive.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.archive.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.archive.index')); ?>">
                                        <i class="ti ti-archive"></i><span>Archive</span>
                                    </a>
                                </li>

                                <?php if(auth()->guard('admin')->check()): ?>
                                <li class="<?php echo e(request()->routeIs('admin.messages.*') && !request()->routeIs('admin.message-reports.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.messages.*') && !request()->routeIs('admin.message-reports.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.messages.messenger')); ?>">
                                        <i class="ti ti-mail"></i><span>Messages</span>
                                    </a>
                                </li>

                                <li class="<?php echo e(request()->routeIs('admin.message-reports.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.message-reports.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.message-reports.index')); ?>">
                                        <i class="ti ti-flag"></i><span>Message Reports</span>
                                        <?php
                                            $pendingReports = \App\Models\MessageReport::where('status', 'pending')->count();
                                        ?>
                                        <?php if($pendingReports > 0): ?>
                                            <span class="badge bg-warning rounded-pill ms-auto"><?php echo e($pendingReports); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                                <?php endif; ?>

                                <li class="<?php echo e(request()->routeIs('admin.sms.*') ? 'active' : ''); ?>">
                                    <a class="<?php echo e(request()->routeIs('admin.sms.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.sms.index')); ?>">
                                        <i class="ti ti-message-2"></i><span>SMS</span>
                                    </a>
                                </li>

                                <li class="submenu <?php echo e($isManagement ? 'active' : ''); ?>">
                                    <a href="javascript:void(0);" class="<?php echo e($isManagement ? 'subdrop' : ''); ?>">
                                        <i class="ti ti-smart-home"></i><span>Management</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul class="<?php echo e($isManagement ? 'd-block' : ''); ?>">
                                        <li><a style="background-color: white;" class="<?php echo e(request()->routeIs('admin.subjects.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.subjects.index')); ?>">Subject</a></li>
                                        <li><a style="background-color: white;" class="<?php echo e(request()->routeIs('admin.strands.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.strands.index')); ?>">Strand</a></li>
                                        <li><a style="background-color: white;" class="<?php echo e(request()->routeIs('admin.sections.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.sections.index')); ?>">Grade & Section</a></li>
                                    </ul>
                                </li>

                                <li class="submenu <?php echo e($isAcademic ? 'active' : ''); ?>">
                                    <a href="javascript:void(0);" class="<?php echo e($isAcademic ? 'subdrop' : ''); ?>">
                                        <i class="ti ti-smart-home"></i><span>Academic Schedule</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul class="<?php echo e($isAcademic ? 'd-block' : ''); ?>">
                                        <li><a style="background-color: white;" class="<?php echo e(request()->routeIs('admin.academic-years.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.academic-years.index')); ?>">Academic Year</a></li>
                                        <!-- <li><a style="background-color: white;" class="<?php echo e(request()->routeIs('admin.subject-records.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.subject-records.index')); ?>">Class Records</a></li> -->
                                        <!-- <li><a style="background-color: white;" class="<?php echo e(request()->routeIs('admin.assessment-types.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.assessment-types.index')); ?>">Assessment Types</a></li> -->
                                        <!-- <li><a style="background-color: white;" class="<?php echo e(request()->routeIs('admin.subject-record-results.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.subject-record-results.index')); ?>">Class Record Entries</a></li> -->
                                    </ul>
                                </li>

                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Logout in sidebar menu at the bottom -->
            <div class="sidebar-logout-block">
                <form action="<?php echo e(route('admin.auth.logout')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="ti ti-logout me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="sidebar-overlay"></div>
        <div class="page-wrapper">
            <header class="portal-mobile-header">
                <button type="button" class="mobile-menu-btn" aria-label="Toggle navigation" aria-controls="sidebar" aria-expanded="false">
                    <i class="ti ti-menu-2"></i>
                </button>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="portal-mobile-logo">
                    <img src="<?php echo e(asset('assets/images/SMAClogo.png')); ?>" alt="SMAC Logo">
                </a>
            </header>
            <div class="content">
                <?php echo $__env->yieldContent('breadcrumb'); ?>
                <?php echo $__env->yieldContent('content'); ?>
            </div>

            <div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
                <p class="mb-0">&copy; <?php echo e(date('Y')); ?> @ St. Matthew Senior High School.</p>
            </div>

        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="<?php echo e(asset('assets/js/jquery-3.7.1.min.js')); ?>"></script>

    <!-- Setup CSRF Token for AJAX requests -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Bootstrap Core JS -->
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>

    <!-- Feather Icon JS -->
    <script src="<?php echo e(asset('assets/js/feather.min.js')); ?>"></script>

    <!-- Slimscroll JS -->
    <script src="<?php echo e(asset('assets/js/jquery.slimscroll.min.js')); ?>"></script>

    <!-- Color Picker JS -->
    <script src="<?php echo e(asset('assets/plugins/@simonwep/pickr/pickr.es5.min.js')); ?>"></script>

    <!-- Datatable JS -->
    <script src="<?php echo e(asset('assets/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/dataTables.bootstrap5.min.js')); ?>"></script>

    <!-- Daterangepikcer JS -->
    <script src="<?php echo e(asset('assets/js/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/daterangepicker/daterangepicker.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap-datetimepicker.min.js')); ?>"></script>

    <!-- Select2 JS -->
    <script src="<?php echo e(asset('assets/plugins/select2/js/select2.min.js')); ?>"></script>

    <!-- Chart JS -->
    <script src="<?php echo e(asset('assets/plugins/apexchart/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/apexchart/chart-data.js')); ?>"></script>

    <!-- Custom JS -->
    <script src="<?php echo e(asset('assets/js/theme-colorpicker.js')); ?>"></script>
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
                    window.location.href = "<?php echo e(route('admin.auth.loginForm')); ?>";
                };
            }
        })();
    </script>
    <script src="<?php echo e(asset('assets/js/responsive-sidebar.js')); ?>"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/components/template.blade.php ENDPATH**/ ?>