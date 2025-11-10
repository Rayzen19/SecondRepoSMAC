# Guardian Account System Implementation

## Overview
This implementation automatically creates guardian accounts in the `guardians` table when students are created or updated, establishing proper relationships between students and their guardians.

## What Was Fixed

### 1. **Model Relationships**
- Added `guardians()` relationship to `Student` model (BelongsToMany)
- Added `students()` relationship to `Guardian` model (BelongsToMany)
- Added `getName()` accessor to `Guardian` model for full name display

### 2. **StudentController Updates**

#### When Creating a Student:
1. Student record is created with their information
2. Guardian is automatically created or found based on guardian email
3. Guardian is linked to the student via `guardian_students` pivot table
4. Both student and guardian receive email notifications

#### When Updating a Student:
1. If guardian email changes, the old guardian is detached
2. New guardian is created or found and linked to the student
3. If guardian email stays the same, guardian information is updated

#### When Deleting a Student:
1. Guardian-student relationships are properly detached
2. Student record is deleted
3. Guardian records remain for other students (if shared)

### 3. **Guardian Creation Logic**

The system intelligently handles guardian accounts:

- **Checks for existing guardian** by email before creating a new one
- **Parses guardian name** into first, middle, last name components
- **Generates unique guardian number** in format: `GRD-YYYY-#####`
- **Links multiple students** to the same guardian if they share the same guardian email
- **Default status** is set to 'active'
- **Logs all operations** for debugging and tracking

### 4. **Email Notifications**

Both student and guardian receive emails when:
- Student account is created
- Password is regenerated

Guardian email includes:
- Student's name and student number
- Student's login credentials
- Link to student portal
- Security reminders

## Database Structure

### Tables:
1. **students** - Student information (includes guardian_name, guardian_email, guardian_contact for legacy data)
2. **guardians** - Guardian accounts with full information
3. **guardian_students** - Pivot table linking guardians to students (many-to-many)

### Guardian Fields:
- `guardian_number` - Unique identifier (GRD-YYYY-#####)
- `first_name`, `middle_name`, `last_name`, `suffix` - Name components
- `gender` - Gender (male/female)
- `email` - Unique email address
- `mobile_number` - Contact number
- `address` - Physical address
- `status` - Active/Inactive
- `profile_picture` - Optional profile image

## Migration Script

### Migrate Existing Students
A migration script is provided to create guardian records for existing students:

```bash
php migrate_existing_guardians.php
```

This script will:
1. Find all students with guardian information
2. Create guardian records (or find existing ones by email)
3. Link guardians to students via the pivot table
4. Display progress and summary statistics

**Important:** Run this script once after implementation to migrate existing data.

## Usage Examples

### Creating a Student (Admin Panel)
When you create a student and fill in:
- Guardian Name: "John Doe"
- Guardian Email: "john.doe@example.com"
- Guardian Contact: "09171234567"

The system will:
1. Create student account
2. Create/find guardian account with email "john.doe@example.com"
3. Link guardian to student
4. Send welcome emails to both

### Multiple Students, Same Guardian
If you create another student with the same guardian email:
- The system will find the existing guardian
- Link the new student to the same guardian
- Both students will be associated with one guardian account

### Checking Guardian-Student Relationships

```php
// Get all guardians for a student
$student->guardians; // Returns collection of Guardian models

// Get all students for a guardian
$guardian->students; // Returns collection of Student models

// Check if student has a guardian
if ($student->guardians()->exists()) {
    $guardian = $student->guardians()->first();
    echo "Guardian: " . $guardian->name;
}
```

## Features

✅ **Automatic guardian creation** when student is created
✅ **Smart duplicate detection** - uses existing guardian if email matches
✅ **Many-to-many relationship** - one guardian can have multiple students
✅ **Name parsing** - intelligently splits full names into components
✅ **Unique guardian numbers** - auto-generated in format GRD-YYYY-#####
✅ **Email notifications** - both student and guardian receive credentials
✅ **Update handling** - guardian info updates when student is updated
✅ **Soft delete support** - proper cleanup on deletion
✅ **Migration script** - easy migration of existing data
✅ **Error handling** - graceful error handling with logging

## Next Steps

1. **Run the migration script** to create guardian records for existing students:
   ```bash
   php migrate_existing_guardians.php
   ```

2. **Verify guardian accounts** are created by checking the Guardian List in the admin panel

3. **Test the flow** by creating a new student and verifying:
   - Guardian record is created
   - Guardian appears in Guardian List
   - Both emails are sent successfully

4. **Optional**: Create a Guardian Portal where guardians can:
   - Log in with their guardian account
   - View all their wards (students)
   - Monitor academic performance
   - Communicate with teachers

## Files Modified

1. `app/Models/Guardian.php` - Added relationships and getName accessor
2. `app/Models/Student.php` - Added guardians relationship
3. `app/Http/Controllers/Admin/StudentController.php` - Added guardian creation/update logic
4. `app/Mail/GuardianNotification.php` - Guardian email notification
5. `resources/views/emails/guardian_notification.blade.php` - Guardian email template
6. `migrate_existing_guardians.php` - Migration script for existing data

## Troubleshooting

### Guardian not created?
- Check logs in `storage/logs/laravel.log`
- Verify guardian email is provided and valid
- Check if guardian with same email already exists

### Duplicate guardian numbers?
- This shouldn't happen, but if it does, check the guardian number generation logic
- Ensure the `guardian_number` column has a unique constraint

### Student-Guardian link missing?
- Check `guardian_students` pivot table
- Run the migration script to fix existing data
- Verify relationships are properly defined in models

## Support

For issues or questions, check the Laravel logs at `storage/logs/laravel.log`
