<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="St. Matthew Senior High School - Login">
    <title>Login - St. Matthew Academy of Cavite</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/image.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <style>
        .account-card {
            transition: all 0.3s ease;
            border: 2px solid #dee2e6;
            cursor: pointer;
        }
        .account-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-color: #0dcaf0;
        }
        .account-icon {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 20px;
            font-size: 3rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <img src="{{ asset('assets/images/image.png') }}" alt="SMAC Logo" height="100" class="mb-3">
                    <h2 class="fw-bold mb-2">Sign in to Your Account</h2>
                    <p class="text-muted">Choose your account type to continue</p>
                </div>

                <div class="row g-4">
                    <!-- Student Login -->
                    <div class="col-md-4">
                        <a href="{{ url('/student/login') }}" class="text-decoration-none">
                            <div class="card account-card h-100">
                                <div class="card-body text-center p-4">
                                    <div class="account-icon bg-primary bg-opacity-10 text-primary">
                                        <i class="ti ti-school"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2">Student</h4>
                                    <p class="text-muted mb-0">Login to access your grades, schedules, and academic information</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Guardian Login -->
                    <div class="col-md-4">
                        <a href="{{ url('/guardian/login') }}" class="text-decoration-none">
                            <div class="card account-card h-100">
                                <div class="card-body text-center p-4">
                                    <div class="account-icon bg-success bg-opacity-10 text-success">
                                        <i class="ti ti-users"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2">Guardian</h4>
                                    <p class="text-muted mb-0">Login to monitor your student's progress and grades</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Admin/Teacher Login -->
                    <div class="col-md-4">
                        <a href="{{ url('/admin/login') }}" class="text-decoration-none">
                            <div class="card account-card h-100">
                                <div class="card-body text-center p-4">
                                    <div class="account-icon bg-info bg-opacity-10 text-info">
                                        <i class="ti ti-lock"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2">Admin / Teacher</h4>
                                    <p class="text-muted mb-0">Login to manage school operations and student records</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="ti ti-arrow-left me-1"></i> Back to Home
                    </a>
                </div>

                <p class="text-center text-muted small mt-4">&copy; {{ date('Y') }} St. Matthew Senior High School</p>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
