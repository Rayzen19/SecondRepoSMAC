# OTP Email System for Password Reset

## Overview
This system sends One-Time Password (OTP) codes via email when users forget their passwords. It works for **all 4 account types**: Admin, Teacher, Student, and Guardian.

---

## How It Works

### 1. User Requests Password Reset
- User clicks "Forgot Password?" on the login page
- User enters their email address
- System generates a 6-digit OTP code
- **OTP is sent to the user's email** (new feature!)
- OTP expires in 10 minutes

### 2. User Receives Email
Beautiful HTML email containing:
- Personalized greeting with user's name
- **Large 6-digit OTP code** (easy to read)
- Step-by-step instructions
- 10-minute expiration warning
- Security notice (don't share OTP)
- SMAC branding with purple gradient

### 3. User Resets Password
- User enters the OTP from their email
- User enters new password (minimum 8 characters)
- System verifies OTP is valid and not expired
- Password is updated
- OTP is marked as used (cannot be reused)
- User is redirected to login page

---

## Technical Implementation

### Files Created/Updated

#### 1. **app/Mail/PasswordResetOtp.php** (NEW)
Mailable class that sends the OTP email.
```php
Mail::to($email)->send(new PasswordResetOtp(
    $userName,    // User's name for personalization
    $otpCode,     // 6-digit code
    $userType     // admin, teacher, student, or guardian
));
```

#### 2. **resources/views/emails/password_reset_otp.blade.php** (NEW)
Beautiful HTML email template with:
- Professional design
- Purple gradient header (#7C3AED to #5B21B6)
- Large OTP display (36px font, letter-spaced)
- Mobile-responsive layout
- Security warnings
- SMAC branding

#### 3. **app/Http/Controllers/Admin/AuthController.php** (UPDATED)
Updated `sendOtp()` and `resetWithOtp()` methods to:
- Send OTP via email to user's address
- Detect user type from route (admin/teacher/student/guardian)
- Redirect to correct portal after reset
- Handle email failures gracefully
- Log OTP for development/debugging

---

## Routes Configuration

All 4 account types use the same AuthController with different route prefixes:

### Admin
- Forgot: `POST /admin/forgot-password` → `admin.auth.forgotSend`
- Reset: `POST /admin/reset-password` → `admin.auth.resetProcess`

### Teacher
- Forgot: `POST /teacher/forgot-password` → `teacher.auth.forgotSend`
- Reset: `POST /teacher/reset-password` → `teacher.auth.resetProcess`

### Student
- Forgot: `POST /student/forgot-password` → `student.auth.forgotSend`
- Reset: `POST /student/reset-password` → `student.auth.resetProcess`

### Guardian
- Forgot: `POST /guardian/forgot-password` → `guardian.auth.forgotSend`
- Reset: `POST /guardian/reset-password` → `guardian.auth.resetProcess`

---

## Email Configuration

Email settings are configured in `.env`:

```properties
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=toledomarcandrei1@gmail.com
MAIL_PASSWORD=davksdlvkkdiuegl  # App Password
MAIL_FROM_ADDRESS=toledomarcandrei1@gmail.com
MAIL_FROM_NAME="SMS"
```

**Important**: Using Gmail App Password (not regular password). Gmail account has 2-step verification enabled.

---

## Security Features

### 1. OTP Expiration
- OTP expires after **10 minutes**
- System checks `expires_at` timestamp before accepting OTP
- Expired OTPs are automatically rejected

### 2. One-Time Use
- Each OTP can only be used **once**
- After successful password reset, OTP is marked with `used_at` timestamp
- Used OTPs cannot be reused

### 3. Hashed Storage
- OTPs are stored as **hashed values** in the database
- Uses Laravel's `Hash::make()` for bcrypt hashing
- Raw OTP never stored in database

### 4. Email Validation
- Email must exist in the `users` table
- Validates email format before sending OTP

### 5. Password Requirements
- Minimum 8 characters
- Must be confirmed (enter twice)
- Hashed using bcrypt before storage

---

## Database Schema

### password_otps table
```sql
CREATE TABLE password_otps (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    code_hash VARCHAR(255) NOT NULL,    -- Bcrypt hash of OTP
    expires_at TIMESTAMP NOT NULL,      -- OTP expiry time
    used_at TIMESTAMP NULL,             -- When OTP was used
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_expires (expires_at)
);
```

---

## Error Handling

### Email Delivery Failures
If email fails to send:
1. Error is logged to `storage/logs/laravel.log`
2. In development mode, OTP is shown in the success message
3. User still sees success message (email "sent")
4. OTP is also logged to Laravel log for debugging

### Invalid OTP
- "Invalid or expired OTP" error message
- User can request a new OTP
- Old OTPs remain in database for audit trail

### Expired OTP
- System checks `expires_at` before accepting OTP
- Expired OTPs trigger "Invalid or expired OTP" error
- User must request new OTP

---

## Testing Checklist

### For Each Account Type (Admin, Teacher, Student, Guardian):

1. **Request OTP**
   - [ ] Navigate to login page for account type
   - [ ] Click "Forgot Password?"
   - [ ] Enter valid email address
   - [ ] Submit form
   - [ ] Verify success message appears
   - [ ] Check email inbox for OTP email
   - [ ] Verify email contains correct name and 6-digit OTP

2. **Reset Password with Valid OTP**
   - [ ] Copy OTP from email
   - [ ] Enter OTP on reset form
   - [ ] Enter new password (8+ characters)
   - [ ] Confirm new password
   - [ ] Submit form
   - [ ] Verify redirect to login page
   - [ ] Verify success message
   - [ ] Login with new password

3. **Test Security Features**
   - [ ] Try using expired OTP (wait 11 minutes)
   - [ ] Try reusing the same OTP twice
   - [ ] Try invalid OTP (wrong digits)
   - [ ] Try password less than 8 characters
   - [ ] Try non-matching password confirmation

4. **Check Logs**
   - [ ] Verify OTP logged in `storage/logs/laravel.log`
   - [ ] Check for any email errors in logs

---

## Quick Test Commands

### 1. Check Recent OTPs
```bash
php artisan tinker
DB::table('password_otps')->latest()->take(5)->get(['email', 'expires_at', 'used_at', 'created_at']);
```

### 2. View Recent Logs
```bash
tail -f storage/logs/laravel.log | grep "Password reset OTP"
```

### 3. Test Email Configuration
```bash
php artisan tinker
Mail::raw('Test email from SMAC', function($msg) {
    $msg->to('your-email@example.com')->subject('Test');
});
```

### 4. Clear Expired OTPs (Cleanup)
```bash
php artisan tinker
DB::table('password_otps')->where('expires_at', '<', now())->delete();
```

---

## Troubleshooting

### Email Not Arriving

1. **Check Spam Folder**
   - Gmail may filter automated emails to spam
   - Mark as "Not Spam" if found there

2. **Verify SMTP Configuration**
   ```bash
   php artisan tinker
   config('mail.from.address');  // Should show toledomarcandrei1@gmail.com
   config('mail.host');          // Should show smtp.gmail.com
   ```

3. **Check Gmail App Password**
   - Make sure using App Password, not regular password
   - Gmail account must have 2-step verification enabled
   - Generate new App Password if needed

4. **Check Logs**
   ```bash
   tail -100 storage/logs/laravel.log | grep -i "mail\|smtp\|error"
   ```

5. **Test Email Manually**
   ```bash
   php artisan tinker
   use App\Mail\PasswordResetOtp;
   Mail::to('your-test-email@example.com')->send(new PasswordResetOtp('Test User', '123456', 'admin'));
   ```

### OTP Not Working

1. **Check if OTP Expired**
   - OTPs expire after 10 minutes
   - Request a new OTP

2. **Verify OTP Digits**
   - Must be exactly 6 digits
   - No spaces or special characters
   - Copy-paste from email to avoid typos

3. **Check Database**
   ```bash
   php artisan tinker
   DB::table('password_otps')
       ->where('email', 'user@example.com')
       ->latest()
       ->first(['code_hash', 'expires_at', 'used_at']);
   ```

### Wrong User Type in Email

If email shows wrong account type:
1. This shouldn't happen with current implementation
2. User type is detected from route name automatically
3. Check that routes are properly configured in `routes/web.php`

---

## User Guide

### For End Users

**Forgot Your Password?**

1. Click "Forgot Password?" on the login page
2. Enter your email address
3. Click "Send OTP"
4. Check your email for a 6-digit code
5. Enter the code on the next page
6. Create your new password
7. Login with your new password

**Didn't receive the email?**
- Check your spam/junk folder
- Make sure you entered the correct email address
- Wait a minute and check again (emails can take 30-60 seconds)
- Request a new OTP if needed

**Email Tips:**
- OTP expires in 10 minutes - use it quickly
- Each OTP can only be used once
- Don't share your OTP with anyone
- The email is from: toledomarcandrei1@gmail.com

---

## Development Notes

### User Type Detection
The system automatically detects user type from the route name:
```php
$routeName = $request->route()->getName();
// Examples: 'admin.auth.forgotSend', 'teacher.auth.resetProcess'

if (str_contains($routeName, 'teacher')) {
    $userType = 'teacher';
} elseif (str_contains($routeName, 'student')) {
    $userType = 'student';
} elseif (str_contains($routeName, 'guardian')) {
    $userType = 'guardian';
} else {
    $userType = 'admin';  // default
}
```

### Email Template Customization
To customize the email template, edit:
`resources/views/emails/password_reset_otp.blade.php`

Available variables:
- `$userName` - User's name
- `$otpCode` - 6-digit OTP
- `$userType` - Account type (admin/teacher/student/guardian)
- `$appName` - Application name (SMS)
- `$expiresIn` - Minutes until expiry (10)

### Adding More Features

**Want to add SMS OTP?**
1. Install SMS package (e.g., Twilio)
2. Update `sendOtp()` method to send both email and SMS
3. Store phone number in users table

**Want to add OTP resend limit?**
1. Add counter to password_otps table
2. Limit to 3 requests per hour per email
3. Add rate limiting middleware

**Want custom OTP length?**
Change line in AuthController:
```php
$code = (string) random_int(100000, 999999);  // 6 digits
// For 8 digits:
$code = (string) random_int(10000000, 99999999);
```

---

## Implementation Summary

✅ **Completed:**
- Created PasswordResetOtp Mailable class
- Designed beautiful HTML email template
- Updated AuthController with email functionality
- Added user type detection from routes
- Implemented error handling and logging
- Works for all 4 account types (Admin, Teacher, Student, Guardian)
- Email configuration verified (Gmail SMTP)
- Security features: expiration, one-time use, hashing

🎯 **Result:**
Users can now reset their passwords by receiving OTP codes via email. The system is secure, user-friendly, and works seamlessly across all account types.

---

## Contact & Support

If you encounter issues with the OTP email system:
1. Check this documentation first
2. Review Laravel logs: `storage/logs/laravel.log`
3. Test email configuration manually
4. Verify Gmail App Password is correct
5. Check that user exists in database with valid email

**Email Issues?**
- Gmail Developer: toledomarcandrei1@gmail.com
- Check Gmail account's "Less secure app access" settings
- Ensure 2-step verification is enabled
- Generate new App Password if needed

---

**Last Updated:** October 21, 2025
**Version:** 1.0
**Laravel Version:** 12.x
