# Student Profile Feature - Quick Reference Card

## 📍 URLs (Student Portal)

| Action | URL | Method |
|--------|-----|--------|
| View Profile | `/student/profile` | GET |
| Edit Profile Form | `/student/profile/edit` | GET |
| Update Profile | `/student/profile` | PUT |
| Upload Picture | `/student/profile/picture` | POST |
| Delete Picture | `/student/profile/picture` | DELETE |
| Change Password Form | `/student/profile/password/edit` | GET |
| Update Password | `/student/profile/password` | PUT |

## 🎯 Route Names

```php
route('student.profile.show')             // View profile
route('student.profile.edit')             // Edit form
route('student.profile.update')           // Update profile
route('student.profile.picture.update')   // Upload picture
route('student.profile.picture.delete')   // Delete picture
route('student.profile.password.edit')    // Password form
route('student.profile.password.update')  // Update password
```

## 📁 Files Created/Modified

### ✅ New Files
- `app/Http/Controllers/Student/ProfileController.php`
- `resources/views/student/profile/show.blade.php`
- `resources/views/student/profile/edit.blade.php`
- `resources/views/student/profile/change-password.blade.php`
- `README_STUDENT_PROFILE.md`
- `PROFILE_IMPLEMENTATION_SUMMARY.md`
- `TESTING_GUIDE_PROFILE.md`
- `ARCHITECTURE_PROFILE.md`
- `CUSTOMIZATION_GUIDE_PROFILE.md`

### ✏️ Modified Files
- `routes/web.php` (added 7 routes)
- `resources/views/student/components/template.blade.php` (added menu item)

## 🗄️ Database

**Table**: `students`
**Field**: `profile_picture` (varchar, nullable) - ✅ Already exists!

**Storage**: `storage/app/public/profile_pictures/`
**Public URL**: `/storage/profile_pictures/`

## 🔐 Permissions

### Editable by Student
- ✅ Email
- ✅ Mobile number
- ✅ Address
- ✅ Guardian name
- ✅ Guardian contact
- ✅ Guardian email
- ✅ Profile picture
- ✅ Password

### Read-Only (Admin only)
- ❌ Student number
- ❌ First/Middle/Last name
- ❌ Suffix
- ❌ Gender
- ❌ Birthdate
- ❌ Program
- ❌ Academic year
- ❌ Status

## ✔️ Validation Rules

### Profile Update
```php
'email' => 'required|email|unique:students,email,{id}'
'mobile_number' => 'required|string|max:20|unique'
'address' => 'nullable|string|max:500'
'guardian_name' => 'nullable|string|max:255'
'guardian_contact' => 'required|string|max:20|unique'
'guardian_email' => 'required|email|unique'
```

### Profile Picture
```php
'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
```
**Limits**: 2MB max, JPG/PNG/GIF only

### Password Change
```php
'current_password' => 'required'
'password' => 'required|confirmed|min:8'
```

## 🎨 UI Components

### Bootstrap Classes Used
- `card`, `card-header`, `card-body`
- `form-control`, `form-label`, `form-text`
- `btn`, `btn-primary`, `btn-secondary`, `btn-danger`
- `alert`, `alert-success`, `alert-danger`
- `badge`, `bg-success`, `bg-secondary`
- `rounded-circle`
- `shadow-sm`

### Icons (Tabler Icons)
- `ti-user` - Profile/User
- `ti-edit` - Edit button
- `ti-upload` - Upload picture
- `ti-trash` - Delete picture
- `ti-lock` - Security/Password
- `ti-check` - Success/Save
- `ti-x` - Cancel/Close
- `ti-arrow-left` - Back button
- `ti-info-circle` - Information

## 🔧 Common Tasks

### Get Current Student
```php
$user = Auth::guard('student')->user();
$student = Student::findOrFail($user->user_pk_id);
```

### Display Profile Picture
```blade
@if($student->profile_picture)
    <img src="{{ asset('storage/' . $student->profile_picture) }}" alt="Profile">
@else
    <div class="default-avatar">
        <i class="ti ti-user"></i>
    </div>
@endif
```

### Check if Picture Exists
```php
if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
    // Picture exists
}
```

### Delete Picture
```php
if ($student->profile_picture) {
    Storage::disk('public')->delete($student->profile_picture);
    $student->update(['profile_picture' => null]);
}
```

### Store New Picture
```php
$path = $request->file('profile_picture')->store('profile_pictures', 'public');
$student->update(['profile_picture' => $path]);
```

### Verify Password
```php
if (Hash::check($request->current_password, $user->password)) {
    // Password correct
}
```

### Update Password
```php
$user->update(['password' => Hash::make($request->password)]);
```

## 🚨 Common Errors & Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| Image not displaying | No storage link | `php artisan storage:link` |
| Upload fails | File too large | Check `upload_max_filesize` in php.ini |
| Validation error (unique) | Email/contact exists | Check database for duplicates |
| 403 Unauthorized | Not logged in | Check auth middleware |
| 404 Not Found | Wrong student ID | Verify `user_pk_id` exists |

## 🎯 Testing Quick Commands

```bash
# Create storage link
php artisan storage:link

# Clear cache
php artisan cache:clear

# Check routes
php artisan route:list --name=student.profile

# Run tests (if you create them)
php artisan test --filter=ProfileTest

# Check file permissions
# Linux/Mac
chmod -R 775 storage

# View logs
tail -f storage/logs/laravel.log
```

## 📞 Support Resources

**Laravel Docs:**
- File Storage: https://laravel.com/docs/filesystem
- Validation: https://laravel.com/docs/validation
- Authentication: https://laravel.com/docs/authentication

**Local Documentation:**
- Setup: `README_STUDENT_PROFILE.md`
- Testing: `TESTING_GUIDE_PROFILE.md`
- Architecture: `ARCHITECTURE_PROFILE.md`
- Customization: `CUSTOMIZATION_GUIDE_PROFILE.md`

## ⚡ Quick Troubleshooting

```bash
# Problem: Images not showing
php artisan storage:link
php artisan config:clear

# Problem: Validation errors
php artisan cache:clear
php artisan route:clear

# Problem: Session issues
php artisan session:clear

# Problem: Can't upload files
# Check php.ini:
# upload_max_filesize = 2M
# post_max_size = 2M
# max_file_uploads = 20

# Problem: Permission denied
# Linux/Mac:
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage

# Windows:
# Right-click storage folder > Properties > Security
# Give full control to IIS_IUSRS or IUSR
```

## 🎉 Success Indicators

✅ Storage link exists at `public/storage`
✅ Can navigate to `/student/profile`
✅ Profile information displays correctly
✅ Can upload and view profile picture
✅ Can edit contact information
✅ Can change password successfully
✅ Validation errors display properly
✅ No PHP errors in logs
✅ No console errors in browser
✅ Responsive on mobile devices

---

**Version**: 1.0 | **Date**: October 15, 2025 | **Status**: ✅ Production Ready

---

## 💾 Backup Checklist

Before making changes:
- [ ] Backup `routes/web.php`
- [ ] Backup `resources/views/student/components/template.blade.php`
- [ ] Backup database
- [ ] Backup `storage/app/public/profile_pictures/` folder

---

## 📱 Access on Mobile

**Test URLs:**
```
http://your-domain/student/login
http://your-domain/student/profile
```

**Responsive Breakpoints:**
- Desktop: ≥1200px (4 columns)
- Tablet: 768px-1199px (2 columns)
- Mobile: <768px (1 column, stacked)

---

**Print this card for quick reference! 📄**
