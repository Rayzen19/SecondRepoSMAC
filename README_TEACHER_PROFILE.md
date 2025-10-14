# Teacher Profile Feature - Implementation Guide

## Overview
This feature allows teachers to:
- View their complete profile information
- Edit contact information (email, phone, address)
- Upload and manage profile pictures
- Change their account password

This feature mirrors the student profile functionality but is tailored for teacher accounts.

## Features Implemented

### 1. **Profile Viewing** (`/teacher/profile`)
Teachers can view their complete profile including:
- Personal information (name, employee number, gender)
- Contact information (email, phone, address)
- Professional information (department, term, specialization)
- Current profile picture
- Account status

### 2. **Profile Editing** (`/teacher/profile/edit`)
Teachers can update:
- ✅ Email address
- ✅ Phone number
- ✅ Address

**Read-only fields** (only admin can change):
- Employee number
- Name (first, middle, last, suffix)
- Department
- Term
- Specialization
- Status
- Gender

### 3. **Profile Picture Management**
- **Upload:** JPG, PNG, GIF (max 2MB)
- **Update:** Replace existing picture
- **Delete:** Remove profile picture
- Pictures stored in: `storage/app/public/profile_pictures/teachers/`

### 4. **Password Change** (`/teacher/profile/password/edit`)
- Requires current password verification
- New password must be at least 8 characters
- Password confirmation required

## Files Created/Modified

### Controllers
✅ **Created:** `app/Http/Controllers/Teacher/ProfileController.php`
- `show()` - Display profile
- `edit()` - Show edit form
- `update()` - Update profile
- `updateProfilePicture()` - Upload/update picture
- `deleteProfilePicture()` - Remove picture
- `editPassword()` - Show password change form
- `updatePassword()` - Update password

### Views
✅ **Created:** `resources/views/teacher/profile/show.blade.php` - Profile display page
✅ **Created:** `resources/views/teacher/profile/edit.blade.php` - Profile edit form
✅ **Created:** `resources/views/teacher/profile/change-password.blade.php` - Password change form

### Routes
✅ **Modified:** `routes/web.php` - Added 7 new routes:
```php
Route::get('/profile', 'show')->name('teacher.profile.show');
Route::get('/profile/edit', 'edit')->name('teacher.profile.edit');
Route::put('/profile', 'update')->name('teacher.profile.update');
Route::post('/profile/picture', 'updateProfilePicture')->name('teacher.profile.picture.update');
Route::delete('/profile/picture', 'deleteProfilePicture')->name('teacher.profile.picture.delete');
Route::get('/profile/password/edit', 'editPassword')->name('teacher.profile.password.edit');
Route::put('/profile/password', 'updatePassword')->name('teacher.profile.password.update');
```

### Navigation
✅ **Modified:** `resources/views/teacher/components/template.blade.php` - Added "My Profile" to sidebar

## Setup Instructions

### 1. Ensure Storage Link Exists
If not already created, run:
```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` so profile pictures are accessible.

### 2. Verify Permissions (Linux/Mac only)
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Usage Guide for Teachers

### Viewing Profile
1. Log in to teacher portal
2. Click "My Profile" in the sidebar
3. View all personal and professional information

### Editing Profile
1. Click "Edit Profile" button on profile page
2. Update email, phone, or address
3. Click "Save Changes"
4. Email changes sync with login credentials automatically

### Uploading Profile Picture
1. On profile page, click "Choose File"
2. Select image (JPG, PNG, GIF - max 2MB)
3. Click "Upload New Picture"
4. Old picture is automatically deleted

### Removing Profile Picture
1. Click "Remove Picture" button
2. Confirm deletion
3. Default avatar icon will be displayed

### Changing Password
1. Click "Change Password" button
2. Enter current password
3. Enter new password (minimum 8 characters)
4. Confirm new password
5. Click "Change Password"

## Validation Rules

