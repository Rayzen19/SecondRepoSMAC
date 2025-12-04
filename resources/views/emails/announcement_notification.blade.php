<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Announcement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.95;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #212529;
            margin-bottom: 20px;
        }
        .announcement-card {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            border-radius: 4px;
            padding: 20px;
            margin: 25px 0;
        }
        .announcement-title {
            font-size: 22px;
            font-weight: bold;
            color: #0d6efd;
            margin: 0 0 15px 0;
        }
        .announcement-content {
            color: #495057;
            font-size: 15px;
            line-height: 1.8;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .announcement-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            margin: 20px 0;
            display: block;
        }
        .announcement-meta {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 13px;
            color: #6c757d;
        }
        .meta-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 8px;
        }
        .meta-label {
            font-weight: 600;
            color: #495057;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #28a745;
            color: white;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .cta-section {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .cta-button:hover {
            background-color: #0b5ed7;
        }
        .info-box {
            background-color: #e7f1ff;
            border: 1px solid #b6d4fe;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #084298;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #6c757d;
        }
        .divider {
            height: 1px;
            background-color: #dee2e6;
            margin: 25px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">📢</div>
            <h1>New Announcement</h1>
            <p>{{ $appName ?? 'St. Matthew Senior High School' }}</p>
        </div>
        
        <div class="content">
            <p class="greeting">
                Dear 
                @if($recipientType === 'student')
                    Student {{ $recipientName }},
                @elseif($recipientType === 'teacher')
                    {{ $recipientName }},
                @elseif($recipientType === 'guardian')
                    Parent/Guardian {{ $recipientName }},
                @else
                    {{ $recipientName }},
                @endif
            </p>
            
            <p>A new announcement has been posted on the school portal. Please find the details below:</p>
            
            <div class="announcement-card">
                <h2 class="announcement-title">{{ $announcement->title }}</h2>
                
                @if($announcement->hasImage())
                    @php
                        // Ensure absolute URL for email clients
                        $imageUrl = $announcement->image_path 
                            ? config('app.url') . '/storage/' . $announcement->image_path
                            : $announcement->image_url;
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $announcement->title }}" class="announcement-image">
                @endif
                
                <div class="announcement-content">{{ $announcement->content }}</div>
                
                <div class="announcement-meta">
                    <div class="meta-item">
                        <span class="meta-label">Status:</span>
                        <span class="badge">Active</span>
                    </div>
                    
                    @if($announcement->published_at)
                    <div class="meta-item">
                        <span class="meta-label">Published:</span>
                        {{ $announcement->published_at->format('F d, Y g:i A') }}
                    </div>
                    @endif
                    
                    @if($announcement->expires_at)
                    <div class="meta-item">
                        <span class="meta-label">Expires:</span>
                        {{ $announcement->expires_at->format('F d, Y g:i A') }}
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="cta-section">
                @if($recipientType === 'student')
                    <a href="{{ url('/student/dashboard') }}" class="cta-button">View on Student Portal</a>
                @elseif($recipientType === 'teacher')
                    <a href="{{ url('/teacher/dashboard') }}" class="cta-button">View on Teacher Portal</a>
                @elseif($recipientType === 'guardian')
                    <a href="{{ url('/guardian/dashboard') }}" class="cta-button">View on Guardian Portal</a>
                @else
                    <a href="{{ url('/') }}" class="cta-button">View on Portal</a>
                @endif
            </div>
            
            <div class="info-box">
                <strong>ℹ️ Note:</strong> This is an important announcement from the school. 
                Please log in to your portal for more details and to view any additional announcements.
            </div>
        </div>
        
        <div class="footer">
            <p><strong>{{ $appName ?? 'St. Matthew Senior High School' }}</strong></p>
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ $appName ?? 'St. Matthew Senior High School' }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
