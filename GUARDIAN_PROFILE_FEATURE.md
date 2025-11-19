# Guardian Self-Service Profile Feature

## Overview
This document describes the self-service profile management feature for guardians, allowing them to view and edit their own profile information without admin intervention.

## Features Implemented

### 1. Profile View Page
- **Route**: `/guardian/profile`
- **Controller**: `App\Http\Controllers\Guardian\ProfileController@show`
- **View**: `resources/views/guardian/profile/show.blade.php`

**Features**:
- Display guardian's profile picture or initials avatar
- Show contact information (email, mobile, address)
- Display account information (gender, member since)
- List all linked students with their details
- Statistics cards showing:
  - Total students count
  - Active students count
- Action buttons:
  - Edit Profile
  - Change Password (modal)

### 2. Profile Edit Page
- **Route**: `/guardian/profile/edit`
- **Controller**: `App\Http\Controllers\Guardian\ProfileController@edit`
- **View**: `resources/views/guardian/profile/edit.blade.php`

**Editable Fields**:
- Profile Picture (upload with preview)
- First Name *
- Last Name *
- Middle Name
- Gender * (Male/Female)
- Mobile Number * (format: 09XXXXXXXXX)
- Address

**Non-Editable**:
- Email (readonly, must contact admin to change)
- Guardian Number (system-generated)

### 3. Profile Update
- **Route**: `PUT /guardian/profile`
- **Controller**: `App\Http\Controllers\Guardian\ProfileController@update`

**Validation Rules**:
```php
'first_name' => 'required|string|max:255'
'middle_name' => 'nullable|string|max:255'
'last_name' => 'required|string|max:255'
'suffix' => 'nullable|string|max:50'
'gender' => 'required|in:male,female'
'mobile_number' => 'required|string|unique:guardians,mobile_number,[except_self]'
'address' => 'nullable|string'
```

**Process**:
1. Validates input data
2. Updates guardian record in `guardians` table
3. Updates corresponding name in `users` table
4. Redirects to profile view with success message

### 4. Password Change
- **Route**: `PUT /guardian/profile/password`
- **Controller**: `App\Http\Controllers\Guardian\ProfileController@updatePassword`
- **UI**: Modal dialog on profile page

**Validation Rules**:
```php
'current_password' => 'required'
'new_password' => 'required|min:8|confirmed'
```

**Process**:
1. Verifies current password is correct
2. Updates password in `users` table with bcrypt hash
3. Redirects to profile view with success message

## Files Created/Modified

### Created Files
1. `app/Http/Controllers/Guardian/ProfileController.php` - Profile management controller
2. `resources/views/guardian/profile/show.blade.php` - Profile display view
3. `resources/views/guardian/profile/edit.blade.php` - Profile edit form

### Modified Files
1. `routes/web.php` - Added profile routes to guardian middleware group
2. `resources/views/guardian/components/template.blade.php` - Added Profile link to sidebar

## Routes Added

```php
Route::middleware('auth:guardian')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('guardian.profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('guardian.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('guardian.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('guardian.profile.updatePassword');
});
```

## Database Tables Used

### guardians
- **Updated Columns**: `first_name`, `middle_name`, `last_name`, `suffix`, `gender`, `mobile_number`, `address`, `profile_picture`
- **Read Columns**: All columns plus relationships

### users
- **Updated Columns**: `name`, `password`
- **Read Columns**: `id`, `email`, `password`

### guardian_students (pivot)
- **Read Only**: Used to load linked students

### students
- **Read Only**: Loaded through guardian relationship

## Navigation

### Sidebar Menu (Guardian Portal)
- Dashboard
- Students
- **Profile** ← New item
- Messages
- Logout

The Profile link appears between "Students" and "Messages" and is highlighted when on any profile-related page (show/edit).

## User Experience

### Viewing Profile
1. Guardian logs into portal
2. Clicks "Profile" in sidebar
3. Sees comprehensive profile with contact info and student list
4. Can click "Edit Profile" or "Change Password"

### Editing Profile
1. From profile page, clicks "Edit Profile" button
2. Sees form with current information pre-filled
3. Can upload new profile picture (preview updates instantly)
4. Modifies desired fields
5. Clicks "Save Changes" - redirects to profile view with success message
6. Clicks "Cancel" - returns to profile view without saving

### Changing Password
1. From profile page, clicks "Change Password" button
2. Modal opens with password form
3. Enters current password
4. Enters new password (min 8 characters)
5. Confirms new password
6. Clicks "Update Password"
7. Modal closes, redirected to profile with success message

## Security Features

