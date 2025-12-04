# Quick Start Guide: Announcement Email Notifications

## 🚀 Getting Started in 3 Steps

### Step 1: Start the Queue Worker
The queue worker processes email notifications in the background.

**Windows:**
```bash
# Double-click this file or run in terminal:
start-queue-worker.bat

# Or manually:
php artisan queue:work --tries=3
```

**Linux/Mac:**
```bash
# Run this script:
./start-queue-worker.sh

# Or manually:
php artisan queue:work --tries=3
```

**Keep this terminal window open!** Emails won't be sent without the queue worker running.

---

### Step 2: Create or Activate an Announcement
1. Log in to the **Admin Panel**
2. Navigate to **Announcements** → **Create New**
3. Fill in the form:
   - **Title**: Your announcement title
   - **Content**: Your announcement message
   - **Image** (optional): Upload or provide URL
   - **Active**: ✅ Check this box
4. Click **"Create Announcement"**

✨ **That's it!** Email notifications are automatically queued.

---

### Step 3: Monitor the Process

**Check how many users will receive emails:**
```bash
php artisan email:stats
```

**Test with a specific announcement:**
```bash
php artisan announcement:test-email [announcement_id]
```

**Monitor queue progress:**
```bash
php artisan queue:monitor
```

**View logs:**
```bash
# Check for success/error messages
tail -f storage/logs/laravel.log

# Windows:
Get-Content storage\logs\laravel.log -Tail 50 -Wait
```

---

## 📧 Who Receives Emails?

When you create or activate an announcement, emails are sent to:

- ✅ **All active students** with valid email addresses
- ✅ **All active teachers** with valid email addresses  
- ✅ **All active guardians** with valid email addresses

**Check recipient counts:**
```bash
php artisan email:stats
```

---

## ✅ Verify It's Working

### 1. Check Success Message
After creating an announcement, you should see:
> "Announcement created successfully. Email notifications are being sent to all users."

### 2. Check Queue Jobs
```bash
# Windows
php artisan tinker
DB::table('jobs')->count();
exit

# Should show number of pending jobs
```

### 3. Check Logs
```bash
# Look for this message:
"Announcement notifications sent successfully"
```

### 4. Check Email Inboxes
- Students, teachers, and guardians should receive professional emails
- Emails include full announcement content and images
- Each email has a link to the respective user portal

---

## 🐛 Troubleshooting

### Emails Not Sending?

**1. Is the queue worker running?**
```bash
# Check if queue worker is active
# You should see a terminal window with "Processing..." messages
```

**2. Are there jobs in the queue?**
```bash
php artisan queue:monitor
```

**3. Check for failed jobs:**
```bash
php artisan queue:failed

# Retry failed jobs:
php artisan queue:retry all
```

**4. Check email configuration:**
```bash
# Test email sending
php artisan tinker
Mail::raw('Test email', function($msg) {
    $msg->to('your@email.com')->subject('Test');
});
exit
```

### Users Not Receiving Emails?

1. **Verify user has email address:**
   ```bash
   php artisan email:stats
   ```

2. **Check user status is "active"**

3. **Check spam/junk folders**

### Queue Worker Keeps Stopping?

**For development:**
- Run `start-queue-worker.bat` in a separate terminal
- Keep the terminal open

**For production (Windows):**
Use Task Scheduler to run queue worker on startup:
1. Open Task Scheduler
2. Create Basic Task
3. Trigger: "When computer starts"
4. Action: Start Program
5. Program: `php`
6. Arguments: `artisan queue:work --tries=3`
7. Start in: `C:\xampp\htdocs\NEWSMAC`

---

## 📊 Commands Reference

```bash
# Check email statistics
php artisan email:stats

# Test announcement emails
php artisan announcement:test-email

# Start queue worker
php artisan queue:work --tries=3

# Monitor queue
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear all failed jobs
php artisan queue:flush
```

---

## 🎯 Best Practices

1. **Always keep queue worker running** when expecting announcement notifications
2. **Test with a small announcement first** before sending to all users
3. **Check email stats** before creating announcements to know how many will receive it
4. **Monitor logs** to catch any email delivery issues early
5. **Use meaningful announcement titles** - they appear in email subject lines

---

## 💡 Tips

- **Inactive announcements don't trigger emails** - only when created as active or when activated
- **Emails are sent in batches of 100** - prevents memory issues with large user bases
- **Failed individual emails don't stop the process** - others will still be sent
- **Queue worker automatically retries** failed jobs up to 3 times
- **All activity is logged** in `storage/logs/laravel.log`

---

## 📞 Support

If you encounter issues:
1. Check `storage/logs/laravel.log` for errors
2. Run `php artisan email:stats` to verify user data
3. Test with `php artisan announcement:test-email`
4. Ensure queue worker is running
5. Verify email configuration in `.env` file

---

## ✨ Features

- 📧 Professional, responsive email templates
- 🚀 Background processing (non-blocking)
- 📊 Batch processing for performance
- 🔄 Automatic retry on failure
- 📝 Comprehensive error logging
- 👥 Supports students, teachers, and guardians
- 🖼️ Includes announcement images
- 🔗 Direct links to user portals
- ⚡ Queue-based for scalability

---

**That's all you need to know! Start the queue worker and create announcements. Emails will be sent automatically! 🎉**
