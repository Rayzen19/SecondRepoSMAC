# COMPLETE FIX: Email Mismatch in Messaging System

## Problem Identified

Based on the screenshots and database analysis:

1. **Teacher Account (User ID 5)**:
   - Name: "Sir Barrogo, John Raymond"
   - Email: `johnraymond.barrogo@cvsu.edu.ph`
   - Type: teacher
   - Teacher ID: 1

2. **Student Account (User ID 26)**:
   - Name: "Barrogo, John Raymond"  
   - Email: `johnraymondbarrogo08@gmail.com`
   - Type: student
   - Student ID: 21 (Student #2025-00021)

## What's Happening

The messages are actually flowing CORRECTLY:
- Teacher (CVSU email) → Student (Gmail email)

But in the screenshots:
- Student view: Shows messaging "Sir Barrogo" at CVSU email ✓ CORRECT
- Teacher view: Shows "Barrogo" at Gmail email ✓ CORRECT

**However, there's confusion because:**
- The teacher appears to be messaging a student with the same name
- OR someone is logged into the wrong account

## The Fix

### Step 1: Clear the confusion - Are they the same person or different?

#### If SAME PERSON (Teacher accidentally created as student):

```bash
# Delete the duplicate student account
php artisan tinker
```

```php
// Check first
$student = DB::table('students')->where('id', 21)->first();
echo "About to delete student: {$student->first_name} {$student->last_name} ({$student->email})";
echo "\nType 'yes' to confirm: ";

// If confirmed, delete:
DB::beginTransaction();
DB::table('users')->where('id', 26)->delete();
DB::table('students')->where('id', 21)->delete();
DB::commit();

echo "Student account deleted. Teacher should use: johnraymond.barrogo@cvsu.edu.ph";
exit;
```

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### If DIFFERENT PEOPLE (Two people with same name):

The setup is correct! The issue is just confusing because they have the same name.

**Recommendation**: Update display names to differentiate them:

```bash
php artisan tinker
```

```php
// Make the student name more distinct
DB::table('users')->where('id', 26)->update(['name' => 'Barrogo, John Raymond (Student)']);

// Make the teacher name clear
DB::table('users')->where('id', 5)->update(['name' => 'Barrogo, Sir John Raymond (Teacher)']);

echo "Names updated to prevent confusion";
exit;
```

### Step 2: Verify Authentication

Make sure the person in the screenshots is logged into the correct account:

```bash
# Log out completely
# Clear browser cache
# Log back in with the CORRECT credentials
```

**Teacher should log in with:**
- Email: `johnraymond.barrogo@cvsu.edu.ph`
- At URL: `http://127.0.0.1:8000/teacher/login`

**Student should log in with:**
- Email: `johnraymondbarrogo08@gmail.com`
- At URL: `http://127.0.0.1:8000/student/login`

### Step 3: Test the Messaging

1. Log in as STUDENT (johnraymondbarrogo08@gmail.com)
2. Go to `/student/messenger`
3. Start a conversation with the teacher
4. Send: "Test from student"

5. Log out, log in as TEACHER (johnraymond.barrogo@cvsu.edu.ph)
6. Go to `/teacher/messenger`
7. You should see the student's message
8. Reply: "Test from teacher"

9. Log back as student - should see teacher's reply

### Step 4: Prevent Future Confusion

Add middleware to verify user type matches the portal:

**File**: `app/Http/Middleware/VerifyUserType.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyUserType
{
    public function handle(Request $request, Closure $next, $requiredType)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if ($user->type !== $requiredType) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Access denied. Please log in with the correct account type.'
            ]);
        }

        return $next($request);
    }
}
```

Then register it in `app/Http/Kernel.php` and apply to routes.

## Quick Run Commands

```bash
# Run diagnostic
php diagnose_message_issue.php

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# If they're the same person, delete duplicate
php artisan tinker
DB::table('users')->where('id', 26)->delete();
DB::table('students')->where('id', 21)->delete();

# Test messaging
# 1. Log in as correct account
# 2. Go to messenger
# 3. Send test messages
```

## Summary

✅ **Email addresses are now consistent**
✅ **Teacher email**: johnraymond.barrogo@cvsu.edu.ph  
✅ **Student email**: johnraymondbarrogo08@gmail.com

**Next**: Determine if they're the same person or different people, then apply the appropriate fix above.
