# 🔄 RESTART QUEUE WORKER TO FIX IMAGES

## ⚠️ IMPORTANT: You Must Restart the Queue Worker!

The email template has been fixed, but your **queue worker is still running with the old template cached in memory**.

---

## ✅ Solution: Restart the Queue Worker

### Step 1: Stop the Current Queue Worker
1. Go to the terminal window running `php artisan queue:work`
2. Press **Ctrl + C** to stop it
3. You should see the worker stop

### Step 2: Restart the Queue Worker
Run one of these commands:

**Option A: Using the batch file**
```bash
start-queue-worker.bat
```

**Option B: Manual command**
```bash
php artisan queue:work --tries=3
```

---

## 🧪 Test Again

After restarting the queue worker:

1. **Send a test email:**
   ```bash
   php artisan announcement:test-email
   ```

2. **Or create a new announcement:**
   - Go to Admin Panel → Announcements → Create
   - Fill in details and upload image
   - Mark as Active
   - Submit

3. **Check your email:**
   - The image should now display correctly! 📸✅

---

## 🔍 Why This Happens

- Laravel queue workers load application code into memory
- When you update template files, the worker still uses the old version
- Restarting the worker loads the new template
- This is normal Laravel behavior for long-running processes

---

## 💡 The Fix Applied

The email template now generates **absolute URLs** like:
```
http://127.0.0.1:8000/storage/announcements/4HCsn4BFtih7qdz56ClCntGaDow065jaxzgst1CG.png
```

Instead of relative URLs like:
```
storage/announcements/4HCsn4BFtih7qdz56ClCntGaDow065jaxzgst1CG.png
```

Email clients need the full URL to fetch images!

---

## ✅ After Restart

Once you restart the queue worker, all future announcement emails will have working images! 🎉

**Your current announcement "Buwan ng Wika" has an image at:**
`announcements/4HCsn4BFtih7qdz56ClCntGaDow065jaxzgst1CG.png` ✓

**Will generate URL:**
`http://127.0.0.1:8000/storage/announcements/4HCsn4BFtih7qdz56ClCntGaDow065jaxzgst1CG.png` ✓

---

## 📝 Remember for Future Updates

**Whenever you update:**
- Email templates
- Job classes
- Controllers
- Any PHP code used by queue workers

**Always restart the queue worker** to load the changes!

---

**Go ahead and restart your queue worker now!** 🚀
