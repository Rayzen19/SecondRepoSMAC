# EMAIL MISMATCH FIX GUIDE

## Problem Summary
There are TWO different people with the same name "John Raymond Barrogo":

1. **Teacher (User ID 5)**
   - Email: `johnraymond.barrogo@cvsu.edu.ph`
   - Type: teacher
   - Teacher ID: 1

2. **Student (User ID 26)**  
   - Email: `johnraymondbarrogo08@gmail.com`
   - Type: student
   - Student ID: 21

## Current Situation
The teacher sent messages TO the student, which is working correctly. However, there's confusion about which account should be used.

## Solutions

### Solution 1: If they are the SAME person (Teacher using wrong account)

The teacher should NOT have a student account. Fix:

```bash
php artisan tinker
```

Then run:
```php
// Delete the duplicate student account
DB::table('users')->where('id', 26)->delete();
DB::table('students')->where('id', 21)->delete();

// Ensure teacher uses correct email
// If teacher wants to use Gmail:
DB::table('teachers')->where('id', 1)->update(['email' => 'johnraymondbarrogo08@gmail.com']);
DB::table('users')->where('id', 5)->update(['email' => 'johnraymondbarrogo08@gmail.com']);

// Clear cache
exit;
```

```bash
php artisan cache:clear
php artisan config:clear
```

### Solution 2: If they are DIFFERENT people (Two people with same name)

This is correct, but the STUDENT (User 26) should NOT be able to access `/teacher/messenger`.

**Check**: Is the student account somehow getting teacher privileges?

```bash
php artisan tinker
```

```php
$user = App\Models\User::find(26);
echo "Type: " . $user->type;
echo "\nCan access teacher routes: " . ($user->type === 'teacher' ? 'YES' : 'NO');
exit;
```

If the student has `type = 'teacher'`, fix it:

```php
DB::table('users')->where('id', 26)->update(['type' => 'student']);
```

### Solution 3: Update Teacher Email to Match Login

If the teacher PREFERS to use `johnraymondbarrogo08@gmail.com`:

Run the interactive script:
```bash
php fix_teacher_email.php
```

Choose option 2 to use the Gmail address.

## Verification Steps

After applying the fix:

1. Log out from all accounts
2. Log in as teacher using: `johnraymond.barrogo@cvsu.edu.ph` (or Gmail if you chose that)
3. Go to `/teacher/messenger`
4. Messages should display correctly
5. Students should see the correct teacher email

## Prevention

- Don't create accounts with duplicate names unless they're different people
- Use institutional emails (@cvsu.edu.ph) for teachers
- Use personal emails for students
- Enforce email uniqueness in the system
