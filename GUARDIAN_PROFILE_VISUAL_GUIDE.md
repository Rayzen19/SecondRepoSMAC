# Guardian Profile Feature - Visual Guide

## 📍 Navigation Flow

```
Guardian Login
    ↓
Dashboard
    ↓
Click "Profile" in Sidebar
    ↓
Profile View Page
    ↓
[Edit Profile] → Edit Form → Save → Back to Profile View
[Change Password] → Modal → Update → Back to Profile View
```

## 🎨 Page Layouts

### Profile View Page

```
┌─────────────────────────────────────────────────────────────────┐
│ My Profile                                    Dashboard > Profile │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────┐  ┌────────────────────────────────────┐  │
│  │  PROFILE CARD    │  │     STATISTICS CARDS               │  │
│  │                  │  │  ┌────────┐      ┌────────┐        │  │
│  │   ┌────────┐     │  │  │  📊 5  │      │  ✅ 4  │        │  │
│  │   │  JP    │     │  │  │ Total  │      │ Active │        │  │
│  │   │ Avatar │     │  │  └────────┘      └────────┘        │  │
│  │   └────────┘     │  │                                     │  │
│  │                  │  ├─────────────────────────────────────┤
│  │ Juan Dela Cruz   │  │  MY STUDENTS                        │  │
│  │ GDN-2024-001    │  │  ┌────────────────────────────────┐ │  │
│  │ 🟢 Active        │  │  │ STU-001 | Maria  | ABM | 11  │ │  │
│  │                  │  │  │ STU-002 | Pedro  | STEM| 12  │ │  │
│  ├──────────────────┤  │  │ STU-003 | Ana    | GAS | 11  │ │  │
│  │ 📧 Email         │  │  │ STU-004 | Jose   | STEM| 11  │ │  │
│  │ juan@email.com  │  │  │ STU-005 | Rosa   | ABM | 12  │ │  │
│  │                  │  │  └────────────────────────────────┘ │  │
│  │ 📱 Mobile        │  └─────────────────────────────────────┘  │
│  │ 09171234567     │                                            │
│  │                  │                                            │
│  │ 📍 Address       │                                            │
│  │ Manila City     │                                            │
│  ├──────────────────┤                                            │
│  │ ℹ️ Gender: Male  │                                            │
│  │ 📅 Since: 2024   │                                            │
│  ├──────────────────┤                                            │
│  │ [Edit Profile]   │                                            │
│  │ [Change Password]│                                            │
│  └──────────────────┘                                            │
└─────────────────────────────────────────────────────────────────┘
```

### Profile Edit Page

```
┌─────────────────────────────────────────────────────────────────┐
│ Edit Profile                  Dashboard > Profile > Edit         │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│              ┌────────┐                                           │
│              │  JP    │  [Choose File]                            │
│              │ Avatar │  Recommended: Square, max 2MB             │
│              └────────┘                                           │
│                                                                   │
│  ─────────────────────────────────────────────────────────────  │
│                                                                   │
│  👤 Basic Information                                             │
│                                                                   │
│  First Name *            Last Name *                              │
│  ┌──────────────┐       ┌──────────────┐                        │
│  │ Juan         │       │ Dela Cruz    │                        │
│  └──────────────┘       └──────────────┘                        │
│                                                                   │
│  Middle Name             Gender *                                 │
│  ┌──────────────┐       ┌──────────────┐                        │
│  │ Pablo        │       │ [v] Male     │                        │
│  └──────────────┘       └──────────────┘                        │
│                                                                   │
│  ─────────────────────────────────────────────────────────────  │
│                                                                   │
│  📧 Contact Information                                           │
│                                                                   │
│  Email Address                                                    │
│  ┌──────────────────────────────────────┐                       │
│  │ juan@email.com (Cannot be changed)   │ 🔒                    │
│  └──────────────────────────────────────┘                       │
│                                                                   │
│  Mobile Number *                                                  │
│  ┌──────────────────────────────────────┐                       │
│  │ 09171234567                          │                       │
│  └──────────────────────────────────────┘                       │
│  Format: 09XXXXXXXXX (11 digits)                                 │
│                                                                   │
│  Address                                                          │
│  ┌──────────────────────────────────────┐                       │
│  │ 123 Main St, Manila City            │                       │
│  │                                      │                       │
│  └──────────────────────────────────────┘                       │
│                                                                   │
│  ─────────────────────────────────────────────────────────────  │
│                                                                   │
│  [← Cancel]                          [✓ Save Changes]            │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

### Change Password Modal

```
                 ┌───────────────────────────┐
                 │   Change Password      ×  │
                 ├───────────────────────────┤
                 │                           │
                 │ Current Password *        │
                 │ ┌──────────────────────┐  │
                 │ │ ••••••••             │  │
                 │ └──────────────────────┘  │
                 │                           │
                 │ New Password *            │
                 │ ┌──────────────────────┐  │
                 │ │ ••••••••             │  │
                 │ └──────────────────────┘  │
                 │                           │
                 │ Confirm New Password *    │
                 │ ┌──────────────────────┐  │
                 │ │ ••••••••             │  │
                 │ └──────────────────────┘  │
                 │                           │
                 ├───────────────────────────┤
                 │ [Cancel] [Update Password]│
                 └───────────────────────────┘
