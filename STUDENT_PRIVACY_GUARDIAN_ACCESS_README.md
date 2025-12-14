# Student Privacy Settings - Guardian Access Control

## Overview

This feature allows students to control whether their parents/guardians can view their academic information (grades and enhancements) in the guardian portal. Students have full autonomy over this privacy setting and can toggle it on or off at any time.

## Feature Summary

- **Student Control**: Students can enable/disable guardian access to their grades and enhancement data
- **Default Behavior**: Guardian access is **enabled by default** (allowing transparency)
- **Privacy Respected**: When disabled, guardians see a privacy notice instead of grades
- **Easy Toggle**: Simple on/off switch in student privacy settings page
- **Real-time Effect**: Changes take effect immediately

---

## What Was Implemented

### 1. Database Changes

**Migration File**: `database/migrations/2025_12_14_040352_add_guardian_access_privacy_to_students_table.php`

Added new column to `students` table:
- **Field**: `allow_guardian_access`
- **Type**: `BOOLEAN`
- **Default**: `true` (allows guardian access by default)
- **Purpose**: Stores student's preference for guardian access

### 2. Model Updates

**File**: `app/Models/Student.php`

- Added `allow_guardian_access` to `$fillable` array
- Students can now update this field through the privacy controller

### 3. Controller - Privacy Settings

**File**: `app/Http/Controllers/Student/PrivacyController.php`

**Methods**:
- `index()` - Display privacy settings page
- `updateGuardianAccess()` - Update guardian access permission

**Features**:
- Validates boolean input
- Updates student's privacy preference
- Provides confirmation messages

### 4. Controller Updates - Guardian Access

**Updated Files**:
- `app/Http/Controllers/Guardian/GradeController.php`
- `app/Http/Controllers/Guardian/EnhancementController.php`

**Changes**:
- Added privacy check after student selection
- If `allow_guardian_access` is `false`, returns view with `accessDenied` flag
- Guardian sees privacy notice instead of student data

### 5. Views

#### Student Privacy Settings Page
**File**: `resources/views/student/privacy/index.blade.php`

**Features**:
- Clean, intuitive interface
- Toggle switch for guardian access
- Real-time status updates (JavaScript)
- Informative alerts showing current state
- FAQ sidebar explaining the feature
- Privacy assurance messaging

**Design Elements**:
- Color-coded status (green for enabled, red/yellow for disabled)
- Lock icons for visual representation
- Bootstrap 5 cards and alerts
- Responsive layout

#### Guardian Portal Updates

**Files Modified**:
- `resources/views/guardian/grades/index.blade.php`
- `resources/views/guardian/enhancement/index.blade.php`

**Privacy Notice Display**:
- Shown when `$accessDenied` is true
- Large lock icon
- Clear explanation message
- Respectful tone acknowledging student privacy rights
- Encourages parent-child communication

### 6. Navigation

**File**: `resources/views/student/components/template.blade.php`

Added new menu item in student sidebar:
- **Label**: "Privacy Settings"
- **Icon**: Shield lock icon (`ti-shield-lock`)
- **Route**: `student.privacy.index`
- **Position**: Between "My Profile" and "Messages"

### 7. Routes

**File**: `routes/web.php`

Added two new routes in student authenticated group:
```php
Route::get('/privacy', [PrivacyController::class, 'index'])
    ->name('student.privacy.index');
    
Route::put('/privacy/guardian-access', [PrivacyController::class, 'updateGuardianAccess'])
    ->name('student.privacy.guardian-access.update');
```

---

## How It Works

### For Students

1. **Access Privacy Settings**
   - Log into student portal
   - Click "Privacy Settings" in sidebar
   - View current guardian access status

2. **Change Settings**
   - Toggle the switch on/off
   - Click "Save Changes"
   - See confirmation message
   - Changes apply immediately

3. **Privacy States**
   - **Enabled (Default)**: Guardians can view grades and enhancements
   - **Disabled**: Guardians see privacy notice, no access to academic data

### For Guardians

1. **When Access is Enabled**
   - Can view all student grades
   - Can access enhancement/DSS analysis
   - Full visibility into academic performance

2. **When Access is Disabled**
   - See privacy notice on grades page
   - See privacy notice on enhancement page
   - Notice explains student has enabled privacy settings
   - Encouraged to discuss directly with their child

### For Administrators & Teachers

- **No Impact**: Teachers and admin can always view student data
- Privacy setting only affects guardian portal access
- Student information remains available for academic purposes

---

## User Interface

### Student Privacy Settings Page

**Header Section**:
- Title: "Privacy Settings"
- Subtitle: "Manage who can access your academic information"

**Main Card**:
- Information box explaining guardian access
- Toggle switch with current status
- Color-coded alert showing effect of current setting
- Save and Back buttons

**Sidebar**:
- FAQ section
- Common questions answered
- Privacy assurance messaging

**Visual Feedback**:
- Switch changes color when toggled
- Status text updates (Enabled/Disabled)
- Alert changes color and message
- Lock icon changes (open/closed)

### Guardian Portal - Privacy Notice

**Display**:
- Large lock icon (warning color)
- "Privacy Settings Enabled" heading
- Explanation that student has chosen privacy
- Information note about student rights
- Respectful and non-judgmental tone

---

## Technical Details

### Database Schema

