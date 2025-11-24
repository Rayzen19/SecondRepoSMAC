<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="St. Matthew Senior High School">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>St. Matthew Senior High School</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/Image.png') }}">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive-sidebar.css') }}">

    @stack('styles')

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
                    <img src="{{ asset('assets/images/SMAClogo.png') }}" alt="SMAC Logo" style="max-height: 40px; width: auto;">
                </a>
                <a href="index.html" class="logo-small">
                    <img src="{{ asset('assets/images/SMAClogo.png') }}" alt="SMAC Logo" style="max-height: 35px; width: auto;">
                </a>
                <a href="index.html" class="dark-logo">
                    <img src="{{ asset('assets/images/SMAClogo.png') }}" alt="SMAC Logo" style="max-height: 40px; width: auto;">
                </a>
            </div>
            <!-- /Logo -->
            <div class="modern-profile p-3 pb-0">
                <div class="text-center rounded bg-light p-3 mb-4 user-profile">
                    <div class="avatar avatar-lg online mb-3">
                        <img src="{{ asset('assets/img/profiles/avatar-02.jpg') }}" alt="Img" class="img-fluid rounded-circle">
                    </div>
                    <h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
                    <p class="fs-10">System Admin</p>
                </div>
                <div class="sidebar-nav mb-3">
                    <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent"
                        role="tablist">
                        <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                        <li class="nav-item"><a class="nav-link border-0" href="{{ route('admin.messages.messenger') }}">Chats</a></li>
                        <li class="nav-item">
                            <a class="nav-link border-0 position-relative" href="{{ route('admin.message-reports.index') }}">
                                Reports
                                @php
                                    $pendingCount = \App\Models\MessageReport::where('status', 'pending')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="badge bg-warning rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size: 9px;">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="sidebar-header p-3 pb-0 pt-2">
                <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
                    <div class="avatar avatar-md onlin">
                        <img src="{{ asset('assets/img/profiles/avatar-02.jpg') }}" alt="Img" class="img-fluid rounded-circle">
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
                        <a href="{{ route('admin.messages.messenger') }}" class="btn btn-menubar position-relative">
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
                        <a href="{{ route('admin.messages.messenger') }}" class="btn btn-menubar">
                            <i class="ti ti-message"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    @php
                        $isTeachers = request()->routeIs('admin.teachers.*');
                        $isStudents = request()->routeIs('admin.students.*');
                        $isGuardians = request()->routeIs('admin.guardians.*');
                        $isEnrollments = request()->routeIs('admin.student-enrollments.*');
                        $isPreEnrollments = request()->routeIs('admin.pre-enrollments.*');
                        $isManagement = request()->routeIs('admin.subjects.*') || request()->routeIs('admin.strands.*') || request()->routeIs('admin.sections.*');
                        $isAcademic = request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.subject-records.*') || request()->routeIs('admin.assessment-types.*') || request()->routeIs('admin.subject-record-results.*');
                    @endphp
                    <ul>
                        <li class="menu-title"><span>Administrator</span></li>
                        <li>
                            <ul>
                                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                        <i class="ti ti-layout-navbar"></i><span>Dashboard</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.show') }}">
                                        <i class="ti ti-user-circle"></i><span>My Profile</span>
                                    </a>
                                </li>

                                <li class="{{ $isTeachers ? 'active' : '' }}">
                                    <a class="{{ $isTeachers ? 'active' : '' }}" href="{{ route('admin.teachers.index') }}">
                                        <i class="ti ti-users"></i><span>Teachers</span>
                                    </a>
                                </li>

                                @auth('admin')
                                <li class="{{ request()->routeIs('admin.co-admins.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.co-admins.*') ? 'active' : '' }}" href="{{ route('admin.co-admins.index') }}">
                                        <i class="ti ti-user-star"></i><span>Co-Admins</span>
                                    </a>
                                </li>
                                @endauth

                                <li class="{{ $isStudents ? 'active' : '' }}">
                                    <a class="{{ $isStudents ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                                        <i class="ti ti-layout-board-split"></i><span>Student</span>
                                    </a>
                                </li>

                                <li class="{{ $isGuardians ? 'active' : '' }}">
                                    <a class="{{ $isGuardians ? 'active' : '' }}" href="{{ route('admin.guardians.index') }}">
                                        <i class="ti ti-user-shield"></i><span>Guardians</span>
                                    </a>
                                </li>

                                <li class="{{ $isEnrollments ? 'active' : '' }}">
                                    <a class="{{ $isEnrollments ? 'active' : '' }}" href="{{ route('admin.student-enrollments.index') }}">
                                        <i class="ti ti-users-group"></i><span>Enrollments</span>
                                    </a>
                                </li>

                                <li class="{{ $isPreEnrollments ? 'active' : '' }}">
                                    <a class="{{ $isPreEnrollments ? 'active' : '' }}" href="{{ route('admin.pre-enrollments.index') }}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <i class="ti ti-file-text"></i><span style="flex: 1; overflow: hidden; text-overflow: ellipsis;">Pre-Enrollment Submissions</span>
                                        @php
                                            $pendingPreEnrollments = \App\Models\PreEnrollment::where('status', 'pending')->count();
                                        @endphp
                                        @if($pendingPreEnrollments > 0)
                                            <span class="badge bg-warning rounded-pill ms-2" style="flex-shrink: 0;">{{ $pendingPreEnrollments }}</span>
                                        @endif
                                    </a>
                                </li>

                                <!-- <li class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                                        <i class="ti ti-calendar-check"></i><span>Attendance</span>
                                    </a>
                                </li> -->

                                <li class="{{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
                                        <i class="ti ti-speakerphone"></i><span>Announcements</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('admin.section-advisers.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.section-advisers.*') ? 'active' : '' }}" href="{{ route('admin.section-advisers.index') }}">
                                        <i class="ti ti-users-group"></i><span>Section & Advisers</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('admin.assigning-list.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.assigning-list.*') ? 'active' : '' }}" href="{{ route('admin.assigning-list.index') }}">
                                        <i class="ti ti-list-check"></i><span>Assigning List</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('admin.archive.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.archive.*') ? 'active' : '' }}" href="{{ route('admin.archive.index') }}">
                                        <i class="ti ti-archive"></i><span>Archive</span>
                                    </a>
                                </li>

                                @auth('admin')
                                <li class="{{ request()->routeIs('admin.messages.*') && !request()->routeIs('admin.message-reports.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.messages.*') && !request()->routeIs('admin.message-reports.*') ? 'active' : '' }}" href="{{ route('admin.messages.messenger') }}">
                                        <i class="ti ti-mail"></i><span>Messages</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('admin.message-reports.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.message-reports.*') ? 'active' : '' }}" href="{{ route('admin.message-reports.index') }}">
                                        <i class="ti ti-flag"></i><span>Message Reports</span>
                                        @php
                                            $pendingReports = \App\Models\MessageReport::where('status', 'pending')->count();
                                        @endphp
                                        @if($pendingReports > 0)
                                            <span class="badge bg-warning rounded-pill ms-auto">{{ $pendingReports }}</span>
                                        @endif
                                    </a>
                                </li>
                                @endauth

                                <li class="{{ request()->routeIs('admin.sms.*') ? 'active' : '' }}">
                                    <a class="{{ request()->routeIs('admin.sms.*') ? 'active' : '' }}" href="{{ route('admin.sms.index') }}">
                                        <i class="ti ti-message-2"></i><span>SMS</span>
                                    </a>
                                </li>

                                <li class="submenu {{ $isManagement ? 'active' : '' }}">
                                    <a href="javascript:void(0);" class="{{ $isManagement ? 'subdrop' : '' }}">
                                        <i class="ti ti-smart-home"></i><span>Management</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul class="{{ $isManagement ? 'd-block' : '' }}">
                                        <li><a style="background-color: white;" class="{{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}" href="{{ route('admin.subjects.index') }}">Subject</a></li>
                                        <li><a style="background-color: white;" class="{{ request()->routeIs('admin.strands.*') ? 'active' : '' }}" href="{{ route('admin.strands.index') }}">Strand</a></li>
                                        <li><a style="background-color: white;" class="{{ request()->routeIs('admin.sections.*') ? 'active' : '' }}" href="{{ route('admin.sections.index') }}">Grade & Section</a></li>
                                    </ul>
                                </li>

                                <li class="submenu {{ $isAcademic ? 'active' : '' }}">
                                    <a href="javascript:void(0);" class="{{ $isAcademic ? 'subdrop' : '' }}">
                                        <i class="ti ti-smart-home"></i><span>Academic Schedule</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul class="{{ $isAcademic ? 'd-block' : '' }}">
                                        <li><a style="background-color: white;" class="{{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}" href="{{ route('admin.academic-years.index') }}">Academic Year</a></li>
                                        <!-- <li><a style="background-color: white;" class="{{ request()->routeIs('admin.subject-records.*') ? 'active' : '' }}" href="{{ route('admin.subject-records.index') }}">Class Records</a></li> -->
                                        <!-- <li><a style="background-color: white;" class="{{ request()->routeIs('admin.assessment-types.*') ? 'active' : '' }}" href="{{ route('admin.assessment-types.index') }}">Assessment Types</a></li> -->
                                        <!-- <li><a style="background-color: white;" class="{{ request()->routeIs('admin.subject-record-results.*') ? 'active' : '' }}" href="{{ route('admin.subject-record-results.index') }}">Class Record Entries</a></li> -->
                                    </ul>
                                </li>

                            </ul>
                        </li>
                    </ul>
                    <!-- Logout in sidebar menu at the bottom -->
                    <div class="sidebar-logout-block">
                        <form action="{{ route('admin.auth.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="ti ti-logout me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                            </ul>
                        </li>
                    </ul>
                </div>
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
                <a href="{{ route('admin.dashboard') }}" class="portal-mobile-logo">
                    <img src="{{ asset('assets/images/SMAClogo.png') }}" alt="SMAC Logo">
                </a>
            </header>
            <div class="content">
                @yield('breadcrumb')
                @yield('content')
            </div>

            <div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
                <p class="mb-0">&copy; {{ date('Y') }} @ St. Matthew Senior High School.</p>
            </div>

        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Setup CSRF Token for AJAX requests -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('assets/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Datatable JS -->
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Daterangepikcer JS -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
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
                    window.location.href = "{{ route('admin.auth.loginForm') }}";
                };
            }
        })();
    </script>
    <script src="{{ asset('assets/js/responsive-sidebar.js') }}"></script>

    @stack('scripts')

</body>

</html>