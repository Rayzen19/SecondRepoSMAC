# Student Profile Feature - Setup Instructions

## Overview
This feature allows students to:
- View their complete profile information
- Edit contact and guardian information
- Upload and manage profile pictures
- Change their account password

## Installation Steps

### 1. Create Storage Link
Run this command to create a symbolic link from `public/storage` to `storage/app/public`:

```bash
php artisan storage:link
```

This is required for profile pictures to be accessible via the web.

### 2. Set Proper Permissions (Linux/Mac)
If you're on Linux or Mac, ensure the storage directory has proper permissions:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 3. Create Profile Pictures Directory
The profile pictures will be automatically stored in `storage/app/public/profile_pictures/`. This directory will be created automatically when the first image is uploaded.

## Usage

### For Students

#### Viewing Profile
1. Log in to the student portal
2. Click "My Profile" in the sidebar
3. View all personal, academic, and guardian information

#### Editing Profile
1. Go to your profile page
2. Click "Edit Profile" button
3. Update the following information:
   - Email address
   - Mobile number
   - Address
   - Guardian name
   - Guardian contact number
   - Guardian email
4. Click "Save Changes"

**Note:** Personal information (name, student number, birthdate, etc.) cannot be edited by students. Contact the administrator to update these fields.

#### Uploading Profile Picture
1. Go to your profile page
2. In the "Profile Picture" section, click "Choose File"
3. Select an image (JPG, PNG, GIF format, max 2MB)
4. Click "Upload New Picture"

#### Removing Profile Picture
1. Go to your profile page
2. Click "Remove Picture" button
3. Confirm the deletion

#### Changing Password
1. Go to your profile page
2. Click "Change Password" in the Security section
3. Enter your current password
4. Enter and confirm your new password (minimum 8 characters)
5. Click "Change Password"

## Technical Details

### Routes
- `GET /student/profile` - View profile
- `GET /student/profile/edit` - Edit profile form
- `PUT /student/profile` - Update profile
- `POST /student/profile/picture` - Upload profile picture
- `DELETE /student/profile/picture` - Remove profile picture
- `GET /student/profile/password/edit` - Change password form
- `PUT /student/profile/password` - Update password

### Controller
`App\Http\Controllers\Student\ProfileController`

### Views
- `resources/views/student/profile/show.blade.php` - Profile display
- `resources/views/student/profile/edit.blade.php` - Profile edit form
- `resources/views/student/profile/change-password.blade.php` - Password change form

### Database
The `profile_picture` field already exists in the `students` table and stores the relative path to the image in storage.

### Validation Rules

#### Profile Update
- **Email:** Required, valid email format, unique (excluding current student)
- **Mobile Number:** Required, max 20 characters, unique (excluding current student)
- **Address:** Optional, max 500 characters
- **Guardian Name:** Optional, max 255 characters
- **Guardian Contact:** Required, max 20 characters, unique (excluding current student)
- **Guardian Email:** Required, valid email format, unique (excluding current student)

#### Profile Picture Upload
- **File Type:** Image (jpeg, png, jpg, gif)
- **Max Size:** 2MB (2048KB)

#### Password Change
- **Current Password:** Required (must match existing password)
- **New Password:** Required, minimum 8 characters, must be confirmed

## Security Features
- Authentication required for all profile operations
- Students can only view/edit their own profile
- Current password verification required for password changes
- Profile pictures are validated for type and size
- Old profile pictures are automatically deleted when replaced

## Troubleshooting

### Images Not Displaying
1. Verify storage link exists: `ls -la public/storage`
2. Check permissions: `ls -la storage/app/public`
3. Ensure APP_URL is set correctly in `.env`

### Upload Fails
1. Check `php.ini` settings:
   - `upload_max_filesize` (should be at least 2M)
   - `post_max_size` (should be at least 2M)
2. Verify storage directory permissions
3. Check available disk space

### Profile Update Fails
1. Check validation errors displayed on the form
2. Ensure email/contact numbers are unique
3. Verify all required fields are filled

## Future Enhancements (Optional)
- Image cropping/resizing before upload
- Support for multiple profile pictures/gallery
- Profile completion percentage indicator
- Email verification for profile changes
- Two-factor authentication integration
