<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found | St. Matthew Academy of Cavite</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        }

        .overlay {
            background: rgba(255, 255, 255, 0.92);
            padding: 2.5rem 3.5rem;
            border-radius: 2rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            text-align: center;
            max-width: 820px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 2px 8px #e3342f22);
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .error-code {
            font-size: 5rem;
            font-weight: 900;
            color: #e3342f;
            margin-bottom: 0.25rem;
            letter-spacing: 0.1em;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: #22223b;
            margin-bottom: 0.5rem;
        }

        .error-desc {
            font-size: 1.15rem;
            color: #444;
            margin-bottom: 1.2rem;
        }

        .tips {
            font-size: 1rem;
            color: #555;
            margin-bottom: 1.5rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border-left: 4px solid #e3342f;
            text-align: left;
        }

        .btn {
            background: #e3342f;
            color: #fff;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
            display: inline-block;
            margin-top: 0.5rem;
            box-shadow: 0 2px 8px #e3342f22;
        }

        .btn:hover {
            background: #cc1f1a;
        }

        .links {
            margin-top: 1.5rem;
            font-size: 0.98rem;
        }

        .links a {
            color: #e3342f;
            text-decoration: underline;
            margin: 0 0.5rem;
            transition: color 0.2s;
        }

        .links a:hover {
            color: #cc1f1a;
        }
    </style>
</head>

<body>
    <div class="overlay">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo"> St. Matthew Academy of Cavite <div class="error-code">404</div>

        <body style="background: linear-gradient(135deg, #f8fafc 60%, #e3342f10 100%); background-image: url('{{ asset('assets/images/logo.png') }}'), linear-gradient(135deg, #f8fafc 60%, #e3342f10 100%); background-repeat: no-repeat; background-position: center 15vh, center center; background-size: 30vw auto, cover;">
            <div class="overlay" style="margin-bottom: 50px;">
                Oops! The page you’re looking for doesn’t exist or has been moved.<br>
                Don’t worry, let’s help you get back on track.
            </div>
            <div class="tips">
                <strong>Here are some things you can try:</strong>
                <ul style="margin:0.5em 0 0 1.2em; padding:0;">
                    <li>Check the URL for typos or errors.</li>
                    <li>Return to the <a href="{{ url('/') }}">homepage</a>.</li>
                    <li>Use the navigation menu to find what you need.</li>
                    <li>If you believe this is an error, <a href="mailto:support@tschool.com">contact support</a>.</li>
                </ul>
            </div>
            <a href="{{ url('/') }}" class="btn">Go to Home</a>
            <div class="links">
                <a href="{{ url('/courses') }}">Browse Courses</a> |
                <a href="{{ url('/about') }}">About Us</a> |
                <a href="{{ url('/contact') }}">Contact</a>
            </div>
    </div>
</body>

</html>