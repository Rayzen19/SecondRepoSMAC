<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .otp-box {
            background: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .otp-expiry {
            font-size: 13px;
            color: #e74c3c;
            margin-top: 10px;
            font-weight: 500;
        }
        .instructions {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .instructions h3 {
            margin-top: 0;
            color: #856404;
            font-size: 16px;
        }
        .instructions ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 5px 0;
            color: #856404;
        }
        .security-notice {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .security-notice strong {
            color: #721c24;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        .footer-info {
            margin: 10px 0;
        }
        .support-info {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 20px 15px;
            }
            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>🔐 Password Reset Request</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">{{ $appName }}</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Hello <strong>{{ $userName }}</strong>,
            </div>

            <p>We received a request to reset the password for your <strong>{{ $userType }}</strong> account at {{ $appName }}.</p>

            <p>Please use the following One-Time Password (OTP) to reset your password:</p>

            <!-- OTP Box -->
            <div class="otp-box">
                <div class="otp-label">Your OTP Code</div>
                <div class="otp-code">{{ $otpCode }}</div>
                <div class="otp-expiry">⏰ Valid for {{ $expiresIn }} minutes only</div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>📝 How to Reset Your Password:</h3>
                <ol>
                    <li>Go to the password reset page</li>
                    <li>Enter your email address</li>
                    <li>Enter the OTP code shown above</li>
                    <li>Create a new strong password</li>
                    <li>Confirm your new password</li>
                </ol>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <strong>⚠️ Security Notice:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This OTP will expire in <strong>{{ $expiresIn }} minutes</strong></li>
                    <li>If you didn't request this password reset, please ignore this email</li>
                    <li>Never share your OTP code with anyone</li>
                    <li>Our staff will never ask for your OTP code</li>
                </ul>
            </div>

            <p style="margin-top: 30px; color: #666;">
                If you're having trouble resetting your password, please contact the system administrator for assistance.
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-info">
                <strong>{{ $appName }}</strong><br>
                St. Matthew Academy of Cavite<br>
                School Management System
            </div>
            
            <div class="support-info">
                <p style="margin: 5px 0;">
                    <strong>Need Help?</strong><br>
                    Contact your system administrator or IT support team
                </p>
            </div>

            <p style="margin-top: 15px; font-size: 11px; color: #999;">
                This is an automated email. Please do not reply to this message.<br>
                © {{ date('Y') }} {{ $appName }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
