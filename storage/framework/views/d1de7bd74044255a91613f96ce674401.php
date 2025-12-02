<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Account Created - <?php echo e($appName); ?></title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#111;">
    <div style="max-width:600px;margin:0 auto;padding:24px;">
        <h2 style="margin:0 0 12px;">Dear <?php echo e($guardianName); ?>,</h2>
        
        <p style="margin:0 0 16px;">
            We are pleased to inform you that a student account has been successfully created for your ward, 
            <strong><?php echo e($studentName); ?></strong>, in the <?php echo e($appName); ?> system.
        </p>

        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:16px;margin:16px 0;">
            <h3 style="margin:0 0 12px;color:#0369a1;">Student Information</h3>
            <p style="margin:0 0 8px;"><strong>Student Name:</strong> <?php echo e($studentName); ?></p>
            <p style="margin:0 0 8px;"><strong>Student Number:</strong> <?php echo e($studentNumber); ?></p>
        </div>

        <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:16px;margin:16px 0;">
            <h3 style="margin:0 0 12px;color:#92400e;">Student Login Credentials</h3>
            <p style="margin:0 0 8px;"><strong>Email:</strong> <?php echo e($studentEmail); ?></p>
            <p style="margin:0 0 8px;"><strong>Temporary Password:</strong> <?php echo e($studentPassword); ?></p>
        </div>

        <div style="background:#dcfce7;border:1px solid #86efac;border-radius:8px;padding:16px;margin:16px 0;">
            <h3 style="margin:0 0 12px;color:#166534;">Your Guardian Account Credentials</h3>
            <p style="margin:0 0 12px;">A guardian account has been created for you to monitor your ward's academic progress:</p>
            <p style="margin:0 0 8px;"><strong>Guardian Email:</strong> <?php echo e($guardianEmail); ?></p>
            <p style="margin:0 0 8px;"><strong>Guardian Password:</strong> <?php echo e($guardianPassword); ?></p>
        </div>

        <p style="margin:0 0 12px;">
            Please share the student credentials with your ward. You can use your guardian account credentials to log in 
            and monitor your ward's academic performance. Both accounts can access the portal using the link below, 
            and you will be prompted to change your passwords after the first login for security purposes.
        </p>

        <p style="margin:16px 0;">
            <a href="<?php echo e($loginUrl); ?>" 
               style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:12px 24px;border-radius:6px;font-weight:bold;">
                Access Portal
            </a>
        </p>

        <div style="background:#f7fafc;border-left:4px solid #2563eb;padding:12px 16px;margin:24px 0;">
            <p style="margin:0 0 8px;"><strong>Important Notes:</strong></p>
            <ul style="margin:0;padding-left:20px;">
                <li style="margin-bottom:6px;">Keep both sets of credentials secure and do not share them with unauthorized persons</li>
                <li style="margin-bottom:6px;">Both the student and you should change the temporary passwords upon first login</li>
                <li style="margin-bottom:6px;">You can use your guardian account to monitor your ward's academic progress, grades, and attendance</li>
                <li style="margin-bottom:6px;">The student account is for your ward to access their courses and assignments</li>
                <li>For any assistance, please contact the school administration</li>
            </ul>
        </div>

        <p style="margin-top:24px;font-size:12px;color:#555;">
            If you did not request this account or believe this email was sent in error, 
            please contact the school administration immediately.
        </p>

        <p style="margin-top:24px;padding-top:16px;border-top:1px solid #e2e8f0;">
            Best regards,<br>
            <strong><?php echo e(config('app.name')); ?> Administration</strong>
        </p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/emails/guardian_notification.blade.php ENDPATH**/ ?>