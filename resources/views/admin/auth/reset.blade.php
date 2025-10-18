<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="St. Matthew Senior High School">
    <meta name="robots" content="noindex, nofollow">
    <title>Reset Password</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/Image.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">
    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="bg-light">
    <div class="container py-5">
        @php
            $seg = request()->segment(1);
            $guard = in_array($seg, ['admin','teacher','student','guardian']) ? $seg : 'admin';
        @endphp
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/img/logo.svg') }}" alt="Logo" height="48">
                </div>
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="mb-3 text-center">Reset Password (OTP)</h4>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route($guard . '.auth.resetProcess') }}" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="otp" class="form-label">One-Time Password (6 digits)</label>
                                <input type="text" name="otp" id="otp" value="{{ old('otp') }}" class="form-control" required pattern="\d{6}" maxlength="6">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Reset Password</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a class="text-primary" href="{{ route($guard . '.auth.forgotForm') }}">Resend OTP</a>
                        </div>
                    </div>
                </div>
                <p class="text-center text-muted small mt-3">&copy; {{ date('Y') }} St. Matthew Senior High School</p>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script>
        feather.replace();
    </script>
</body>
</html>
