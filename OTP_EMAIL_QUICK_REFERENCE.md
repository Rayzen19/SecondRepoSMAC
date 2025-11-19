# OTP Email Quick Reference

## 🚀 Quick Start

**For Users:**
1. Click "Forgot Password?" → Enter email → Get OTP in email → Reset password

**For Developers:**
- All 4 account types (Admin/Teacher/Student/Guardian) use same system
- OTPs expire in 10 minutes
- One-time use only
- Sent via Gmail SMTP

---

## 📁 Key Files

| File | Purpose |
|------|---------|
| `app/Mail/PasswordResetOtp.php` | Mailable class |
| `resources/views/emails/password_reset_otp.blade.php` | Email template |
| `app/Http/Controllers/Admin/AuthController.php` | OTP logic |
| `database/migrations/*_create_password_otps_table.php` | Database schema |

---

## 🔑 Routes

All account types use `Admin\AuthController` with different prefixes:

```
POST /admin/forgot-password    → admin.auth.forgotSend
POST /teacher/forgot-password  → teacher.auth.forgotSend
POST /student/forgot-password  → student.auth.forgotSend
POST /guardian/forgot-password → guardian.auth.forgotSend
```

---

## 📧 Email Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=toledomarcandrei1@gmail.com
MAIL_PASSWORD=davksdlvkkdiuegl
MAIL_FROM_ADDRESS=toledomarcandrei1@gmail.com
```

---

## 🧪 Testing Commands

### View Recent OTPs
```bash
php artisan tinker
DB::table('password_otps')->latest()->get(['email', 'expires_at', 'used_at']);
```

### Watch Logs
```bash
tail -f storage/logs/laravel.log | grep "Password reset OTP"
```

### Test Email
```bash
php artisan tinker
use App\Mail\PasswordResetOtp;
Mail::to('test@example.com')->send(new PasswordResetOtp('Test', '123456', 'admin'));
```

### Clear Expired OTPs
```bash
php artisan tinker
DB::table('password_otps')->where('expires_at', '<', now())->delete();
```

---

## 🔒 Security Features

✅ 10-minute expiration  
✅ One-time use  
✅ Bcrypt hashing  
✅ Email validation  
✅ 8+ character passwords  

---

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| Email not arriving | Check spam folder, verify SMTP config |
| Invalid OTP | Check if expired (>10 min) or already used |
| Email fails | Check logs: `tail storage/logs/laravel.log` |
| Wrong user type | System auto-detects from route name |

---

## 📊 Database Schema

```sql
password_otps:
  - id
  - email
  - code_hash  (bcrypt)
  - expires_at (timestamp + 10 min)
  - used_at    (null until used)
  - created_at
  - updated_at
```

---

## ✉️ Email Contents

📧 **Subject:** Password Reset OTP - SMAC

**Contains:**
- Personalized greeting with user's name
- Large 6-digit OTP code (36px)
- Expiration warning (10 minutes)
- Step-by-step instructions
- Security notice
- SMAC branding (purple gradient)

---

## 🔄 Flow Diagram

```
User clicks "Forgot Password?"
         ↓
User enters email
         ↓
System generates 6-digit OTP
         ↓
OTP saved to database (hashed)
         ↓
Email sent with OTP
         ↓
User receives email
         ↓
User enters OTP + new password
         ↓
System verifies OTP
         ↓
Password updated
         ↓
OTP marked as used
         ↓
User redirected to login
```

---

## 🎯 Test Checklist

For each account type:

- [ ] Request OTP from forgot password page
- [ ] Receive email with 6-digit code
- [ ] Reset password with valid OTP
- [ ] Login with new password
- [ ] Try expired OTP (should fail)
- [ ] Try reusing OTP (should fail)
- [ ] Check logs for OTP entry

---

## 💡 Quick Tips

**For Users:**
- OTP expires in 10 minutes - use it quickly!
- Check spam folder if email doesn't arrive
- Don't share your OTP with anyone

**For Developers:**
- OTPs are logged to Laravel log for debugging
- In dev mode, OTP shown in success message if email fails
- All 4 account types use same controller
- User type auto-detected from route name

---

## 📞 Support

**Email Issues:**
- Check Gmail App Password is correct
- Verify 2-step verification enabled
- Review `storage/logs/laravel.log`

**Database Issues:**
```bash
php artisan migrate:status  # Check migrations
php artisan tinker
DB::connection()->getPdo();  # Test connection
```

---

**Documentation:** README_OTP_EMAIL_SYSTEM.md  
**Last Updated:** October 21, 2025
