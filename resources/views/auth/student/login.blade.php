<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Login</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-center">Student Login</h2>
        <form method="POST" action="{{ route('student.auth.login') }}" id="student-login-form">
            @csrf
            <div class="mb-4">
                <label for="email" class="block mb-1">Email</label>
                <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="block mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
            </div>
            <button type="submit" id="login-btn" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700">Login</button>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            $.get('/csrf-token', function(data) {
                $('meta[name="csrf-token"]').attr('content', data.token);
            });
            
            $('#student-login-form').on('submit', function(e) {
                if ($(this).data('submitting')) return true;
                e.preventDefault();
                
                const form = $(this);
                const btn = $('#login-btn');
                btn.prop('disabled', true).text('Logging in...');
                
                $.get('/csrf-token', function(data) {
                    let tokenInput = form.find('input[name="_token"]');
                    if (tokenInput.length === 0) {
                        form.prepend('<input type="hidden" name="_token" value="' + data.token + '">');
                    } else {
                        tokenInput.val(data.token);
                    }
                    form.data('submitting', true).off('submit').submit();
                }).fail(function() {
                    btn.prop('disabled', false).text('Login');
                    form.data('submitting', true).off('submit').submit();
                });
            });
        });
    </script>
</body>
</html>
