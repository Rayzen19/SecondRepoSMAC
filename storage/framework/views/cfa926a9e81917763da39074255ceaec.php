<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to <?php echo e($appName); ?></title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#111;">
    <div style="max-width:600px;margin:0 auto;padding:24px;">
        <h2 style="margin:0 0 12px;">Welcome, <?php echo e($studentName); ?>!</h2>
        <p style="margin:0 0 16px;">Your student account for <?php echo e($appName); ?> has been created.</p>

        <div style="background:#f7fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;margin:16px 0;">
            <p style="margin:0 0 8px;"><strong>Login Email:</strong> <?php echo e($email); ?></p>
            <p style="margin:0 0 8px;"><strong>Temporary Password:</strong> <?php echo e($password); ?></p>
        </div>

        <p style="margin:0 0 12px;">You can sign in using the link below and you'll be prompted to change your password after your first login:</p>

        <p>
            <a href="<?php echo e($loginUrl); ?>" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px;">Login to <?php echo e($appName); ?></a>
        </p>

        <p style="margin-top:16px;font-size:12px;color:#555;">
            For security, please do not share this password. If you didn't expect this email, you can ignore it.
        </p>

        <p style="margin-top:24px;">Thank you,<br><?php echo e(config('app.name')); ?> Team</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/emails/student_welcome.blade.php ENDPATH**/ ?>