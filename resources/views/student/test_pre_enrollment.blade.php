<!DOCTYPE html>
<html>
<head>
    <title>Pre-Enrollment Test</title>
</head>
<body>
    <h1>Pre-Enrollment Link Test</h1>
    <p>Click the link below to test pre-enrollment:</p>
    <a href="{{ route('student.pre-enrollment.index') }}" style="color: blue; text-decoration: underline; font-size: 18px;">
        Go to Pre-Enrollment
    </a>
    
    <hr>
    
    <h2>Debug Information</h2>
    <p><strong>URL:</strong> {{ route('student.pre-enrollment.index') }}</p>
    <p><strong>Auth Check:</strong> {{ Auth::guard('student')->check() ? 'YES - Logged in' : 'NO - Not logged in' }}</p>
    @if(Auth::guard('student')->check())
        <p><strong>Student:</strong> {{ Auth::guard('student')->user()->first_name }} {{ Auth::guard('student')->user()->last_name }}</p>
        <p><strong>Student ID:</strong> {{ Auth::guard('student')->user()->id }}</p>
    @endif
    
    <hr>
    
    <h2>Manual Navigation</h2>
    <form action="{{ route('student.pre-enrollment.index') }}" method="GET">
        <button type="submit" style="padding: 10px 20px; font-size: 16px;">
            Navigate to Pre-Enrollment (Form)
        </button>
    </form>
    
    <script>
        console.log('Pre-enrollment URL:', '{{ route("student.pre-enrollment.index") }}');
        console.log('Is authenticated:', {{ Auth::guard('student')->check() ? 'true' : 'false' }});
    </script>
</body>
</html>
