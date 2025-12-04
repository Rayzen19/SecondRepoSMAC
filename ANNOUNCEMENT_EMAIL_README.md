# 📧 Announcement Email Notifications - README

## 🎯 Overview

When you upload (create or activate) an announcement, the system will **automatically send email notifications** to:
- ✅ All active **students** (58 users)
- ✅ All active **teachers** (2 users)
- ✅ All active **guardians** (10 users)
- **Total: 70 email notifications per announcement**

---

## ⚡ Quick Start

### 1️⃣ Start Queue Worker (Required!)

**Windows:**
```bash
# Option A: Double-click this file
start-queue-worker.bat

# Option B: Run in terminal
php artisan queue:work --tries=3
```

**Linux/Mac:**
```bash
chmod +x start-queue-worker.sh
./start-queue-worker.sh
```

> **⚠️ Important:** Keep this terminal window open! Emails won't send without the queue worker running.

---

### 2️⃣ Create an Announcement

1. Go to: **Admin Panel** → **Announcements** → **Create New**
2. Fill in:
   - **Title**: Your announcement title
   - **Content**: Your message
   - **Image** (optional): Upload a photo or provide URL
   - **✅ Active**: Check this box to send notifications
3. Click **"Create Announcement"**

**✨ Done!** Emails are automatically sent to all 70 users.

---

## 📊 Check Email Statistics

See how many users will receive notifications:

```bash
php artisan email:stats
```

**Current Stats:**
- Students: 58 with email (100% coverage)
- Teachers: 2 with email (100% coverage)
- Guardians: 10 with email (100% coverage)
- **Total: 70 users will receive emails**

---

## 🧪 Test the System

### Test with Latest Announcement:
```bash
php artisan announcement:test-email
```

### Test with Specific Announcement:
```bash
php artisan announcement:test-email 5
```
(Replace `5` with your announcement ID)

---

## 📧 What Users Receive

Users will receive a **professional HTML email** with:

- 📢 Announcement title (in subject line)
- 👤 Personalized greeting (based on user type)
- 📝 Full announcement content
- 🖼️ Image (if uploaded)
- 📅 Publication & expiration dates
- 🔵 Button linking to their portal
- 📱 Mobile-friendly design

**Example Subject:** "New Announcement: School Event This Friday"

---

## ✅ Verify It's Working

### 1. Check Success Message
After creating an announcement, you should see:
> ✓ "Announcement created successfully. Email notifications are being sent to all users."

### 2. Check Queue Worker
The terminal running the queue worker should show:
```
[2024-12-04 10:30:15][1] Processing: App\Jobs\SendAnnouncementNotifications
[2024-12-04 10:30:20][1] Processed:  App\Jobs\SendAnnouncementNotifications
```

### 3. Check Logs
```bash
# View recent logs (Windows PowerShell)
Get-Content storage\logs\laravel.log -Tail 20
```

Look for:
```
✓ "Announcement notifications sent successfully"
```

### 4. Check Email Inboxes
- Students, teachers, and guardians receive emails
- Check spam/junk folders if not in inbox

---

## 🐛 Troubleshooting

### Problem: Emails not sending

**Solution 1: Start the queue worker**
```bash
php artisan queue:work --tries=3
```

**Solution 2: Check queue status**
```bash
php artisan queue:monitor
```

**Solution 3: Check for failed jobs**
```bash
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

**Solution 4: Check logs**
```bash
Get-Content storage\logs\laravel.log -Tail 50
```

---

### Problem: Users not receiving emails

**Check 1: User has email address**
```bash
php artisan email:stats
```

**Check 2: User status is "active"**

**Check 3: Email settings in .env**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_FROM_ADDRESS=johnraymondbarrogo08@gmail.com
```

**Check 4: Check spam/junk folders**

---

### Problem: Queue worker keeps stopping

**For Development:**
- Keep terminal window open
- Run `start-queue-worker.bat`

**For Production:**
Set up Windows Task Scheduler:
1. Open Task Scheduler
2. Create Basic Task → "Queue Worker"
3. Trigger: "When computer starts"
4. Action: `php artisan queue:work --tries=3`
5. Start in: `C:\xampp\htdocs\NEWSMAC`

---

## 🎯 Important Notes

### ✅ DO:
- Keep queue worker running
- Check email stats before sending
- Monitor logs regularly
- Test with a small announcement first

### ❌ DON'T:
- Create announcements without queue worker running
- Close the queue worker terminal
- Send test announcements to all users repeatedly
- Forget to mark announcements as "Active" if you want emails sent

---

## 📚 Documentation

**Quick Start (This file):**
- `README.md` ← You are here

**Detailed Guide:**
- `ANNOUNCEMENT_EMAIL_NOTIFICATION.md` - Complete technical documentation

**Summary:**
- `ANNOUNCEMENT_EMAIL_SUMMARY.md` - Feature overview and flow diagrams

**Pick the one that fits your needs!**

---

## 🔧 Useful Commands

```bash
# Check email statistics
php artisan email:stats

# Test announcement email
php artisan announcement:test-email

# Start queue worker
php artisan queue:work --tries=3

# Monitor queue
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry all failed jobs
php artisan queue:retry all

# Clear all jobs
php artisan queue:flush

# View logs
Get-Content storage\logs\laravel.log -Tail 50
```

---

## 🎊 That's It!

1. **Start queue worker:** `start-queue-worker.bat`
2. **Create announcement:** Admin Panel → Announcements → Create
3. **Mark as active:** ✅ Check the "Active" box
4. **Click create:** Emails sent automatically to all 70 users!

---

## 📞 Need Help?

1. Check logs: `storage/logs/laravel.log`
2. Run email stats: `php artisan email:stats`
3. Test with: `php artisan announcement:test-email`
4. Verify queue worker is running
5. Review detailed documentation files

---

**🚀 Ready to send announcements? Start the queue worker and you're good to go!**
