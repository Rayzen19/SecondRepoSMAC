@php
    $routeIs = fn($name) => request()->routeIs($name);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="robots" content="noindex, nofollow">
    <title>Teacher Portal</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
    <div class="main-wrapper">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <a href="{{ route('teacher.dashboard') }}" class="logo logo-normal">
                    <img src="{{ asset('assets/img/logo.svg') }}" alt=" Logo">
                </a>
                <a href="{{ route('teacher.dashboard') }}" class="logo-small">
                    <img src="{{ asset('assets/img/logo-small.svg') }}" alt=" Logo">
                </a>
                <a href="{{ route('teacher.dashboard') }}" class="dark-logo">
                    <img src="{{ asset('assets/img/logo-white.svg') }}" alt=" Logo">
                </a>
            </div>
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title"><span>Teacher</span></li>
                        <li>
                            <ul>
                                <li class="{{ $routeIs('teacher.dashboard') ? 'active' : '' }}">
                                    <a class="{{ $routeIs('teacher.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">
                                        <i class="ti ti-layout-navbar"></i><span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="{{ $routeIs('teacher.subjects.index') ? 'active' : '' }}">
                                    <a class="{{ $routeIs('teacher.subjects.index') ? 'active' : '' }}" href="{{ route('teacher.subjects.index') }}">
                                        <i class="ti ti-books"></i><span>Classes</span>
                                    </a>
                                </li>
                                <li class="{{ $routeIs('teacher.class-records.index') ? 'active' : '' }}">
                                    <a class="{{ $routeIs('teacher.class-records.index') ? 'active' : '' }}" href="{{ route('teacher.class-records.index') }}">
                                        <i class="ti ti-notebook"></i><span>Class Records</span>
                                    </a>
                                </li>
                                <li class="{{ $routeIs('teacher.profile.*') ? 'active' : '' }}">
                                    <a class="{{ $routeIs('teacher.profile.*') ? 'active' : '' }}" href="{{ route('teacher.profile.show') }}">
                                        <i class="ti ti-user"></i><span>My Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('teacher.auth.logout') }}" method="POST" class="d-inline">
                                        @csrf
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

        <div class="page-wrapper">
            <div class="content">
                @yield('content')
            </div>

            <div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
                <p class="mb-0">&copy; {{ date('Y') }} St. Matthew Senior High School</p>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>
    <!-- Datatable JS -->
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    @stack('modals')
    @stack('scripts')
</body>

</html>
