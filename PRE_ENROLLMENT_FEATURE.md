# Pre-Enrollment Feature

## Overview
The Pre-Enrollment feature allows students to submit their enrollment preferences for the upcoming academic year. Students can select their preferred grade level (Grade 11 or Grade 12), strand, and section.

## Features

### For Students
1. **View Current Enrollment**: Students can see their current academic year, grade level, strand, and section
2. **Submit Pre-Enrollment**: Students can submit their preferences for next year including:
   - Grade Level (G-11 or G-12)
   - Strand (from available active strands)
   - Section (optional preference based on grade level and strand)
3. **View Submission Status**: After submitting, students can see:
   - Status (Pending, Approved, Rejected, or Enrolled)
   - Submission date and time
   - Selected preferences
   - Admin remarks (if any)
4. **Cancel Pre-Enrollment**: Students can cancel their pending pre-enrollment submission

### Key Features
- **Dynamic Section Loading**: Sections are loaded dynamically based on the selected grade level and strand
- **One-Time Submission**: Students can only submit their pre-enrollment once (unless they cancel)
- **Pre-Enrollment Availability Check**: System checks if pre-enrollment is enabled in the current academic year
- **Enrollment Validation**: Students must have an active enrollment to submit pre-enrollment

## Files Created

### Database
- **Migration**: `database/migrations/2025_11_15_create_pre_enrollments_table.php`
  - Creates `pre_enrollments` table with columns:
    - student_id
    - current_academic_year_id
    - target_academic_year_id
    - strand_id
    - section_id
    - grade_level
    - status (pending, approved, rejected, enrolled)
    - remarks
    - submitted_at
    - processed_at
    - processed_by

### Models
- **PreEnrollment Model**: `app/Models/PreEnrollment.php`
  - Relationships with Student, AcademicYear, Strand, Section, and User

### Controllers
- **PreEnrollmentController**: `app/Http/Controllers/Student/PreEnrollmentController.php`
  - `index()` - Display pre-enrollment form and existing submissions
  - `getSections()` - AJAX endpoint to load sections based on grade level and strand
  - `store()` - Process and save pre-enrollment submission
  - `cancel()` - Delete pending pre-enrollment

### Views
- **Pre-Enrollment Form**: `resources/views/student/pre_enrollment/index.blade.php`
  - Shows current enrollment information
  - Displays existing submission if already submitted
  - Pre-enrollment form with grade level, strand, and section selection
  - Dynamic section loading with AJAX

### Routes
Added to `routes/web.php` under student group:
```php
Route::get('/pre-enrollment', [PreEnrollmentController::class, 'index'])->name('student.pre-enrollment.index');
Route::post('/pre-enrollment', [PreEnrollmentController::class, 'store'])->name('student.pre-enrollment.store');
Route::post('/pre-enrollment/sections', [PreEnrollmentController::class, 'getSections'])->name('student.pre-enrollment.sections');
Route::delete('/pre-enrollment/{id}', [PreEnrollmentController::class, 'cancel'])->name('student.pre-enrollment.cancel');
```

### Navigation
- Updated `resources/views/student/components/template.blade.php`
  - Fixed pre-enrollment link to route to the actual form
  - Link is enabled/disabled based on `pre_enrollment_enabled` flag

## How to Use

### For Admin
1. Enable pre-enrollment in the Section Advisers page
2. Pre-enrollment becomes available to all eligible students
3. Monitor and process pre-enrollment submissions (admin interface to be developed)

### For Students
1. Navigate to the Pre-Enrollment menu (visible when enabled)
2. Review your current enrollment information
3. Select your preferences:
   - Grade Level (G-11 or G-12)
   - Strand
   - Section (optional)
4. Submit your pre-enrollment
5. View your submission status

## Database Schema

### pre_enrollments table
```
id                          BIGINT (Primary Key)
student_id                  BIGINT (Foreign Key -> students)
current_academic_year_id    BIGINT (Foreign Key -> academic_years)
target_academic_year_id     BIGINT (Foreign Key -> academic_years, nullable)
strand_id                   BIGINT (Foreign Key -> strands)
section_id                  BIGINT (Foreign Key -> sections, nullable)
grade_level                 VARCHAR (e.g., 'G-11', 'G-12')
status                      ENUM (pending, approved, rejected, enrolled)
remarks                     TEXT (nullable)
submitted_at                TIMESTAMP (nullable)
processed_at                TIMESTAMP (nullable)
processed_by                BIGINT (Foreign Key -> users, nullable)
created_at                  TIMESTAMP
updated_at                  TIMESTAMP
deleted_at                  TIMESTAMP (nullable - soft deletes)
```

## Access Control
- Pre-enrollment is only available when `pre_enrollment_enabled` is true in the active academic year
- Students must have an active enrollment in the current academic year
- Students can only submit one pre-enrollment per academic year

## Future Enhancements
1. **Admin Interface**: Create admin pages to:
   - View all pre-enrollment submissions
   - Approve/reject pre-enrollments
   - Add remarks
   - Generate reports
2. **Email Notifications**: Send emails when pre-enrollment status changes
3. **Pre-Enrollment Reports**: Generate statistics and reports
4. **Bulk Processing**: Allow admins to bulk approve/reject submissions
5. **Section Capacity**: Check and display available slots per section
6. **Auto-Enrollment**: Automatically create enrollments from approved pre-enrollments

## Testing
To test the feature:
1. Ensure you have an active academic year with `pre_enrollment_enabled = true`
2. Login as a student with an active enrollment
3. Navigate to Pre-Enrollment menu
4. Submit a pre-enrollment and verify it's saved
5. Try to submit again and verify the restriction
6. Cancel the pre-enrollment and verify it's deleted

## Notes
- Section selection is optional and serves as a preference only
- The actual section assignment is done by administrators
- Pre-enrollment submissions can be cancelled only while in "pending" status
- Only Grade 11 and Grade 12 are available for pre-enrollment (senior high school)