1. **Authentication Required**: All routes protected by `auth:guardian` middleware
2. **Authorization**: Guardian can only view/edit their own profile (using `Auth::guard('guardian')->user()->user_pk_id`)
3. **Password Verification**: Current password must be verified before changing to new password
4. **Password Hashing**: Passwords stored using `Hash::make()` (bcrypt)
5. **Validation**: All input validated before processing
6. **Unique Mobile**: Mobile number must be unique across guardians (except own)

## Error Handling

### Validation Errors
- Displayed inline on form fields with red text
- Bootstrap's `.is-invalid` class applied to invalid inputs

### Authentication Errors
- 404 error if guardian profile not found for logged-in user
- Automatic redirect to login if not authenticated

### Password Errors
- "Current password is incorrect" error message if verification fails
- Returns to form with error message preserved

## Success Messages

All success messages displayed using Bootstrap alerts with auto-dismiss:
- "Profile updated successfully." - after profile update
- "Password updated successfully." - after password change

## UI Components

### Profile Picture Handling
- Shows uploaded picture if exists
- Shows initials avatar (first letter of first + last name) if no picture
- Edit page allows uploading new picture with instant preview
- Supports JPG, PNG formats (max 2MB recommended)

### Student List Display
- Table format with columns:
  - Student Number
  - Name (with avatar/picture)
  - Program
  - Grade Level
  - Status badge (color-coded)
- Empty state message if no students linked
- Read-only view (guardian cannot modify student assignments)

### Statistics Cards
- Visual cards showing:
  - Total students count (blue border)
  - Active students count (green border)
- Icon-based design for quick scanning

### Responsive Design
- Mobile-friendly layout
- Two-column layout on desktop (profile card + students)
- Single-column stacked on mobile
- Form fields responsive (2-column on desktop, 1-column on mobile)

## Testing Checklist

- [ ] Guardian can access profile page after login
- [ ] Profile displays correct guardian information
- [ ] Profile displays linked students correctly
- [ ] Statistics cards show correct counts
- [ ] Edit profile button navigates to edit page
- [ ] Edit form shows current data pre-filled
- [ ] Email field is readonly on edit page
- [ ] Profile picture upload works with preview
- [ ] Form validation works (required fields, mobile format, etc.)
- [ ] Profile update saves to database correctly
- [ ] Name updates in users table after profile update
- [ ] Change password modal opens correctly
- [ ] Password change requires correct current password
- [ ] New password must be min 8 characters
- [ ] Password confirmation must match
- [ ] Password updates in database with proper hashing
- [ ] Success messages display correctly
- [ ] Error messages display correctly
- [ ] Cancel buttons return to profile view
- [ ] Sidebar profile link highlights correctly
- [ ] Mobile responsive layout works
- [ ] Unauthorized access blocked (different guardian's profile)

## Future Enhancements

Potential improvements for future iterations:

1. **Profile Picture Features**
   - Image cropping tool
   - Multiple image format support
   - Image compression
   - Delete picture option

2. **Notification Preferences**
   - Email notification settings
   - SMS notification settings (when SMS feature added)
   - Notification frequency preferences

3. **Account Settings**
   - Two-factor authentication
   - Login history view
   - Active sessions management
   - Account deletion request

4. **Student Communication**
   - Quick message buttons for each student
   - View student grades from profile
   - Export student information

5. **Profile Enhancements**
   - Emergency contact information
   - Relationship to student field
   - Employment information
   - Additional contact methods

## Related Features

This feature integrates with:
- **Guardian Account System** - Profile data from guardian accounts
- **Guardian-Student Linking** - Displays linked students
- **Authentication System** - Uses guardian guard
- **Messaging System** - Profile info used in messages
- **Email Notifications** - Profile email used for communications

## Support & Maintenance

### Common Issues

**Issue**: "Guardian profile not found" error
- **Cause**: User record exists but no matching guardian record
- **Solution**: Run guardian user account creation script

**Issue**: Can't change email
- **Cause**: Email is readonly for security
- **Solution**: Contact system administrator to change email

**Issue**: Mobile number already in use
- **Cause**: Another guardian has same mobile number
- **Solution**: Use unique mobile number or contact admin

### Maintenance Tasks

1. **Regular Checks**
   - Verify guardian-user account synchronization
   - Check for orphaned user accounts
   - Monitor profile picture storage usage

2. **Data Cleanup**
   - Remove old profile pictures when updated
   - Archive inactive guardian accounts
   - Cleanup test data

3. **Security Updates**
   - Review password requirements periodically
   - Update validation rules as needed
   - Monitor for suspicious activity

## Conclusion

The Guardian Self-Service Profile feature provides guardians with full control over their profile information while maintaining security and data integrity. The feature follows Laravel best practices and integrates seamlessly with the existing guardian portal.
