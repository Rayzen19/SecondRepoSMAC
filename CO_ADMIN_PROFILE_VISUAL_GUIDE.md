# Co-Admin Profile - Visual Guide

## 🎯 Step-by-Step Guide with Screenshots Reference

### Step 1: Login as Co-Admin

**What you see:**
- Admin login page at `/admin/login`
- Email and password fields

**What to do:**
```
1. Go to: http://your-domain/admin/login
2. Enter co-admin email
3. Enter password
4. Click "Login"
```

---

### Step 2: Access Profile from Sidebar

**What you see:**
- Admin dashboard
- Left sidebar with menu items
- "My Profile" option with user icon

**What to do:**
```
1. Look at the left sidebar
2. Find "My Profile" with icon: 🔷
3. Click on "My Profile"
```

**Location in sidebar:**
```
Dashboard
├── Students
├── Teachers
├── ...
└── My Profile  ← Click here!
```

---

### Step 3: View Your Profile

**What you see:**
- Profile page with two sections:
  1. **Left side**: Profile card with:
     - Large circular initial (first letter of name)
     - Your name
     - "Co-Administrator" badge
     - Your email
     - "Edit Profile" button (blue)
     - "Change Password" button (red outline)
  
  2. **Right side**: Account Information card with:
     - Full Name
     - Email Address
     - Account Type: "Co-Administrator" (blue badge)
     - Member Since date
     - Last Updated date

**Profile Card Layout:**
```
┌─────────────────────────────────┐
│         [  M  ]                 │  ← Your initial
│                                 │
│       Your Name Here            │
│   🔷 Co-Administrator           │
│   ✉ youremail@example.com      │
│                                 │
│   [  Edit Profile  ]            │  ← Blue button
│   [  Change Password  ]         │  ← Red outline button
└─────────────────────────────────┘
```

---

### Step 4a: Edit Your Profile

**What to do:**
```
1. Click "Edit Profile" button
2. You'll see a form with:
   - Full Name field (pre-filled)
   - Email Address field (pre-filled)
3. Change what you need
4. Click "Save Changes" (blue button)
   OR
   Click "Cancel" to go back
```

**Edit Form Layout:**
```
┌─────────────────────────────────────────┐
│  ✏ Edit Profile                         │
├─────────────────────────────────────────┤
│                                         │
│  Full Name *                            │
│  [John Doe                          ]   │
│                                         │
│  Email Address *                        │
│  [john@example.com                  ]   │
│  ℹ This email will be used for login   │
│                                         │
│  [ ✓ Save Changes ]  [ ✗ Cancel ]      │
└─────────────────────────────────────────┘
```

**Success:**
- Green success message: "✓ Profile updated successfully."
- Redirected back to profile view
- Changes are visible immediately

---

### Step 4b: Change Your Password

**What to do:**
```
1. Click "Change Password" button
2. You'll see a password change form with:
   - Current Password field
   - New Password field (with strength indicator)
   - Confirm New Password field
   - Eye icons to show/hide passwords
3. Fill in all fields
4. Watch the password strength indicator
5. Ensure passwords match
6. Click "Change Password" (red button)
```

**Password Form Layout:**
```
┌─────────────────────────────────────────┐
│  🔒 Change Password                      │
├─────────────────────────────────────────┤
│  ℹ Password Requirements:                │
│  • At least 8 characters                │
│  • Uppercase and lowercase letters      │
│  • At least one number                  │
│  • At least one special character       │
├─────────────────────────────────────────┤
│  Current Password *                      │
│  [••••••••••                        ] 👁 │
│                                         │
│  New Password *                         │
│  [••••••••••                        ] 👁 │
│  Password strength: Strong 💪           │
│                                         │
│  Confirm New Password *                 │
│  [••••••••••                        ] 👁 │
│  ✓ Passwords match                      │
│                                         │
│  [ ✓ Change Password ]  [ ✗ Cancel ]    │
└─────────────────────────────────────────┘
```

