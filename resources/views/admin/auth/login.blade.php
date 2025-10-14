<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="St. Matthew Senior High School">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - St. Matthew Senior High School</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">
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
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/img/logo.svg') }}" alt="Logo" height="48">
                </div>
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="mb-3 text-center">Sign in to Your Account</h4>
                        <p class="text-center text-muted small mb-4">For Admin, Teachers, Students, and Guardians</p>

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

                        @php
                            // This view is reused for admin and teacher login pages.
                            // Use the current URL as the form action so the POST is sent
                            // to the same origin/path as the GET. This avoids issues
                            // where generated absolute URLs (via route()) use APP_URL
                            // which may differ from the dev host and cause the
                            // session cookie/CSRF cookie to be rejected (leading to 419).
                            //
                            // We still rely on the current request path to determine
                            // context (admin vs teacher) for helper links.
                            $actionRoute = url()->current();
                        @endphp

                        <form method="POST" action="{{ $actionRoute }}" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="{{ route('admin.auth.forgotForm') }}" class="text-primary">Forgot password?</a>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ url('/') }}" class="btn btn-outline-secondary">Back</a>
                                <button type="submit" class="btn btn-primary w-100">Sign in</button>
                            </div>
                        </form>
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
