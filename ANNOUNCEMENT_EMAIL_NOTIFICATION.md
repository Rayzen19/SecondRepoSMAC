# Announcement Email Notification System

## Overview
This system automatically sends email notifications to all users (students, teachers, and guardians) when a new announcement is created or when an inactive announcement is activated.

## Features
- ✅ Automatic email notifications to all active users
- ✅ Queued job processing for better performance
- ✅ Beautiful, responsive email template
- ✅ Separate handling for students, teachers, and guardians
- ✅ Error logging and handling
- ✅ Batch processing (100 users per batch) to prevent memory issues
- ✅ Only sends to users with valid email addresses

## Files Created/Modified

### New Files Created:
1. **`app/Mail/AnnouncementNotification.php`**
   - Mailable class for announcement email notifications
   - Handles email structure and content

2. **`app/Jobs/SendAnnouncementNotifications.php`**
   - Background job that processes email sending
   - Sends emails in batches to prevent memory issues
   - Handles all three user types (students, teachers, guardians)

3. **`resources/views/emails/announcement_notification.blade.php`**
   - Beautiful, responsive email template
   - Displays announcement title, content, and image
   - Includes relevant metadata (publish date, expiration date)
   - Provides direct links to respective user portals

### Modified Files:
1. **`app/Http/Controllers/Admin/AnnouncementController.php`**
   - Added notification dispatch on announcement creation
   - Added notification dispatch when activating an announcement
   - Updated success messages to inform about email notifications

## How It Works

### When Creating a New Announcement:
1. Admin creates announcement via the web form
2. Announcement is saved to the database
3. `SendAnnouncementNotifications` job is dispatched to the queue
4. Job processes in background and sends emails to:
   - All active students with valid email addresses
   - All active teachers with valid email addresses
   - All active guardians with valid email addresses

### When Updating an Announcement:
1. If an inactive announcement is being activated, notifications are sent
2. If announcement is already active, no new notifications are sent (to prevent spam)

### Email Processing:
- Emails are sent in batches of 100 users at a time
- Each batch is processed separately to prevent memory issues
- If an individual email fails, it's logged but doesn't stop other emails
- All errors are logged for debugging purposes

## Setup Instructions

### 1. Run Migrations (if not already done)
```bash
php artisan migrate
```

This ensures the `jobs` table exists for queue processing.

### 2. Configure Email Settings
Make sure your `.env` file has proper email configuration. Current settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=johnraymondbarrogo08@gmail.com
MAIL_PASSWORD=dgqxipzyllfzcqdc
MAIL_FROM_ADDRESS=johnraymondbarrogo08@gmail.com
MAIL_FROM_NAME="SMS"
```

### 3. Configure Queue Connection
Your `.env` already has:
```env
QUEUE_CONNECTION=database
```

This means jobs will be stored in the database and processed by queue workers.

### 4. Start the Queue Worker

**Option A: Run in Terminal (Development)**
```bash
php artisan queue:work --tries=3
```

**Option B: Run with Specific Queue**
```bash
php artisan queue:work --queue=default --tries=3
```

**Option C: Run as Background Process (Production - Windows)**
```bash
start /B php artisan queue:work --tries=3
```

**Important Notes:**
- The queue worker must be running to process email notifications
- Without a running queue worker, emails will be queued but not sent
- Use `--tries=3` to retry failed jobs up to 3 times
- For production, consider using Supervisor or Task Scheduler to keep queue worker running

### 5. Process Jobs Immediately (Alternative - Not Recommended)
If you don't want to use queues, you can process jobs synchronously by changing:
```env
QUEUE_CONNECTION=sync
```

**Warning:** This will slow down announcement creation as it waits for all emails to send.

## Testing the System

### 1. Create a Test Announcement
1. Log in as admin
2. Go to Announcements → Create New
3. Fill in the form with:
   - Title: "Test Announcement"
   - Content: "This is a test to verify email notifications"
   - Mark as Active
4. Click "Create Announcement"

### 2. Check Queue Jobs
```bash
# Check pending jobs
php artisan queue:monitor