```

## 📱 Sidebar Navigation

```
┌────────────────────┐
│  SMAC LOGO         │
├────────────────────┤
│  GUARDIAN          │
├────────────────────┤
│  📊 Dashboard      │
│  👥 Students       │
│  👤 Profile    ✓   │  ← NEW!
│  ✉️  Messages      │
│                    │
│  [🚪 Logout]       │
└────────────────────┘
```

## 🎯 User Interactions

### 1. Viewing Profile
```
┌─────────┐
│ LOGIN   │
└────┬────┘
     │
     ▼
┌─────────────┐
│ DASHBOARD   │
└────┬────────┘
     │
     │ Click "Profile"
     ▼
┌─────────────────┐
│ PROFILE VIEW    │
│ • See info      │
│ • See students  │
│ • See stats     │
└─────────────────┘
```

### 2. Editing Profile
```
┌─────────────────┐
│ PROFILE VIEW    │
└────┬────────────┘
     │
     │ Click "Edit Profile"
     ▼
┌─────────────────┐
│ EDIT FORM       │
│ • Modify fields │
│ • Upload photo  │
└────┬────────────┘
     │
     │ Click "Save Changes"
     ▼
┌─────────────────┐
│ PROFILE VIEW    │
│ ✅ Updated!     │
└─────────────────┘
```

### 3. Changing Password
```
┌─────────────────┐
│ PROFILE VIEW    │
└────┬────────────┘
     │
     │ Click "Change Password"
     ▼
┌─────────────────┐
│ MODAL OPENS     │
│ • Enter current │
│ • Enter new     │
│ • Confirm new   │
└────┬────────────┘
     │
     │ Click "Update Password"
     ▼
