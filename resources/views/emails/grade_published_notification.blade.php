<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grades Published</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 640px; margin: 0 auto; padding: 16px; }
        .btn { display: inline-block; background: #2f7cf3; color: #fff; padding: 10px 16px; border-radius: 6px; text-decoration: none; }
        .muted { color: #666; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Grades Published</h2>
    <p>Dear Guardian,</p>
    <p>Grades for <strong>{{ $subjectName }}</strong> have been published for student <strong>{{ $studentName }}</strong>.</p>
    <p>
        Strand: <strong>{{ $strandName }}</strong><br>
        School Year: <strong>{{ $schoolYearName }}</strong><br>
        Semester: <strong>{{ $semester }}</strong>
    </p>
    <p>You can log in to the Guardian Portal to view the details.</p>
    <p>
        <a class="btn" href="{{ url('/guardian/grades') }}">View Grades</a>
    </p>
    <p class="muted">If you have questions, please contact the subject teacher through the portal.</p>
    <p>Thank you,<br>SMAC</p>
</div>
</body>
</html>
