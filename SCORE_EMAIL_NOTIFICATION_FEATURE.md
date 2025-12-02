# Score Email Notification Feature

## Overview
This feature automatically sends email notifications to guardians whenever a teacher inputs or updates a student's assessment score.

## How It Works

### When Scores Are Saved
1. Teacher enters scores in the Student Scores page
2. Clicks "Save Scores" button
3. System saves the scores to the database
4. System automatically sends email to all guardians associated with each student

### Email Recipients
The system sends emails to:
- All guardians linked to the student via the `guardian_students` table
- Legacy guardian email from `students.guardian_email` field (if exists)

### Email Content
Each email includes:
- **Student Name**: Full name of the student
- **Assessment Details**:
  - Assessment name (e.g., "Quiz 1", "WW1", "PT2")
  - Assessment type badge (WW/PT/QA)
  - Subject name and code
  - Academic year and semester
  - Term/Quarter (1st Quarter or 2nd Quarter)
  - Date given
- **Score Information**:
  - Raw score (e.g., 85)
  - Maximum score (e.g., 100)
  - Percentage (e.g., 85%)

## Files Created/Modified

### New Files
1. **app/Mail/ScoreNotification.php**
   - Mailable class for score notification emails
   - Handles email data and structure

2. **resources/views/emails/score_notification.blade.php**
   - Professional HTML email template
   - Responsive design with color-coded badges
   - School branding

### Modified Files
1. **app/Http/Controllers/Teacher/ScoresController.php**
   - Added `sendScoreNotification()` method
   - Integrated email sending in `store()` method
   - Added necessary imports (Mail, Log, ScoreNotification)

## Configuration Required

### Mail Settings (.env)
Make sure your `.env` file has proper mail configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="St. Matthew Senior High School"
```

### For Gmail:
1. Enable 2-Factor Authentication
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Use the app password in `MAIL_PASSWORD`

### For Other Email Providers:
Update the SMTP settings accordingly (host, port, encryption)

## Features

### ✅ Smart Email Handling
- Sends to multiple guardians if student has more than one
- Validates email addresses before sending
- Supports both new guardian relationship and legacy guardian_email field

### ✅ Error Handling
- Email failures don't prevent score saving
- Errors are logged for troubleshooting
- System continues to function even if email service is down

### ✅ Professional Design
- Color-coded assessment type badges:
  - **WW (Written Work)**: Blue badge
  - **PT (Performance Task)**: Yellow badge
  - **QA (Quarterly Assessment)**: Red badge
- Responsive layout works on desktop and mobile
- Clear score display with percentage calculation
- School branding and footer

### ✅ Batch Processing
- Sends individual emails for each student when multiple scores are saved
- Processes all guardians for each student
- Efficient and doesn't slow down the save operation

## Testing

### Test the Feature
1. Log in as a teacher
2. Go to Student Scores page
3. Select academic year, subject, section, and term
4. Enter a score for a student
5. Click "Save Scores"
6. Check the guardian's email inbox

### Verify Email Delivery
- Check spam/junk folder if email doesn't arrive
- Verify guardian has a valid email address
- Check Laravel logs: `storage/logs/laravel.log`

## Troubleshooting

### Email Not Sending
1. **Check mail configuration**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Test mail connection**
   - Check `.env` mail settings
   - Verify SMTP credentials
   - Test with a simple email

3. **Check logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Guardian Not Receiving Email
1. Verify guardian has email address:
   - Check `guardians.email` table
   - Check `students.guardian_email` field
2. Verify guardian is linked to student in `guardian_students` table
3. Check email is not in spam folder

## Customization

### Modify Email Template
Edit: `resources/views/emails/score_notification.blade.php`

### Modify Email Subject
Edit: `app/Mail/ScoreNotification.php` → `envelope()` method

### Add More Recipients
Modify: `app/Http/Controllers/Teacher/ScoresController.php` → `sendScoreNotification()` method

### Disable Email Notifications
Comment out this line in `ScoresController.php` store method:
```php
// $this->sendScoreNotification($scoreData['student_id'], $assessment, $scoreData['raw_score'], $scoreData['max_score'], $assignment);
```

## Future Enhancements

Potential improvements:
- [ ] Queue emails for better performance
- [ ] Add email notification preferences (guardian can opt-in/opt-out)
- [ ] Weekly summary emails instead of per-score
- [ ] SMS notifications as alternative
- [ ] Email templates for different languages
- [ ] Notification history log in database

## Notes

- Emails are sent immediately when scores are saved (synchronous)
- Consider using queues for large numbers of students
- Email sending failures don't affect score saving
- All email errors are logged for monitoring

---

**Created**: December 2, 2025  
**Feature**: Guardian Email Notifications for Assessment Scores  
**Status**: ✅ Active
