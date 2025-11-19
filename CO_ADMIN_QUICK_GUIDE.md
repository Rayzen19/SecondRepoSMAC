# Co-Admin Quick Reference Guide

## What is Co-Admin?

Co-Admin is a new user privilege that allows users to login through the admin form and have full access to all admin features, just like regular admins.

## Quick Start

### 1. Create a Co-Admin User

**Option A: Using the interactive script**
```bash
php create_co_admin.php
```
Follow the prompts to enter name, email, and password.

**Option B: Using the quick test script (pre-configured)**
```bash
php create_test_coadmin.php
```
This creates a test co-admin with:
- Email: `coadmin@test.com`
- Password: `password`

### 2. Login as Co-Admin

1. Navigate to: `/admin/login`
2. Enter co-admin email and password
3. You'll be redirected to the admin dashboard
4. Co-admins have full access to all admin features

### 3. Verify Co-Admin Users

```bash
php list_coadmins.php
```
This lists all co-admin users in the database.

## Key Features

✅ **Same Login Form**: Co-admins use the admin login form  
✅ **Full Access**: Co-admins have identical permissions to admins  
✅ **Separate Guard**: Uses dedicated authentication guard for security  
✅ **Easy Management**: Simple scripts for creating and managing co-admins

## Test Credentials

A test co-admin has been created for you:

```
Email: coadmin@test.com
Password: password
Login URL: /admin/login
```

## File Changes Summary

1. **Migration**: Added 'co-admin' to users table type enum
2. **Model**: Created `CoAdminUser` model
3. **Auth Config**: Added co-admin guard and provider
4. **Login Controller**: Updated to handle co-admin authentication
5. **Middleware**: Updated to allow co-admin access
6. **Routes**: Protected admin routes for co-admin access

## Helper Scripts

- `create_co_admin.php` - Interactive co-admin creation
- `create_test_coadmin.php` - Quick test co-admin creation
- `list_coadmins.php` - List all co-admin users

## Need Help?

See `CO_ADMIN_FEATURE.md` for complete documentation including:
- Detailed implementation details
- Security considerations
- Future enhancement suggestions
- Troubleshooting guide