**Password Strength Indicator:**
- 🔴 **Weak** - Too simple, not secure
- 🟠 **Fair** - Acceptable but could be better
- 🔵 **Good** - Good password
- 🟢 **Strong** - Excellent, very secure!

**Success:**
- Green success message: "✓ Password changed successfully."
- Redirected back to profile view
- Can login with new password immediately

---

## 🎨 Visual Elements Guide

### Badge Colors
- 🟢 **Green "Administrator"** - Main admin
- 🔵 **Blue "Co-Administrator"** - Co-admin (you!)

### Button Styles
- 🔵 **Blue buttons** - Primary actions (Save, Edit)
- 🔴 **Red buttons** - Password/security actions
- ⚪ **Gray buttons** - Cancel/secondary actions

### Icons Used
- 🔷 User icon - Profile menu
- ✏ Edit icon - Edit profile
- 🔒 Lock icon - Change password
- 👁 Eye icon - Show/hide password
- ✓ Check icon - Success/confirm
- ✗ X icon - Cancel/close

---

## 📱 Mobile Responsive

The profile pages work on mobile devices:
- Sidebar collapses into hamburger menu
- Cards stack vertically
- Forms are touch-friendly
- All buttons are easy to tap

---

## 🚨 Error Messages

### Common Errors & What They Look Like

**Email Already Taken:**
```
❌ The email has already been taken.
```
↳ Solution: Use a different email address

**Current Password Incorrect:**
```
❌ The current password is incorrect.
```
↳ Solution: Re-enter your current password carefully

**Passwords Don't Match:**
```
✗ Passwords do not match
```
↳ Solution: Make sure both password fields are identical

**Weak Password:**
```
Password strength: Weak
```
↳ Solution: Add more character types (uppercase, numbers, symbols)

---

## ✅ Success Messages

**Profile Updated:**
```
✓ Profile updated successfully.
```

**Password Changed:**
```
✓ Password changed successfully.
```

---

## 🔗 Navigation Breadcrumbs

**On Edit Profile page:**
```
Profile > Edit
```

**On Change Password page:**
```
Profile > Change Password
```

Click "Profile" link to go back to profile view anytime.

---

## 💡 Pro Tips

1. **Use Password Visibility Toggle**
   - Click the eye icon (👁) to see what you're typing
   - Helps avoid typos in passwords

2. **Watch the Strength Indicator**
   - Aim for "Good" or "Strong"
   - Green is best!

3. **Check Match Indicator**
   - Wait for "✓ Passwords match" before submitting
   - Red "✗" means they don't match yet

4. **Bookmark Your Profile**
   - Direct URL: `/admin/profile`
   - Quick access to your info

5. **Update Regularly**
   - Change password every few months
   - Keep email current for notifications

---

## 📋 Keyboard Shortcuts

- **Tab** - Move between fields
- **Enter** - Submit form (when on a button)
- **Esc** - Close alerts/modals

---

## 🎯 Quick Reference Card

```
╔══════════════════════════════════════╗
║      CO-ADMIN PROFILE GUIDE          ║
╠══════════════════════════════════════╣
║ VIEW PROFILE                         ║
║ → Sidebar: Click "My Profile"        ║
╠══════════════════════════════════════╣
║ EDIT PROFILE                         ║
║ → Click "Edit Profile" button        ║
║ → Update name/email                  ║
║ → Click "Save Changes"               ║
╠══════════════════════════════════════╣
║ CHANGE PASSWORD                      ║
║ → Click "Change Password" button     ║
║ → Enter current password             ║
║ → Enter new password (strong!)       ║
║ → Confirm new password               ║
║ → Click "Change Password"            ║
╠══════════════════════════════════════╣
║ TIPS                                 ║
║ • Use eye icon to show passwords     ║
║ • Check strength indicator           ║
║ • Ensure passwords match             ║
║ • Email must be unique               ║
╚══════════════════════════════════════╝
```

---

**Need more help?** See `CO_ADMIN_PROFILE_FEATURE.md` for complete documentation.
