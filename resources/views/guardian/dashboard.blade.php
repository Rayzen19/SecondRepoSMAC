@extends('guardian.components.template')

@section('content')
<div class="container-fluid">
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-1">Guardian Dashboard</h4>
                    <p class="text-muted mb-0">Welcome back.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian Dashboard</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/Image.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
    </head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-3">Guardian Dashboard</h1>
        <p class="text-muted">Welcome back.</p>
    </div>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
