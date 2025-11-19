# Co-Admin Feature Implementation

## Overview
This document describes the implementation of the **Co-Admin** privilege in the SMAC system. Co-Admins have the same access as regular Admins and can login through the admin login form.

## Changes Made

### 1. Database Migration
**File**: `database/migrations/2025_11_11_000001_add_co_admin_type_to_users_table.php`

- Modified the `users` table `type` enum column to include 'co-admin'
- New enum values: `'admin', 'co-admin', 'teacher', 'student', 'guardian'`
- Migration has been run successfully

### 2. CoAdminUser Model
**File**: `app/Models/Auth/CoAdminUser.php`

Created a new model for Co-Admin authentication:
```php
class CoAdminUser extends User
{
    protected $table = 'users';
    
    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'co-admin');
        });
    }
}
```

### 3. Authentication Configuration
**File**: `config/auth.php`

Added new guard and provider:
- **Guard**: `co-admin` - Uses session driver with co-admins provider
- **Provider**: `co-admins` - Uses CoAdminUser model

### 4. Login Controller Updates
**File**: `app/Http/Controllers/Admin/LoginController.php`

Updated three methods:

#### a. `showLoginForm()`
- Added check for co-admin authentication
- Redirects authenticated co-admins to admin dashboard

#### b. `login()`
- Added co-admin authentication attempt after admin attempt
- Co-admins are redirected to admin dashboard upon successful login
- Authentication order: admin → co-admin → teacher → student → guardian

#### c. `logout()`
- Added co-admin logout handling
- Properly clears co-admin session

### 5. Middleware Updates
**File**: `app/Http/Middleware/RedirectIfAdminAuthenticated.php`

- Updated to check both admin and co-admin guards
- Redirects authenticated co-admins to dashboard

### 6. Routes Protection
**File**: `routes/web.php`

- Updated admin routes middleware from `auth:admin` to `auth:admin,co-admin`
- This allows both admin and co-admin guards to access admin routes

## Creating a Co-Admin Account

### Method 1: Using the Helper Script
Run the provided PHP script:
```bash
php create_co_admin.php
```

Follow the prompts to enter:
- Co-Admin name
- Co-Admin email
- Co-Admin password

### Method 2: Manual Database Entry
Insert directly into the database:
```sql
INSERT INTO users (name, email, password, email_verified_at, type, user_pk_id, created_at, updated_at)
VALUES (
    'Co-Admin Name',
    'coadmin@example.com',
    '$2y$12$hashedPasswordHere',  -- Use Hash::make() or bcrypt
    NOW(),
    'co-admin',
    NULL,
    NOW(),
    NOW()
);
```

### Method 3: Using Laravel Tinker
```bash
php artisan tinker
```

Then run:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Co-Admin Name',
    'email' => 'coadmin@example.com',
    'password' => Hash::make('your-password'),
    'email_verified_at' => now(),
    'type' => 'co-admin',
    'user_pk_id' => null,
]);
```

## Login Process

1. Co-Admins use the same login form as Admins: `/admin/login`
2. Enter co-admin email and password
3. Upon successful authentication, redirected to admin dashboard
4. Co-Admins have full access to all admin features

## Permissions

**Co-Admins have the SAME permissions as regular Admins**, including access to:
- Dashboard
- Student management
- Teacher management
- Subject management
- Strand management
- Academic year management
- Section management
- Announcements
- Messages
- Reports
- Profile settings
- All other admin features

## Security Considerations

1. **Separate Guard**: Co-Admins use a separate authentication guard (`co-admin`) for better security and session management
2. **Same Access Level**: Currently, co-admins have identical permissions to admins
3. **Future Enhancement**: If you need to restrict co-admin permissions, you can:
   - Create a role-based permission system
   - Add middleware to check user type
   - Implement feature flags based on user type

## Testing

### Create a Test Co-Admin
```bash
php create_co_admin.php
```

### Test Login
1. Go to `/admin/login`
2. Enter co-admin credentials
3. Verify redirect to admin dashboard
4. Test admin features (should all work)
5. Test logout functionality

### Verify Database
```sql
SELECT id, name, email, type, created_at FROM users WHERE type = 'co-admin';
```

## Future Enhancements

If you need to differentiate permissions between admin and co-admin:

1. **Add a permissions table**:
```php
Schema::create('permissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('permission');
    $table->timestamps();
});
```

2. **Create a permission checking middleware**:
```php
class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (Auth::guard('admin')->check()) {
            return $next($request); // Admins have all permissions
        }
        
        if (Auth::guard('co-admin')->check()) {
            $user = Auth::guard('co-admin')->user();
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
            abort(403, 'Unauthorized action.');
        }
        
        return redirect()->route('admin.auth.loginForm');
    }
}
```

3. **Apply to specific routes**:
```php
Route::delete('/students/{student}', [...])
    ->middleware(['auth:admin,co-admin', 'permission:delete_students']);
```

## Troubleshooting

### Co-Admin Can't Login
1. Verify user exists: `SELECT * FROM users WHERE type = 'co-admin'`
2. Check password is hashed correctly
3. Clear cache: `php artisan cache:clear`
4. Check session configuration

### Co-Admin Redirected to Wrong Page
1. Verify guard configuration in `config/auth.php`
2. Check middleware on routes
3. Clear config cache: `php artisan config:clear`

### Co-Admin Has No Access to Pages
1. Verify route middleware includes `co-admin`: `auth:admin,co-admin`
2. Check for any hard-coded `Auth::guard('admin')` checks
3. Review custom middleware

## Summary

The Co-Admin feature has been successfully implemented with:
- ✅ Database schema updated
- ✅ CoAdminUser model created
- ✅ Authentication guards configured
- ✅ Login controller updated
- ✅ Middleware updated
- ✅ Routes protected for co-admin access
- ✅ Helper script for creating co-admins
- ✅ Full documentation

Co-Admins can now login through the admin form and have full access to all admin features.
