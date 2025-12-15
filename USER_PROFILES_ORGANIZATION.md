# Unified User Profile System - Complete Organization Guide

## 📋 Overview

This document provides a comprehensive overview of the organized user profile system across all user types in the NEWSMAC system. All profiles have been standardized with consistent features, layout, and functionality.

---

## 👥 User Types and Their Profiles

### 1. **Administrator Profile** 👨‍💼
- **Access Route:** `/admin/profile`
- **User Guard:** `auth:admin,co-admin`
- **Controller:** `App\Http\Controllers\Admin\ProfileController`

#### Features:
- ✅ View complete profile information
- ✅ Upload/update profile picture (NEW!)
- ✅ Remove profile picture (NEW!)
- ✅ Edit name and email
- ✅ Change password with strong requirements
- ✅ View account type (Administrator or Co-Administrator)
- ✅ View account creation and update dates

#### Profile Picture:
- **Storage:** `storage/app/public/profile_pictures/admins/`
- **Formats:** JPG, JPEG, PNG, GIF
- **Max Size:** 2MB
- **Default:** Circular badge with first letter of name

---

### 2. **Teacher Profile** 👨‍🏫
- **Access Route:** `/teacher/profile`
- **User Guard:** `auth:teacher`
- **Controller:** `App\Http\Controllers\Teacher\ProfileController`

#### Features:
- ✅ View complete profile (personal, contact, professional info)
- ✅ Upload/update profile picture
- ✅ Remove profile picture
- ✅ Edit email, phone, and address
- ✅ Change password
- ✅ View all assigned subjects
- ✅ Status-based access control (inactive teachers have view-only access)

#### Profile Picture:
- **Storage:** `storage/app/public/profile_pictures/teachers/`
- **Formats:** JPG, JPEG, PNG, GIF
- **Max Size:** 2MB
- **Default:** User icon placeholder

#### Editable Fields:
- Email address (syncs with login)
- Phone number
- Address

#### Read-Only Fields (Admin-only):
- Employee number, Name, Department, Term, Specialization, Gender, Status

---

### 3. **Student Profile** 👨‍🎓
- **Access Route:** `/student/profile`
- **User Guard:** `auth:student`
- **Controller:** `App\Http\Controllers\Student\ProfileController`

#### Features:
- ✅ View complete profile (personal, academic, guardian info)
- ✅ Upload/update profile picture
- ✅ Remove profile picture
- ✅ Edit contact and guardian information
- ✅ Change password with strong requirements

#### Profile Picture:
- **Storage:** `storage/app/public/profile_pictures/`
- **Formats:** JPG, JPEG, PNG, GIF
- **Max Size:** 2MB
- **Default:** User icon placeholder

#### Editable Fields:
- Email address
- Mobile number
- Address
- Guardian name
- Guardian contact number
- Guardian email

#### Read-Only Fields (Admin-only):
- Student number, Name, Birthdate, Gender, Grade level, Section, Strand

---

### 4. **Guardian Profile** 👨‍👩‍👧
- **Access Route:** `/guardian/profile`
- **User Guard:** `auth:guardian`
- **Controller:** `App\Http\Controllers\Guardian\ProfileController`

#### Features:
- ✅ View complete profile
- ✅ Upload/update profile picture
- ✅ Edit personal and contact information
- ✅ Change password
- ✅ View associated students list

#### Profile Picture:
- **Storage:** `storage/app/public/profile_pictures/`
- **Formats:** JPG, PNG, GIF
- **Max Size:** 2MB

#### Editable Fields:
- First name, Middle name, Last name, Suffix
- Gender
- Mobile number
- Address
- Profile picture

---

## 🎨 Standardized Layout Structure

All profile pages follow this consistent layout:

