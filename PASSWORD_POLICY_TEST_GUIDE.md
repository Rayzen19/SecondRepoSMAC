# Password Policy - Quick Test Guide

## ✅ What Was Changed

The password change feature now enforces a **strong password policy** for teachers, students, and guardians.

### New Requirements:
- 🔒 **Minimum 12 characters** (14+ recommended)
- 🔡 **Uppercase + lowercase letters**
- 🔢 **Numbers**
- 🔣 **Symbols** (@$!%*#?&)
- 🚫 **Not a dictionary word**
- 🛡️ **Not in data breach databases**

---

## 🧪 Quick Test Steps

### Test 1: Password Too Short
```
Current Password: [your-current-password]
New Password: Short1!
Confirm: Short1!
```
**Expected**: ❌ Error: "Password must be at least 12 characters long."

---

### Test 2: No Uppercase Letters
```
Current Password: [your-current-password]
New Password: mypassword123!
Confirm: mypassword123!
```
**Expected**: ❌ Error: "Password must contain both uppercase and lowercase letters."

---

### Test 3: No Lowercase Letters
```
Current Password: [your-current-password]
New Password: MYPASSWORD123!
Confirm: MYPASSWORD123!
```
**Expected**: ❌ Error: "Password must contain both uppercase and lowercase letters."

---

### Test 4: No Numbers
```
Current Password: [your-current-password]
New Password: MyPassword!!!
Confirm: MyPassword!!!
```
**Expected**: ❌ Error: "Password must contain at least one number."

---

### Test 5: No Symbols
```
Current Password: [your-current-password]
New Password: MyPassword123
Confirm: MyPassword123
```
**Expected**: ❌ Error: "Password must contain at least one symbol."

---

### Test 6: Common/Breached Password
```
Current Password: [your-current-password]
New Password: Password123!
Confirm: Password123!
```
**Expected**: ❌ Error: "This password has appeared in a data breach and should not be used."

---

### Test 7: Wrong Current Password
```
Current Password: wrong-password
New Password: MyStr0ng!Pass2024#
Confirm: MyStr0ng!Pass2024#
```
**Expected**: ❌ Error: "The current password is incorrect."

---

### Test 8: Password Mismatch
```
Current Password: [your-current-password]
New Password: MyStr0ng!Pass2024#
Confirm: MyStr0ng!Pass2024#Different
```
**Expected**: ❌ Error: "The password confirmation does not match."

---

### Test 9: Strong Password (SUCCESS) ✅
```
Current Password: [your-current-password]
New Password: MyStr0ng!Pass2024#
Confirm: MyStr0ng!Pass2024#
```
**Expected**: ✅ Success: "Password changed successfully!"

---

## 📍 Where to Test

### Teacher
1. Login at: `http://127.0.0.1:8000/admin/login`
2. Navigate to: **My Profile**
3. Click: **Change Password**
4. Page: `/teacher/profile/password/edit`

### Student
1. Login at: `http://127.0.0.1:8000/student/login`
2. Navigate to: **My Profile**
3. Click: **Change Password**
4. Page: `/student/profile/password/edit`

### Guardian
1. Login at: `http://127.0.0.1:8000/guardian/login`
2. Navigate to: **Profile**
3. Click: **Change Password** button
4. Modal opens on the same page

---

## 💡 Good Password Examples

✅ **Strong Passwords:**
- `MySchool!2024@Pass`
- `Teach3r$SecureKey#`
- `Student!2024#Str0ng`
- `Guard1an@P@ssw0rd!`
- `Secure#Learn2024$`
- `MySecure!Key#2024`

❌ **Weak Passwords:**
- `password123` (too simple, no symbols, no uppercase)
- `Teacher123` (no symbols)
- `TEACHER123!` (no lowercase)
- `TeacherPass!` (no numbers)
- `Pass123!` (too short)
- `Password123!` (breached password)

---

## 📋 Password Policy Display

When users visit the change password page, they'll see:

```
🛈 Password Policy:
  • At least 12 characters long (but 14 or more is better)
  • A combination of uppercase letters, lowercase letters, numbers, and symbols
  • Not a word that can be found in a dictionary or the name of a person, 
    character, product, or organization
  • Make sure your new password is different from your current password
```

---

## 🔧 Technical Details

### Frontend Validation
- HTML5 `minlength="12"` attribute
- Real-time browser validation

### Backend Validation (Laravel)
- **Teacher & Student**: Uses `Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised()`
- **Guardian**: Uses regex patterns + `min:12` validation

### Security Features
- Passwords are hashed with bcrypt
- Checked against "Have I Been Pwned" database
- Current password verification required
- No password history stored (only current password)

---

## 🚀 Next Steps After Testing

If all tests pass:
1. ✅ Password policy is working correctly
2. ✅ Users can change passwords with strong requirements
3. ✅ System prevents weak passwords
4. ✅ Enhanced security is in place

If tests fail:
1. Check PHP version (Laravel 11 requires PHP 8.2+)
2. Clear cache: `php artisan config:clear`
3. Check error logs in `storage/logs/laravel.log`

---

**Status**: Ready for Testing
**Date**: November 5, 2025
**Security Level**: Enhanced (12-char minimum with complexity)
