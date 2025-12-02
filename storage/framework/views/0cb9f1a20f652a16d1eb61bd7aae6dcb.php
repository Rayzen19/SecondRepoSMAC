<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Score Notification</title>
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
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .score-card {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #0d6efd;
        }
        .score-detail {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .score-detail:last-child {
            border-bottom: none;
        }
        .score-detail label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 150px;
        }
        .score-value {
            color: #212529;
        }
        .score-highlight {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
            text-align: center;
            padding: 15px;
            background-color: #e7f1ff;
            border-radius: 5px;
            margin: 15px 0;
        }
        .percentage {
            font-size: 18px;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-ww {
            background-color: #0dcaf0;
            color: white;
        }
        .badge-pt {
            background-color: #ffc107;
            color: #000;
        }
        .badge-qa {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">📊 Assessment Score Update</h2>
        <p style="margin: 5px 0 0 0;">St. Matthew Senior High School</p>
    </div>
    
    <div class="content">
        <p>Dear Parent/Guardian,</p>
        
        <p>This is to inform you that a new assessment score has been recorded for <strong><?php echo e($studentName); ?></strong>.</p>
        
        <div class="score-card">
            <h3 style="margin-top: 0; color: #0d6efd;">Assessment Details</h3>
            
            <div class="score-detail">
                <label>Assessment:</label>
                <span class="score-value"><?php echo e($assessmentName); ?></span>
            </div>
            
            <div class="score-detail">
                <label>Type:</label>
                <span class="badge 
                    <?php if($assessmentType === 'WW'): ?> badge-ww
                    <?php elseif($assessmentType === 'PT'): ?> badge-pt
                    <?php elseif($assessmentType === 'QA'): ?> badge-qa
                    <?php endif; ?>">
                    <?php echo e($assessmentType); ?>

                </span>
            </div>
            
            <div class="score-detail">
                <label>Subject:</label>
                <span class="score-value"><?php echo e($subject); ?></span>
            </div>
            
            <div class="score-detail">
                <label>Academic Year:</label>
                <span class="score-value"><?php echo e($academicYear); ?></span>
            </div>
            
            <div class="score-detail">
                <label>Term:</label>
                <span class="score-value"><?php echo e($term); ?></span>
            </div>
            
            <div class="score-detail">
                <label>Date Given:</label>
                <span class="score-value"><?php echo e($dateGiven); ?></span>
            </div>
        </div>
        
        <div class="score-highlight">
            Score: <?php echo e($rawScore); ?> / <?php echo e($maxScore); ?>

            <div class="percentage">(<?php echo e($percentage); ?>%)</div>
        </div>
        
        <p style="color: #6c757d; font-size: 14px; margin-top: 20px;">
            <strong>Note:</strong> This is an automated notification sent when a teacher records or updates a student's assessment score. 
            You can log in to the student portal to view complete grade details and academic progress.
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from St. Matthew Senior High School Student Management System.</p>
        <p>Please do not reply to this email.</p>
        <p>&copy; <?php echo e(date('Y')); ?> St. Matthew Senior High School. All rights reserved.</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/emails/score_notification.blade.php ENDPATH**/ ?>