```
┌─────────────────────────────────────────────────┐
│  Header: "My Profile"                           │
│  Success/Error Alerts                           │
└─────────────────────────────────────────────────┘

┌──────────────────┬──────────────────────────────┐
│  LEFT COLUMN     │  RIGHT COLUMN                │
│                  │                              │
│  📸 Profile      │  ℹ️ Profile Header           │
│    Picture       │    (Name, Type Badge, Edit)  │
│    - Upload      │                              │
│    - Remove      │  📋 Account Information      │
│                  │    (Detailed fields table)   │
│  🔒 Security     │                              │
│    - Change      │  📚 Additional Sections      │
│      Password    │    (Type-specific content)   │
│                  │                              │
└──────────────────┴──────────────────────────────┘
```

---

## 🔐 Security Features (All User Types)

### Password Requirements:
- **Minimum Length:** 8 characters (Student: 12 characters)
- **Required Elements:**
  - Uppercase letters
  - Lowercase letters
  - Numbers
  - Special symbols
- **Validation:** Current password required for changes
- **Security:** Passwords are bcrypt hashed

### Profile Picture Security:
- File type validation (images only)
- File size limit (2MB)
- Secure storage in Laravel storage
- Old pictures automatically deleted on update

---

## 📂 File Structure

### Controllers
```
app/Http/Controllers/
├── Admin/
│   └── ProfileController.php      ✅ Updated with profile picture support
├── Teacher/
│   └── ProfileController.php      ✅ Complete
├── Student/
│   └── ProfileController.php      ✅ Complete
└── Guardian/
    └── ProfileController.php      ✅ Complete
```

### Views
```
resources/views/
├── admin/profile/
│   ├── show.blade.php            ✅ Reorganized with profile picture
│   ├── edit.blade.php            ✅ Complete
│   └── password.blade.php        ✅ Complete
├── teacher/profile/
│   ├── show.blade.php            ✅ Complete
│   ├── edit.blade.php            ✅ Complete
│   ├── change-password.blade.php ✅ Complete
│   └── all-subjects.blade.php    ✅ Complete
├── student/profile/
│   ├── show.blade.php            ✅ Complete
│   ├── edit.blade.php            ✅ Complete
│   └── change-password.blade.php ✅ Complete
└── guardian/profile/
    ├── show.blade.php            ✅ Complete
    └── edit.blade.php            ✅ Complete
```

### Routes
```php
// Admin Profile Routes
GET    /admin/profile                    - View profile
GET    /admin/profile/edit               - Edit form
PUT    /admin/profile                    - Update profile
POST   /admin/profile/picture            - Upload picture ✨ NEW
DELETE /admin/profile/picture            - Delete picture ✨ NEW
GET    /admin/profile/password/edit      - Password form
PUT    /admin/profile/password           - Update password

// Teacher Profile Routes
GET    /teacher/profile                  - View profile
GET    /teacher/profile/edit             - Edit form
PUT    /teacher/profile                  - Update profile
POST   /teacher/profile/picture          - Upload picture
DELETE /teacher/profile/picture          - Delete picture
PUT    /teacher/profile/password         - Update password
GET    /teacher/profile/subjects         - All subjects

// Student Profile Routes
GET    /student/profile                  - View profile
GET    /student/profile/edit             - Edit form
PUT    /student/profile                  - Update profile
POST   /student/profile/picture          - Upload picture
DELETE /student/profile/picture          - Delete picture
GET    /student/profile/password/edit    - Password form
PUT    /student/profile/password         - Update password

// Guardian Profile Routes
GET    /guardian/profile                 - View profile
GET    /guardian/profile/edit            - Edit form
PUT    /guardian/profile                 - Update profile
POST   /guardian/profile/picture         - Upload picture (in edit form)
```

---

## 🗄️ Database Structure

### Users Table
```sql
users
├── id
├── name
├── email (unique)
├── password
├── type (admin|teacher|student|guardian)
├── user_pk_id (references type-specific table)
├── profile_picture (nullable) ✨ NEW for admin users
└── timestamps
```

### Type-Specific Tables
```sql
students, teachers, guardians
├── id
├── [type-specific fields]
├── profile_picture (nullable)
└── timestamps
```