```sql
ALTER TABLE students ADD COLUMN allow_guardian_access BOOLEAN DEFAULT TRUE;
```

### Key Logic Flow

```php
// In Guardian Controllers:
if (!$student->allow_guardian_access) {
    return view('guardian.{grades|enhancement}.index', [
        // ... minimal data
        'accessDenied' => true
    ]);
}
```

### Validation

```php
$request->validate([
    'allow_guardian_access' => 'required|boolean',
]);
```

---

## Security & Privacy

✅ **Authentication Required**: Only logged-in students can change settings  
✅ **Authorization Check**: Students can only modify their own settings  
✅ **Immediate Effect**: Changes apply instantly to guardian portal  
✅ **No Data Leakage**: When disabled, guardians receive no academic data  
✅ **Audit Trail**: Changes are tracked via updated_at timestamp  

---

## Use Cases

### 1. Student Wants Full Transparency
- Keep toggle **enabled** (default)
- Parents can monitor academic progress
- Encourages family involvement in education

### 2. Student Prefers Privacy
- Switch toggle to **disabled**
- Student maintains control over their information
- Parents must communicate directly with student

### 3. Conditional Privacy
- Student can enable/disable anytime
- Might enable during good performance, disable during struggles
- Flexible based on student preference

---

## Testing Guide

### Test as Student

1. **Initial State**
   ```
   - Login as student
   - Navigate to Privacy Settings
   - Should show "Enabled" by default
   - Toggle should be checked
   ```

2. **Disable Guardian Access**
   ```
   - Uncheck the toggle
   - Click "Save Changes"
   - Should see "Disabled" status
   - Alert should be yellow/warning
   ```

3. **Re-enable Guardian Access**
   ```
   - Check the toggle
   - Click "Save Changes"
   - Should see "Enabled" status
   - Alert should be green/success
   ```

### Test as Guardian

1. **With Access Enabled**
   ```
   - Login as guardian
   - View Grades page
   - Should see student's grades
   - View Enhancement page
   - Should see performance data
   ```

2. **With Access Disabled**
   ```
   - Login as guardian
   - View Grades page
   - Should see privacy notice with lock icon
   - View Enhancement page
   - Should see privacy notice with lock icon
   - No student data visible
   ```

### Database Testing

```sql
-- Check student's privacy setting
SELECT student_number, first_name, last_name, allow_guardian_access 
FROM students 
WHERE student_number = 'STUDENT_NUMBER';

-- Manually toggle setting
UPDATE students 
SET allow_guardian_access = 0 
WHERE student_number = 'STUDENT_NUMBER';
```

---

## Installation & Setup

### Step 1: Run Migration

```bash
php artisan migrate
```

This will add the `allow_guardian_access` column to the students table.

### Step 2: Clear Cache (if needed)

```bash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 3: Test the Feature

- Login as a student
- Navigate to Privacy Settings
- Toggle guardian access
- Login as the student's guardian
- Verify privacy notices appear when access is disabled

---

## Files Created/Modified

### New Files (3)
1. `database/migrations/2025_12_14_040352_add_guardian_access_privacy_to_students_table.php`
2. `app/Http/Controllers/Student/PrivacyController.php`
3. `resources/views/student/privacy/index.blade.php`

### Modified Files (6)
1. `app/Models/Student.php` - Added fillable field
2. `app/Http/Controllers/Guardian/GradeController.php` - Added privacy check
3. `app/Http/Controllers/Guardian/EnhancementController.php` - Added privacy check
4. `resources/views/guardian/grades/index.blade.php` - Added privacy notice
5. `resources/views/guardian/enhancement/index.blade.php` - Added privacy notice
6. `resources/views/student/components/template.blade.php` - Added navigation link
7. `routes/web.php` - Added 2 new routes

---

## Future Enhancements

Potential additions:
- **Notification System**: Alert guardians when student changes privacy settings
- **Time-based Access**: Allow students to schedule when guardians can view data
- **Granular Control**: Separate controls for grades vs enhancement
- **Access Log**: Show students when guardians viewed their data
- **Admin Override**: Allow administrators to override in special circumstances
- **Guardian Request**: Let guardians request access with student approval

---

## Support & Troubleshooting

### Issue: Privacy toggle not working
**Solution**: Clear browser cache and refresh the page

### Issue: Guardian still sees data after disabling
**Solution**: Guardian needs to refresh their browser page

### Issue: Migration fails
**Solution**: Check if column already exists, manually add if needed

### Issue: Privacy setting resets
**Solution**: Check database connection and permissions

---

## Best Practices

### For Students
- Consider enabling transparency with your guardians
- Use this feature responsibly
- Communicate with your parents about your choice
- Remember teachers and admin can always view your data

### For Parents/Guardians
- Respect your child's privacy decision
- Use this as an opportunity for open communication
- Discuss academic progress directly with your child
- Contact school if you have concerns

### For Administrators
- Educate students on responsible use of privacy settings
- Encourage parent-child communication about academic performance
- Monitor for misuse (e.g., hiding failing grades)
- Maintain policies on guardian communication

---

## Conclusion

This privacy feature empowers students with control over their academic information while maintaining transparency as the default. It respects student autonomy while encouraging family involvement in education. The implementation is secure, user-friendly, and immediately effective.

**Remember**: The goal is to foster trust and communication between students and their guardians, not to hide information. Use this feature wisely! 🎓🔒
