<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher Login</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-center">Teacher Login</h2>
        
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('teacher.auth.login') }}" id="teacher-login-form">
            @csrf
            <div class="mb-4">
                <label for="email" class="block mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="block mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-sm">Remember Me</span>
                </label>
            </div>
            <button type="submit" id="login-btn" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Login</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Refresh CSRF token when page loads (in case of back button)
            $.get('/csrf-token', function(data) {
                $('meta[name="csrf-token"]').attr('content', data.token);
                $('input[name="_token"]').val(data.token);
            }).fail(function() {
                console.log('Could not refresh CSRF token');
            });

            // Handle form submission with fresh token
            $('#teacher-login-form').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const button = $('#login-btn');
                const originalText = button.text();
                
                // Disable button and show loading
                button.prop('disabled', true).text('Logging in...');
                
                // Get fresh CSRF token and submit
                $.get('/csrf-token', function(data) {
                    // Update token in form
                    let tokenInput = form.find('input[name="_token"]');
                    if (tokenInput.length === 0) {
                        form.prepend('<input type="hidden" name="_token" value="' + data.token + '">');
                    } else {
                        tokenInput.val(data.token);
                    }
                    
                    // Submit form with fresh token
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response, status, xhr) {
                            // Check if response is redirect
                            if (xhr.getResponseHeader('Location')) {
                                window.location.href = xhr.getResponseHeader('Location');
                            } else {
                                // Submit form normally to let Laravel handle redirect
                                form.off('submit').submit();
                            }
                        },
                        error: function(xhr) {
                            button.prop('disabled', false).text(originalText);
                            
                            if (xhr.status === 419) {
                                alert('Session expired. Please refresh the page and try again.');
                                location.reload();
                            } else if (xhr.status === 422) {
                                // Validation errors
                                let errors = xhr.responseJSON.errors;
                                let errorHtml = '<div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded"><ul class="list-disc list-inside">';
                                for (let field in errors) {
                                    errors[field].forEach(function(error) {
                                        errorHtml += '<li>' + error + '</li>';
                                    });
                                }
                                errorHtml += '</ul></div>';
                                $('.bg-red-100').remove(); // Remove old errors
                                $('h2').after(errorHtml);
                            } else {
                                // For other responses, submit form normally
                                form.off('submit').submit();
                            }
                        }
                    });
                }).fail(function() {
                    // If token refresh fails, try submitting anyway
                    button.prop('disabled', false).text(originalText);
                    form.off('submit').submit();
                });
                
                return false;
            });
        });
    </script>
</body>
</html>