### Profile Update
| Field | Rules |
|-------|-------|
| Email | Required, valid email, unique (excluding current teacher) |
| Phone | Required, max 20 characters |
| Address | Optional, max 500 characters |

### Profile Picture
| Rule | Value |
|------|-------|
| File Types | jpeg, png, jpg, gif |
| Max Size | 2MB (2048KB) |

### Password Change
| Field | Rules |
|-------|-------|
| Current Password | Required, must match existing |
| New Password | Required, minimum 8 characters, must be confirmed |

## Security Features
✅ Authentication required (teacher guard)
✅ Teachers can only view/edit their own profile
✅ Current password verification for password changes
✅ Profile pictures validated for type and size
✅ Old pictures automatically deleted when replaced
✅ Email changes sync with user authentication table

## Technical Details

### Database
Uses existing `teachers` table with `profile_picture` column.

### Storage Structure
```
storage/
  app/
    public/
      profile_pictures/
        teachers/
          [uploaded images]
```

### Guards & Authentication
- Uses `auth:teacher` middleware
- Accesses teacher data via `Auth::guard('teacher')->user()`
- Links to `teachers` table via `user_pk_id`

## Differences from Student Profile

| Feature | Student | Teacher |
|---------|---------|---------|
| Editable Fields | Email, mobile, address, guardian info | Email, phone, address |
| Read-only Info | Student number, name, program | Employee number, name, department |
| Storage Path | `profile_pictures/` | `profile_pictures/teachers/` |
| Additional Info | Guardian information, academic year | Department, term, specialization |

## Testing Checklist

### Profile Viewing
- [ ] Access `/teacher/profile` after login
- [ ] Verify all teacher information displays correctly
- [ ] Check profile picture displays (or default icon)
- [ ] Verify professional information shows

### Profile Editing
- [ ] Click "Edit Profile" button
- [ ] Update email, phone, address
- [ ] Save changes successfully
- [ ] Verify changes reflect on profile page
- [ ] Check email updated in users table too

### Profile Picture
- [ ] Upload JPG image successfully
- [ ] Upload PNG image successfully
- [ ] Try uploading file > 2MB (should fail)
- [ ] Try uploading PDF (should fail)
- [ ] Replace existing picture
- [ ] Delete picture
- [ ] Verify old pictures are deleted from storage

### Password Change
- [ ] Access password change page
- [ ] Enter wrong current password (should fail)
- [ ] Enter new password < 8 chars (should fail)
- [ ] Enter mismatched password confirmation (should fail)
- [ ] Successfully change password
- [ ] Log out and log in with new password

### Security
- [ ] Try accessing another teacher's profile (should fail)
- [ ] Try editing profile while logged out (redirect to login)
- [ ] Verify CSRF protection on all forms

## Troubleshooting

### Profile Pictures Not Displaying
1. Check if storage link exists: `ls -la public/storage` (Linux/Mac) or `dir public\storage` (Windows)
2. Run `php artisan storage:link`
3. Verify `APP_URL` in `.env` is correct
4. Check file permissions

### Upload Fails
1. Check `php.ini`:
   - `upload_max_filesize` (should be ≥ 2M)
   - `post_max_size` (should be ≥ 2M)
2. Verify storage directory is writable
3. Check available disk space

### Profile Update Fails
1. Check validation errors on form
2. Ensure email is unique
3. Verify all required fields are filled

### Email Not Syncing
- The controller automatically updates the `users` table when email is changed
- Check that teacher has corresponding user account

## Future Enhancements (Optional)
- [ ] Image cropping/resizing before upload
- [ ] Profile completion percentage
- [ ] Email verification for profile changes
- [ ] Two-factor authentication
- [ ] Activity log (profile changes history)
- [ ] Export profile as PDF

---

**Status:** ✅ Fully Implemented and Ready to Use
**Date:** October 15, 2025
**Compatible With:** Student Profile Feature
