# User Profiles - Quick Reference Card

## 🚀 Quick Access URLs

| User Type | Profile URL | Login URL |
|-----------|-------------|-----------|
| 👨‍💼 Admin | `/admin/profile` | `/admin/login` |
| 👨‍🏫 Teacher | `/teacher/profile` | `/teacher/login` |
| 👨‍🎓 Student | `/student/profile` | `/student/login` |
| 👨‍👩‍👧 Guardian | `/guardian/profile` | `/guardian/login` |

---

## 📸 Profile Picture Quick Guide

### Upload Steps (All Users):
1. Go to profile page
2. Click "Choose File" button
3. Select image (JPG, PNG, GIF - Max 2MB)
4. Preview appears automatically
5. Click "Upload" button
6. Done! ✅

### Remove Picture:
1. Go to profile page
2. Click "Remove Picture" button
3. Confirm deletion
4. Default icon appears

---

## 🔑 Password Change (All Users)

### Requirements:
- ✅ Minimum 8 characters (Students: 12)
- ✅ Uppercase + Lowercase letters
- ✅ Numbers (0-9)
- ✅ Special symbols (!@#$%)

### Steps:
1. Click "Change Password"
2. Enter current password
3. Enter new password
4. Confirm new password
5. Submit ✅

---

## ✏️ Editable Fields by User Type

### Admin / Co-Admin:
- ✅ Name
- ✅ Email
- ✅ Profile Picture

### Teacher:
- ✅ Email
- ✅ Phone
- ✅ Address
- ✅ Profile Picture
- ❌ Name, Employee#, Department (Admin only)

### Student:
- ✅ Email
- ✅ Mobile Number
- ✅ Address
- ✅ Guardian Info (Name, Contact, Email)
- ✅ Profile Picture
- ❌ Name, Student#, Grade (Admin only)

### Guardian:
- ✅ Name (First, Middle, Last, Suffix)
- ✅ Gender
- ✅ Mobile Number
- ✅ Address
- ✅ Profile Picture

---

## 🎨 Profile Layout

```
┌─────────────────────────────────────┐
│     MY PROFILE HEADER               │
└─────────────────────────────────────┘

┌─────────────┬───────────────────────┐
│ 📸 PICTURE  │  ℹ️ INFORMATION       │
│             │                       │
│  [Photo]    │  Name: John Doe      │
│             │  Type: Administrator  │
│  Upload     │  Email: john@...     │
│  Remove     │                       │
│             │  [Edit Profile]       │
│ 🔒 Security │                       │
│  Change     │  📋 Details Table     │
│  Password   │  ...                  │
└─────────────┴───────────────────────┘
```

---

## 🛠️ Common Actions

| Action | Location | Button |
|--------|----------|--------|
| Edit Profile | Profile Page | Blue "Edit Profile" |
| Upload Picture | Profile Page | Info "Choose File" |
| Remove Picture | Profile Page | Red "Remove Picture" |
| Change Password | Profile Page | Red "Change Password" |
| Save Changes | Edit Page | Info "Save Changes" |
| Cancel | Edit Page | Gray "Cancel" |

---

## 📁 Route Names (For Developers)

### Admin Routes:
```php
route('admin.profile.show')
route('admin.profile.edit')
route('admin.profile.update')
route('admin.profile.picture.update')    // NEW!
route('admin.profile.picture.delete')    // NEW!
route('admin.profile.password.edit')
route('admin.profile.password.update')
```

### Teacher Routes:
```php
route('teacher.profile.show')
route('teacher.profile.edit')
route('teacher.profile.update')
route('teacher.profile.picture.update')
route('teacher.profile.picture.delete')
route('teacher.profile.password.update')
route('teacher.profile.subjects.all')
```

### Student Routes:
```php
route('student.profile.show')
route('student.profile.edit')
route('student.profile.update')
route('student.profile.picture.update')
route('student.profile.picture.delete')
route('student.profile.password.edit')
route('student.profile.password.update')
```

### Guardian Routes:
```php
route('guardian.profile.show')
route('guardian.profile.edit')
route('guardian.profile.update')
```

---

## ⚡ Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| Picture won't upload | Check file size (<2MB) and format (JPG/PNG/GIF) |
| Can't edit certain fields | Some fields are admin-only (by design) |
| Password change fails | Ensure current password is correct |
| Picture not showing | Run `php artisan storage:link` |
| Email already exists | Email must be unique across system |

---

## 🎯 Feature Comparison Table

| Feature | Admin | Teacher | Student | Guardian |
|---------|:-----:|:-------:|:-------:|:--------:|
| Profile Picture | ✅ | ✅ | ✅ | ✅ |
| Edit Name | ✅ | ❌ | ❌ | ✅ |
| Edit Email | ✅ | ✅ | ✅ | ❌ |
| Edit Phone | ❌ | ✅ | ✅ | ✅ |
| Edit Address | ❌ | ✅ | ✅ | ✅ |
| Change Password | ✅ | ✅ | ✅ | ✅ |
| View Students | ❌ | ❌ | ❌ | ✅ |
| View Subjects | ❌ | ✅ | ❌ | ❌ |

---

## 🔐 Security Notes

✅ **All profiles are protected by authentication**
✅ **Users can only access their own profile**
✅ **Passwords are encrypted (bcrypt)**
✅ **File uploads are validated**
✅ **Email changes are unique-checked**

---

## 💡 Tips & Best Practices

1. **Profile Pictures:**
   - Use clear, recent photos
   - Keep file size small for faster loading
   - Square images work best (circular crop)

2. **Passwords:**
   - Change regularly (every 3-6 months)
   - Don't share with anyone
   - Use mix of characters for strength

3. **Email Updates:**
   - Use active email for notifications
   - Confirm email before logging out
   - Email is used for password recovery

4. **Contact Info:**
   - Keep phone numbers up-to-date
   - Update address if you move
   - Ensure guardian info is accurate (students)

---

## 📞 Need Help?

- **Students/Teachers:** Contact school administrator
- **Guardians:** Contact school office
- **Admins:** Check technical documentation

---

**Last Updated:** December 15, 2025
**Version:** 2.0
**Status:** ✅ All Features Active