┌─────────────────┐
│ PROFILE VIEW    │
│ ✅ Updated!     │
└─────────────────┘
```

## 🎨 Color Scheme

### Status Badges
- 🟢 **Active**: Green background (#28a745)
- 🟡 **Inactive**: Gray background (#6c757d)
- 🔵 **Graduated**: Blue background (#007bff)

### Statistics Cards
- **Total Students**: Blue border (#007bff)
- **Active Students**: Green border (#28a745)

### Buttons
- **Primary (Edit/Save)**: Blue (#007bff)
- **Secondary (Cancel)**: Gray (#6c757d)
- **Danger (Delete)**: Red (#dc3545)

### Icons
- Profile: `ti ti-user`
- Students: `ti ti-users`
- Email: `ti ti-mail`
- Phone: `ti ti-phone`
- Address: `ti ti-map-pin`
- Edit: `ti ti-edit`
- Lock: `ti ti-lock`
- Check: `ti ti-check`

## 📊 Data Display

### Profile Information Card
```
┌─────────────────────────┐
│     👤 JP               │  ← Avatar/Photo
│   Guardian Name         │  ← Full name
│   GDN-2024-001         │  ← Guardian number
│   🟢 Active            │  ← Status badge
│ ─────────────────────── │
│ 📧 Email               │
│ guardian@email.com     │
│                        │
│ 📱 Mobile              │
│ 09171234567           │
│                        │
│ 📍 Address             │
│ Full address here      │
│ ─────────────────────── │
│ ℹ️ Gender: Male        │
│ 📅 Member Since: 2024  │
│ ─────────────────────── │
│ [Edit Profile]         │
│ [Change Password]      │
└─────────────────────────┘
```

### Student List Table
```
┌────────────────────────────────────────────────────────────┐
│ Student Number │ Name        │ Program │ Level │ Status   │
├────────────────┼─────────────┼─────────┼───────┼──────────┤
│ STU-2024-001   │ 👤 Maria    │ ABM     │ 11    │ 🟢 Active│
│ STU-2024-002   │ 👤 Pedro    │ STEM    │ 12    │ 🟢 Active│
│ STU-2024-003   │ 👤 Ana      │ GAS     │ 11    │ 🟢 Active│
└────────────────────────────────────────────────────────────┘
```

## ✅ Success Messages

```
┌────────────────────────────────────────────────┐
│ ✅ Profile updated successfully.               │
└────────────────────────────────────────────────┘

┌────────────────────────────────────────────────┐
│ ✅ Password updated successfully.              │
└────────────────────────────────────────────────┘
```

## ❌ Error Messages

```
┌────────────────────────────────────────────────┐
│ ❌ Current password is incorrect.              │
└────────────────────────────────────────────────┘

┌────────────────────────────────────────────────┐
│ ❌ The mobile number has already been taken.   │
└────────────────────────────────────────────────┘

┌────────────────────────────────────────────────┐
│ ❌ The first name field is required.           │
└────────────────────────────────────────────────┘
```

## 📱 Mobile Responsive Layout

### Desktop (2-column)
```
┌─────────────────────────────────────┐
│  [Profile Card]  │  [Students List] │
└─────────────────────────────────────┘
```

### Mobile (Stacked)
```
┌──────────────┐
│ Profile Card │
├──────────────┤
│ Students     │
│ List         │
└──────────────┘
```

## 🔒 Security Features

```
┌─────────────────────────────────────────┐
│ 🔐 SECURITY FEATURES                    │
├─────────────────────────────────────────┤
│ ✅ Authentication Required              │
│ ✅ Authorization Check                  │
│ ✅ Password Verification                │
│ ✅ Password Hashing (bcrypt)            │
│ ✅ Input Validation                     │
│ ✅ Unique Mobile Number                 │
│ ✅ CSRF Protection                      │
│ ✅ Session Management                   │
└─────────────────────────────────────────┘
```

## 📋 Field Validations

```
┌─────────────────────────────────────────────────┐
│ Field             │ Validation Rules            │
├───────────────────┼─────────────────────────────┤
│ First Name        │ ✅ Required, Max 255 chars  │
│ Last Name         │ ✅ Required, Max 255 chars  │
│ Middle Name       │ ⚪ Optional, Max 255 chars  │
│ Gender            │ ✅ Required, Male/Female    │
│ Mobile Number     │ ✅ Required, Unique, 11 dig │
│ Address           │ ⚪ Optional, Text           │
│ Email             │ 🔒 Read-only               │
│ Current Password  │ ✅ Required for change     │
│ New Password      │ ✅ Min 8 chars, Confirmed  │
└─────────────────────────────────────────────────┘
```

---

**Legend**:
- ✅ = Required field / Feature enabled
- ⚪ = Optional field
- 🔒 = Read-only / Secure
- 🟢 = Active status
- 🔵 = Info status
- 🟡 = Warning status
- ❌ = Error message
- 👤 = User/Student
- 📊 = Statistics
- 📧 = Email
- 📱 = Mobile
- 📍 = Address
- 📅 = Date
- ℹ️ = Information
