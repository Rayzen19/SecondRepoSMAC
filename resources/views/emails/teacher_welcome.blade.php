<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $appName }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .credentials {
            background-color: #fff;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #4F46E5;
            border-radius: 4px;
        }
        .credentials strong {
            color: #4F46E5;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
        .warning {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to {{ $appName }}</h1>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $teacherName }}</strong>,</p>
        
        <p>Welcome to {{ $appName }}! Your teacher account has been successfully created.</p>
        
        <p>You can now access the system using the following credentials:</p>
        
        <div class="credentials">
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
            <p><strong>Login URL:</strong> <a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>
        </div>
        
        <div class="warning">
            <strong>⚠ Important Security Notice:</strong>
            <p style="margin: 10px 0 0 0;">Please change your password immediately after your first login to ensure the security of your account.</p>
        </div>
        
        <center>
            <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>
        </center>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact the system administrator.</p>
        
        <p>Best regards,<br>
        <strong>{{ $appName }} Administration</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
    </div>
</body>
</html>
