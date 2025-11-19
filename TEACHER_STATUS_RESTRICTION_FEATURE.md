# Teacher Status Restriction Feature

## Overview
This feature implements access control based on teacher status. When a teacher's status is set to "inactive", "retired", or "resigned", they can only view their profile details but cannot access any other features of the system.

## How It Works

### 1. Teacher Status Types
Teachers can have the following statuses (managed by admin):
- **Active**: Full access to all teacher portal features
- **Inactive**: View-only access to profile
- **Retired**: View-only access to profile  
- **Resigned**: View-only access to profile

### 2. Access Control Implementation

#### Middleware: `CheckTeacherStatus`
**Location**: `app/Http/Middleware/CheckTeacherStatus.php`

This middleware:
- Checks if the authenticated teacher's status is "active"
- If not active, redirects to profile page with a warning message
- Allows access only to specific routes for inactive teachers:
  - `teacher.profile.show` - View profile
  - `teacher.profile.edit` - View profile edit page (read-only)
  - `teacher.profile.password.edit` - View password change page (read-only)
  - `teacher.auth.logout` - Logout

#### Route Protection
**Location**: `routes/web.php`

Routes are organized into two groups:
1. **Protected Routes** (require active status):
   - Dashboard
   - Classes/Subjects
   - Class Records
   - Student Lists
   - Messages
   - Profile Update Actions (PUT/POST/DELETE)

2. **Allowed Routes** (accessible when inactive):
   - Profile Viewing
   - Logout

### 3. User Interface Changes

#### Sidebar Navigation
**Location**: `resources/views/teacher/components/template.blade.php`

- Active teachers see all menu items
- Inactive teachers only see:
  - My Profile
  - Account status warning badge
  - Logout button

#### Profile Page
**Location**: `resources/views/teacher/profile/show.blade.php`

Shows:
- Warning alert for inactive status at the top
- Disabled "Edit Profile" button with lock icon
- Account status badge

### 4. Visual Indicators

#### Warning Messages
When an inactive teacher tries to access restricted pages:
```
Your account is currently inactive. You can only view your profile details. 
Please contact the administrator for assistance.
```

#### Status Badge in Sidebar
```
⚠️ Account Inactive
Limited access - Profile only
```

#### Profile Page Warning
```
⚠️ Account Status: Inactive
Your account is currently inactive. You have view-only access to your profile. 
All other features are restricted. Please contact the administrator for assistance.
```

## Admin Management

### Changing Teacher Status
1. Login as Admin
2. Go to "Teachers" section
3. Click "Edit" on any teacher
4. Change the "Status" dropdown:
   - Active
   - Inactive
   - Retired
   - Resigned
5. Click "Update Teacher"

The teacher's access will be immediately restricted upon changing status to anything other than "active".

## Technical Details

### Database
**Table**: `teachers`
**Column**: `status` (enum: 'active', 'inactive', 'retired', 'resigned')

### Middleware Registration
**Location**: `app/Http/Kernel.php`
```php
'teacher.status' => \App\Http\Middleware\CheckTeacherStatus::class,
```

### Route Implementation
```php
Route::middleware('auth:teacher')->group(function () {
    // Logout (always accessible)
    Route::post('/logout', ...)->name('teacher.auth.logout');
    
    // Protected routes (active status required)
    Route::middleware('teacher.status')->group(function () {
        Route::get('/dashboard', ...);
        Route::get('/subjects', ...);
        // ... other protected routes
    });
    
    // Profile viewing (accessible when inactive)
    Route::get('/profile', ...)->name('teacher.profile.show');
    Route::get('/profile/edit', ...)->name('teacher.profile.edit');
    Route::get('/profile/password/edit', ...)->name('teacher.profile.password.edit');
});
```

## User Experience Flow

### Active Teacher
1. Login → Redirected to Dashboard
2. Full access to all features
3. Can view and edit profile
4. Can access students, grades, messages, etc.

### Inactive Teacher
1. Login → Redirected to Dashboard
2. Middleware intercepts → Redirected to Profile with warning
3. Sees warning banner on profile page
4. Sidebar shows only Profile and Logout
5. Any attempt to access other features → Redirected back to profile with warning
6. Can logout normally

## Testing the Feature

### As Admin:
1. Create or select a test teacher
2. Set their status to "Inactive"
3. Logout from admin

### As Inactive Teacher:
1. Login with the inactive teacher credentials
2. Verify you see only the profile page
3. Try accessing dashboard URL directly → Should redirect to profile
4. Check sidebar → Should show limited menu
5. Verify warning messages appear
6. Confirm logout works

### Re-activating:
1. Login as admin
2. Change teacher status back to "Active"
3. Login as teacher again
4. Verify full access is restored

## Files Modified/Created

### Created:
- `app/Http/Middleware/CheckTeacherStatus.php` - Status checking middleware
- `TEACHER_STATUS_RESTRICTION_FEATURE.md` - This documentation

### Modified:
- `app/Http/Kernel.php` - Registered middleware alias
- `routes/web.php` - Reorganized routes with middleware
- `resources/views/teacher/components/template.blade.php` - Conditional sidebar
- `resources/views/teacher/profile/show.blade.php` - Added warning alerts

## Benefits

1. **Security**: Prevents unauthorized access by inactive teachers
2. **Graceful**: Teachers can still view their information
3. **Clear Communication**: Multiple visual indicators of status
4. **Admin Control**: Easy status management through admin panel
5. **Flexible**: Easy to add/remove allowed routes for inactive teachers

## Future Enhancements

Potential improvements:
1. Allow inactive teachers to download their own documents
2. Add email notification when status changes
3. Add reason field for status change
4. Allow viewing historical data but not current data
5. Add temporary suspension feature with expiration date
