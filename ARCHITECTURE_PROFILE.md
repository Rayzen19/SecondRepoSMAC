# Student Profile Feature - Architecture Overview

## 📊 System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        Student Portal                            │
└─────────────────────────────────────────────────────────────────┘
                                 │
                                 │ Authentication
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Student Auth Middleware                       │
│           (auth:student - guards student routes)                 │
└─────────────────────────────────────────────────────────────────┘
                                 │
                                 │
                    ┌────────────┴────────────┐
                    │                         │
                    ▼                         ▼
         ┌──────────────────┐     ┌──────────────────┐
         │  Sidebar Menu    │     │   Profile Routes  │
         │  "My Profile"    │────▶│   /student/...    │
         └──────────────────┘     └──────────────────┘
                                           │
                                           │
                    ┌──────────────────────┼────────────────────────┐
                    │                      │                        │
                    ▼                      ▼                        ▼
         ┌─────────────────┐   ┌─────────────────┐   ┌─────────────────┐
         │  View Profile   │   │  Edit Profile   │   │ Change Password │
         │  show()         │   │  edit()         │   │  editPassword() │
         │                 │   │  update()       │   │  updatePwd()    │
         └─────────────────┘   └─────────────────┘   └─────────────────┘
                    │                      │
                    │                      │
                    ▼                      ▼
         ┌─────────────────────────────────────────┐
         │   Profile Picture Management            │
         │   - updateProfilePicture()              │
         │   - deleteProfilePicture()              │
         └─────────────────────────────────────────┘
                                 │
                                 │
                                 ▼
                    ┌────────────────────────┐
                    │   Laravel Storage      │
                    │   (Public Disk)        │
                    │   /storage/app/public/ │
                    │     profile_pictures/  │
                    └────────────────────────┘
                                 │
                                 │ Symlink
                                 ▼
                    ┌────────────────────────┐
                    │   Public Directory     │
                    │   /public/storage/     │
                    │     profile_pictures/  │
                    └────────────────────────┘
```

## 🗂️ File Structure

```
SMAC/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Student/
│   │           └── ProfileController.php ← NEW! Main controller
│   └── Models/
│       ├── Student.php (already has profile_picture field)
│       └── Auth/
│           └── StudentUser.php (auth model)
│
├── resources/
│   └── views/
│       └── student/
│           ├── components/
│           │   └── template.blade.php (updated with profile link)
│           └── profile/ ← NEW! Profile views folder
│               ├── show.blade.php ← View profile
│               ├── edit.blade.php ← Edit form
│               └── change-password.blade.php ← Password form
│
├── routes/
│   └── web.php (added 7 new profile routes)
│
├── storage/
│   └── app/
│       └── public/
│           └── profile_pictures/ (auto-created on first upload)
│
├── public/
│   └── storage/ (symlink to storage/app/public)
│
└── Documentation/ (NEW!)
    ├── README_STUDENT_PROFILE.md
    ├── PROFILE_IMPLEMENTATION_SUMMARY.md
    └── TESTING_GUIDE_PROFILE.md
```

## 🔄 Data Flow

### 1. View Profile
```
Student clicks "My Profile"
    ↓
Route: GET /student/profile
    ↓
ProfileController@show
    ↓
Auth::guard('student')->user() → Get authenticated user
    ↓
Student::findOrFail($user->user_pk_id) → Get student record
    ↓
Return view with student data
    ↓
Display: show.blade.php
```

### 2. Upload Profile Picture
```
Student selects image file
    ↓
Submits form
    ↓
Route: POST /student/profile/picture
    ↓
ProfileController@updateProfilePicture
    ↓
Validate file (type, size)
    ↓
Delete old picture (if exists)
    ↓
Store new picture in storage/app/public/profile_pictures/
    ↓
Update database: students.profile_picture = path
    ↓
Redirect with success message
```

### 3. Edit Profile
```
Student clicks "Edit Profile"
    ↓
Route: GET /student/profile/edit
    ↓
ProfileController@edit
    ↓
Load student data into form
    ↓
Display: edit.blade.php
    ↓
Student updates fields
    ↓
Route: PUT /student/profile
    ↓
ProfileController@update
    ↓
Validate input (email unique, required fields, etc.)
    ↓
Update database: students table
    ↓
Redirect to profile with success message
```

### 4. Change Password
```
Student clicks "Change Password"
    ↓
Route: GET /student/profile/password/edit
    ↓
ProfileController@editPassword
    ↓
Display: change-password.blade.php
    ↓
Student enters current + new password
    ↓
Route: PUT /student/profile/password
    ↓
ProfileController@updatePassword
    ↓
Validate: current password correct?
    ↓
Validate: new password meets requirements?
    ↓
Hash new password
    ↓
Update database: users.password
    ↓
Redirect to profile with success message
```

## 🔐 Security Layers

```
┌────────────────────────────────────────────┐
│  1. Route Middleware: auth:student         │
│     → Only logged-in students access       │
└────────────────────────────────────────────┘
                   ↓
┌────────────────────────────────────────────┐
│  2. Controller: Get Auth User              │
│     → Auth::guard('student')->user()       │
└────────────────────────────────────────────┘
                   ↓