---

## 🎯 Key Features Summary

| Feature | Admin | Teacher | Student | Guardian |
|---------|-------|---------|---------|----------|
| View Profile | ✅ | ✅ | ✅ | ✅ |
| Edit Info | ✅ | ✅ (limited) | ✅ (limited) | ✅ |
| Profile Picture | ✅ NEW | ✅ | ✅ | ✅ |
| Change Password | ✅ | ✅ | ✅ | ✅ |
| View Students | ❌ | ❌ | ❌ | ✅ |
| View Subjects | ❌ | ✅ | ❌ | ❌ |
| Status Control | ❌ | ✅ | ❌ | ❌ |

---

## 🚀 Recent Improvements

### ✨ What's New (December 2025)

1. **Admin Profile Enhancement**
   - Added profile picture upload/delete functionality
   - Reorganized layout to match other user types
   - Improved visual consistency
   - Added JavaScript preview for image uploads

2. **Standardization**
   - Consistent layout across all user types
   - Uniform color scheme and styling
   - Standardized button placement and actions
   - Consistent form validation and error handling

3. **Security Enhancements**
   - Strong password requirements across all types
   - Secure file upload handling
   - Profile picture size and type validation

---

## 📝 Usage Instructions

### For Administrators:
1. Login → Click "My Profile" in sidebar
2. **Upload Picture:** Choose file → Preview → Upload
3. **Edit Info:** Click "Edit Profile" → Update → Save
4. **Change Password:** Security Settings → Change Password

### For Teachers:
1. Login → Click "My Profile" in sidebar
2. View complete professional information
3. Update email, phone, address as needed
4. Upload/manage profile picture
5. View all assigned subjects via profile menu

### For Students:
1. Login → Click "My Profile" in sidebar
2. View academic and guardian information
3. Update contact details
4. Manage profile picture
5. Change password for security

### For Guardians:
1. Login → Click "Profile" in sidebar
2. View associated students
3. Update personal and contact information
4. Upload profile picture via edit form

---

## 🔧 Technical Notes

### Profile Picture Handling:
- All pictures stored in `storage/app/public/profile_pictures/`
- Subdirectories: `admins/`, `teachers/` (others in root)
- Accessible via `/storage/` public symlink
- Automatic cleanup of old pictures on update

### Authentication:
- Multiple guards: `admin`, `co-admin`, `teacher`, `student`, `guardian`
- Middleware protection on all profile routes
- Users can only access/edit their own profile

### Validation:
- Server-side validation for all inputs
- Email uniqueness checks across tables
- File upload validation (type, size)
- Password strength enforcement

---

## 📚 Related Documentation

- `CO_ADMIN_PROFILE_SUMMARY.md` - Co-Admin specific features
- `TEACHER_PROFILE_SUMMARY.md` - Teacher profile details
- `README_STUDENT_PROFILE.md` - Student profile guide
- `GUARDIAN_PROFILE_FEATURE.md` - Guardian profile implementation
- `ARCHITECTURE_PROFILE.md` - Technical architecture
- `PROFILE_IMPLEMENTATION_SUMMARY.md` - Implementation details

---

## ✅ System Status

| Component | Status |
|-----------|--------|
| Admin Profile | ✅ Complete & Enhanced |
| Teacher Profile | ✅ Complete |
| Student Profile | ✅ Complete |
| Guardian Profile | ✅ Complete |
| Routes | ✅ All Configured |
| Views | ✅ Standardized |
| Controllers | ✅ Updated |
| Database | ✅ Migration Ready |
| Documentation | ✅ Complete |

---

## 🎉 Conclusion

The user profile system is now fully organized and standardized across all user types. All profiles share:
- ✅ Consistent visual design
- ✅ Uniform functionality
- ✅ Profile picture management
- ✅ Security features
- ✅ User-friendly interfaces

**Last Updated:** December 15, 2025
**Version:** 2.0
**Status:** Production Ready ✨
