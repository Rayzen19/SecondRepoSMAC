# Admin Password Change Script

## Overview
This script allows you to change the admin password from the command line. The password is now **changeable** and not fixed, with full validation to ensure security requirements are met.

## Usage

### Basic Usage (Change first admin's password)
```bash
php change_admin_password.php "YourNewPassword@123"
```

### Change Specific Admin's Password by Email
```bash
php change_admin_password.php "YourNewPassword@123" "admin@school.test"
```

## Password Requirements

The new password must meet the following criteria:
- ✓ At least **8 characters** long
- ✓ Contains both **uppercase** and **lowercase** letters
- ✓ Contains at least **one number**
- ✓ Contains at least **one special character** (@$!%*#?&)

## Examples

### Valid Passwords
```bash
php change_admin_password.php "SecurePass@123"
php change_admin_password.php "MyAdmin2025!"
php change_admin_password.php "Str0ng#Password"
```

### Invalid Passwords (Will be rejected)
```bash
php change_admin_password.php "weak"           # Too short, no uppercase, no number, no special char
php change_admin_password.php "password123"    # No uppercase, no special character
php change_admin_password.php "PASSWORD@"      # No lowercase, no number
```

## Error Messages

### No Arguments Provided
```
Usage: php change_admin_password.php <new_password> [email]
Example: php change_admin_password.php 'NewP@ssw0rd123'
Example with email: php change_admin_password.php 'NewP@ssw0rd123' admin@school.test
```

### Password Validation Failed
```
Password does not meet requirements:
- At least 8 characters long
- Contains both uppercase and lowercase letters
- Contains at least one number
- Contains at least one special character (@$!%*#?&)

✗ The password field must be at least 8 characters.
✗ The password field format is invalid.
```

### Admin Not Found
```
No admin user found with email: wrong@email.com
```

## Success Output
```
✓ Admin password updated successfully!
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Email: admin@school.test
Name: Administrator
New Password: YourNewPassword@123
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

## Security Notes

1. **Always use quotes** around your password to prevent shell interpretation of special characters
2. The password is **hashed** before being stored in the database
3. The script validates the password against the same requirements as the web interface
4. Consider deleting this script from production servers to prevent unauthorized password changes

## Features

- ✓ **Changeable passwords** - Not fixed, fully customizable
- ✓ **Password validation** - Enforces security requirements
- ✓ **Specific admin targeting** - Change password by email
- ✓ **Clear feedback** - Detailed error messages and success confirmation
- ✓ **Secure hashing** - Uses Laravel's Hash facade for bcrypt hashing