┌────────────────────────────────────────────┐
│  3. Database: Find by user_pk_id           │
│     → Student::findOrFail($user_pk_id)     │
└────────────────────────────────────────────┘
                   ↓
┌────────────────────────────────────────────┐
│  4. Validation: Unique constraints         │
│     → email, mobile, contacts unique       │
└────────────────────────────────────────────┘
                   ↓
┌────────────────────────────────────────────┐
│  5. Password: Hash verification            │
│     → Hash::check() for current password   │
└────────────────────────────────────────────┘
                   ↓
┌────────────────────────────────────────────┐
│  6. File Upload: Type & size validation    │
│     → image|mimes:jpeg,png,jpg,gif|max:2MB │
└────────────────────────────────────────────┘
```

## 📊 Database Schema

### students table
```sql
┌─────────────────────┬──────────────┬──────────────┐
│ Column              │ Type         │ Editable?    │
├─────────────────────┼──────────────┼──────────────┤
│ id                  │ bigint       │ No           │
│ student_number      │ varchar(255) │ No           │
│ first_name          │ varchar(255) │ No           │
│ middle_name         │ varchar(255) │ No           │
│ last_name           │ varchar(255) │ No           │
│ suffix              │ varchar(255) │ No           │
│ gender              │ enum         │ No           │
│ birthdate           │ date         │ No           │
│ email               │ varchar(255) │ YES ✓        │
│ mobile_number       │ varchar(255) │ YES ✓        │
│ address             │ text         │ YES ✓        │
│ guardian_name       │ varchar(255) │ YES ✓        │
│ guardian_contact    │ varchar(255) │ YES ✓        │
│ guardian_email      │ varchar(255) │ YES ✓        │
│ program             │ varchar(255) │ No           │
│ academic_year       │ varchar(255) │ No           │
│ academic_year_id    │ bigint       │ No           │
│ status              │ enum         │ No           │
│ profile_picture     │ varchar(255) │ YES ✓ (file) │
│ created_at          │ timestamp    │ No           │
│ updated_at          │ timestamp    │ No           │
│ deleted_at          │ timestamp    │ No           │
└─────────────────────┴──────────────┴──────────────┘
```

### users table (for authentication)
```sql
┌─────────────────────┬──────────────┬──────────────┐
│ Column              │ Type         │ Usage        │
├─────────────────────┼──────────────┼──────────────┤
│ id                  │ bigint       │ Primary key  │
│ name                │ varchar(255) │ Display name │
│ email               │ varchar(255) │ Login email  │
│ password            │ varchar(255) │ Hashed pwd   │
│ type                │ varchar(255) │ 'student'    │
│ user_pk_id          │ bigint       │ → students.id│
│ created_at          │ timestamp    │              │
│ updated_at          │ timestamp    │              │
└─────────────────────┴──────────────┴──────────────┘
```

## 🎯 Key Features Matrix

| Feature                  | Implemented | Tested | Secure |
|-------------------------|-------------|--------|--------|
| View Profile            | ✅          | ⬜     | ✅     |
| Edit Profile            | ✅          | ⬜     | ✅     |
| Upload Picture          | ✅          | ⬜     | ✅     |
| Remove Picture          | ✅          | ⬜     | ✅     |
| Change Password         | ✅          | ⬜     | ✅     |
| Input Validation        | ✅          | ⬜     | ✅     |
| Error Handling          | ✅          | ⬜     | ✅     |
| Responsive Design       | ✅          | ⬜     | N/A    |
| Authentication Required | ✅          | ⬜     | ✅     |
| File Type Validation    | ✅          | ⬜     | ✅     |
| File Size Limit         | ✅          | ⬜     | ✅     |
| Unique Constraints      | ✅          | ⬜     | ✅     |

## 🚀 Deployment Checklist

Before deploying to production:

- [ ] Run `php artisan storage:link` on production server
- [ ] Set proper file permissions (775 for storage/)
- [ ] Verify APP_URL in production .env
- [ ] Check upload_max_filesize in php.ini (≥2M)
- [ ] Test all features in production environment
- [ ] Monitor error logs for issues
- [ ] Set up regular backups for uploaded images
- [ ] Configure CDN for image delivery (optional)
- [ ] Enable HTTPS for secure uploads
- [ ] Test on production database

## 📈 Future Enhancements

Potential improvements (not implemented):

1. **Image Processing**
   - Auto-resize/crop uploaded images
   - Generate thumbnails
   - Optimize file size

2. **Advanced Security**
   - Two-factor authentication
   - Email verification for changes
   - Activity log

3. **User Experience**
   - Drag-and-drop upload
   - Preview before upload
   - Progress bar for uploads
   - Profile completion percentage

4. **Social Features**
   - Public/private profile toggle
   - Share profile
   - QR code generator

5. **Analytics**
   - Profile view counter
   - Last updated timestamp
   - Change history log

## 💡 Tips for Maintenance

1. **Regular Backups**: Back up `storage/app/public/profile_pictures/`
2. **Monitor Storage**: Check disk space regularly
3. **Image Cleanup**: Consider cleanup script for orphaned images
4. **Update Validation**: Adjust file size limits as needed
5. **Security Updates**: Keep Laravel and dependencies updated

---

**Status**: ✅ Fully Implemented and Ready for Testing
**Version**: 1.0
**Last Updated**: October 15, 2025
