<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian Login</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-center">Guardian Login</h2>
        <form method="POST" action="{{ route('guardian.login.submit') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block mb-1">Email</label>
                <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="block mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
            </div>
            <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded hover:bg-orange-700">Login</button>
        </form>
    </div>
</body>
</html>