# Or check the jobs table directly
SELECT * FROM jobs;
```

### 3. Process the Queue
```bash
php artisan queue:work
```

### 4. Check Email Logs
Check `storage/logs/laravel.log` for:
- Success messages: "Announcement notifications sent successfully"
- Error messages: "Failed to send announcement notifications"

### 5. Verify Email Delivery
- Check recipient inboxes (students, teachers, guardians)
- Verify email content displays correctly
- Test links to respective portals

## Monitoring and Maintenance

### View Failed Jobs
```bash
php artisan queue:failed
```

### Retry Failed Jobs
```bash
# Retry all failed jobs
php artisan queue:retry all

# Retry specific job
php artisan queue:retry [job_id]
```

### Clear Failed Jobs
```bash
php artisan queue:flush
```

### View Queue Statistics
```bash
# Monitor queue in real-time
php artisan queue:monitor
```

## Email Template Features

The email template includes:
- 📢 Eye-catching header with icon
- 📝 Full announcement title and content
- 🖼️ Image display (if announcement has an image)
- 📅 Published and expiration dates
- 🔵 Call-to-action button linking to user's portal
- 📱 Responsive design for mobile devices
- ✉️ Professional footer with school branding

## User Experience

### Students Receive:
- Personalized greeting: "Dear Student [Name]"
- Link to Student Dashboard
- Email address from student record

### Teachers Receive:
- Respectful greeting: "Dear Sir/Ma'am [Name]"
- Link to Teacher Dashboard
- Email address from teacher record

### Guardians Receive:
- Formal greeting: "Dear Parent/Guardian [Name]"
- Link to Guardian Dashboard
- Email address from guardian record

## Performance Considerations

### Batch Processing
- Users are processed in batches of 100
- Prevents memory overflow with large user bases
- Uses Laravel's `chunk()` method for efficiency

### Error Handling
- Individual email failures don't stop the process
- All errors are logged with context
- Failed jobs can be retried automatically

### Queue Benefits
- Non-blocking: Admin doesn't wait for emails to send
- Scalable: Can handle thousands of recipients
- Resilient: Failed jobs can be retried
- Monitored: Easy to track job status

## Troubleshooting

### Emails Not Sending
1. **Check Queue Worker**
   ```bash
   # Is it running?
   ps aux | grep "queue:work"
   ```

2. **Check Jobs Table**
   ```sql
   SELECT * FROM jobs;
   ```

3. **Check Failed Jobs**
   ```bash
   php artisan queue:failed
   ```

4. **Check Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Emails Going to Spam
- Ensure `MAIL_FROM_ADDRESS` matches authenticated sender
- Use Google App Password (already configured)
- Consider using a dedicated email service (SendGrid, Mailgun)

### Slow Performance
- Increase batch size in job (currently 100)
- Use multiple queue workers
- Consider using Redis instead of database for queue

### Users Not Receiving Emails
- Verify user has valid email in database
- Check user status is "active"
- Verify email is not empty string

## Future Enhancements

Possible improvements:
1. **Email Preferences**: Allow users to opt-out of announcement emails
2. **Priority Levels**: Mark announcements as urgent and send immediately
3. **Scheduling**: Schedule announcement publication for future date/time
4. **Read Receipts**: Track who has opened the email
5. **Category Filtering**: Send only relevant announcements to specific groups
6. **SMS Notifications**: Add SMS notifications for urgent announcements
7. **Push Notifications**: Integrate browser/mobile push notifications
8. **Email Templates**: Multiple template designs for different announcement types

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check queue status: `php artisan queue:monitor`
3. Review failed jobs: `php artisan queue:failed`
4. Test email configuration: `php artisan tinker` then `Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });`

## Summary

The announcement email notification system is now fully functional and will:
- ✅ Automatically send emails when announcements are created
- ✅ Send emails when inactive announcements are activated
- ✅ Process emails in background without slowing down the admin interface
- ✅ Handle errors gracefully and log all issues
- ✅ Send beautiful, professional emails to all users
- ✅ Provide direct links to user portals

**Remember to keep the queue worker running for emails to be sent!**
