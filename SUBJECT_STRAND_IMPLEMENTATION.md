# Strand-Specific Subject Implementation

## Overview
This document describes the implementation of strand-specific subject creation in the admin dashboard. When adding a new subject, admins must now select which strand the subject belongs to, and the subject will be automatically linked to that strand with grading configurations.

## Changes Made

### 1. Database Migration
**File:** `database/migrations/2025_10_17_115455_add_hours_to_subjects_table.php`
- Added `hours` column to `subjects` table (nullable, integer)
- This field stores the total hours for the subject (e.g., 320 hours for ICT courses)

### 2. Subject Model Update
**File:** `app/Models/Subject.php`
- Added `hours` to `$fillable` array
- Allows mass assignment of hours field

### 3. Subject Controller Updates
**File:** `app/Http/Controllers/Admin/SubjectController.php`

#### Changes:
- **Imports:** Added `Strand` and `StrandSubject` models
- **`create()` method:** Now passes active strands to the view
- **`store()` method:** 
  - Added validation for `strand_id` and grading percentages
  - Validates that grading percentages sum to 100%
  - Automatically creates `StrandSubject` link when subject is created
  - Links subject to selected strand with grading configuration
- **`index()` method:** Added eager loading for `strandSubjects.strand` relationship

### 4. Subject Creation Form
**File:** `resources/views/admin/subjects/create.blade.php`

#### New Fields:
1. **Strand Dropdown** (Required)
   - Displays all active strands
   - Format: "CODE — Name"
   - Helper text: "Subject will be linked to this strand"

2. **Hours Field** (Optional)
   - Integer input for total subject hours
   - Example: 320 hours for Computer Programming

3. **Subject Code** (Required)
   - Now has placeholder: "e.g., ICT201"

4. **Subject Name** (Required)
   - Now has placeholder: "e.g., Computer Programming (JAVA)"

5. **Grading Configuration Section**
   - Written Works % (default: 20%)
   - Performance Tasks % (default: 60%)
   - Quarterly Assessment % (default: 20%)
   - JavaScript validation ensures percentages sum to 100%
   - Info alert explaining the requirement

#### JavaScript Validation:
- Real-time validation of percentage sum
- Submit button disabled until sum equals 100%
- Warning message displays current sum if not 100%

### 5. Subject Index Page
**File:** `resources/views/admin/subjects/index.blade.php`

#### New Columns:
- **Strand(s):** Displays badges for all strands the subject is linked to
- **Type:** Badge showing subject type (Core/Applied/Specialized)
  - Core: Green badge
  - Applied: Blue badge
  - Specialized: Yellow badge

### 6. Subject Show Page
**File:** `resources/views/admin/subjects/show.blade.php`

#### Updates:
- Changed layout from 2-column to 3-column for Units/Type
- Added **Hours** field display in General tab
- Shows "N/A" if hours not specified

## Usage Example: TVL Strand Subjects

### Creating TVL ICT Subjects:

#### Example 1: Computer Programming (JAVA)
```
Strand: TVL — Technical-Vocational-Livelihood
Subject Code: ICT201
Subject Name: Computer Programming (JAVA)
Hours: 320
Units: 3
Type: Specialized
Semester: 1st

Grading Configuration:
- Written Works: 20%
- Performance Tasks: 60%
- Quarterly Assessment: 20%
```

#### Example 2: Computer Programming (.NET)
```
Strand: TVL — Technical-Vocational-Livelihood
Subject Code: ICT202
Subject Name: Computer Programming (.NET TECHNOLOGY)
Hours: 320
Units: 3
Type: Specialized
Semester: 1st

Grading Configuration:
- Written Works: 20%
- Performance Tasks: 60%
- Quarterly Assessment: 20%
```

#### Example 3: Work Immersion
```
Strand: TVL — Technical-Vocational-Livelihood
Subject Code: ICT301
Subject Name: Work Immersion / Research / Career Advocacy / Culminating Activity
Hours: (leave blank or enter appropriate hours)
Units: 2
Type: Applied
Semester: 2nd

Grading Configuration:
- Written Works: 20%
- Performance Tasks: 60%
- Quarterly Assessment: 20%
```

## Workflow

### Before (Old Flow):
1. Admin creates subject with basic info
2. Admin manually navigates to "Link Subject and Strand" page
3. Admin selects strand and subject
4. Admin configures grading percentages
5. Admin submits

### After (New Flow):
1. Admin navigates to "Add Subject" page
2. Admin selects strand first (required)
3. Admin enters subject details (code, name, hours, etc.)
4. Admin configures grading percentages (validated to sum to 100%)
5. Admin submits
6. **Subject is created AND automatically linked to strand**

## Benefits

1. **Streamlined Process:** One-step subject creation with automatic strand linking
2. **Validation:** JavaScript ensures grading percentages always sum to 100%
3. **Better Organization:** Subjects are always associated with at least one strand
4. **Hours Tracking:** Optional hours field for detailed subject information
5. **Visual Feedback:** Subject index shows which strands each subject belongs to
6. **Type Indicators:** Color-coded badges for quick subject type identification

## Technical Notes

### Database Relationships:
- **subjects** table: Contains subject basic information
- **strand_subjects** pivot table: Links subjects to strands with grading config
- **strands** table: Contains strand information

### Validation Rules:
```php
'strand_id' => 'required|exists:strands,id'
'hours' => 'nullable|integer|min:0'
'written_works_percentage' => 'required|numeric|min:0|max:100'
'performance_tasks_percentage' => 'required|numeric|min:0|max:100'
'quarterly_assessment_percentage' => 'required|numeric|min:0|max:100'
```

### Automatic Linking:
When a subject is created, the system automatically creates a `StrandSubject` record with:
- `strand_id`: Selected strand
- `subject_id`: Newly created subject
- `semestral_period`: Same as subject's semester
- `written_works_percentage`: From form
- `performance_tasks_percentage`: From form
- `quarterly_assessment_percentage`: From form
- `is_active`: true

## Future Enhancements

Possible improvements:
1. Allow linking subject to multiple strands during creation
2. Preset grading configurations per strand type
3. Import subjects via CSV with strand associations
4. Bulk strand-subject linking
5. Subject templates for common courses

## Testing

To test the implementation:
1. Navigate to Admin > Subjects > Add Subject
2. Select a strand (e.g., TVL)
3. Fill in subject details:
   - Code: ICT201
   - Name: Computer Programming (JAVA)
   - Hours: 320
   - Type: Specialized
   - Semester: 1st
4. Adjust grading percentages (must sum to 100%)
5. Click Save
6. Verify subject is created and appears in Subject List with strand badge
7. Click on subject to view details and confirm hours field is displayed
8. Verify strand-subject link was created automatically

## Support

For issues or questions about this implementation, check:
- `app/Http/Controllers/Admin/SubjectController.php` - Business logic
- `resources/views/admin/subjects/create.blade.php` - Form and validation
- `database/migrations/2025_10_17_115455_add_hours_to_subjects_table.php` - Schema changes
