# Subject-Teacher Assignment Feature

## Summary of Changes

### ✅ Setup Complete
1. **Academic Year**: Created active academic year (2025-2026)
2. **Teacher Profiles**: All teachers profiled for all subjects (38 links)
3. **Backend Endpoints**: Added 2 new routes
4. **Frontend UI**: Added button and modal for teacher assignment

### Backend Changes (SectionAdviserController.php)

#### Modified Methods:
1. **saveAdvisers()** - Now persists to `academic_year_strand_advisers` table (required for FK)
2. **getSubjects()** - Returns subjects with `assigned_teacher` info

#### New Methods Added:
1. **subjectTeachers(Request $request)**
   - Route: `POST /admin/section-advisers/subject-teachers`
   - Returns teachers eligible for a subject (filtered by `teacher_subject` pivot)
   
2. **saveSubjectTeacher(Request $request)**
   - Route: `POST /admin/section-advisers/save-subject-teacher`
   - Validates teacher is profiled for subject
   - Requires strand adviser to exist first
   - Saves to `academic_year_strand_subjects` table

### Frontend Changes (index.blade.php)

#### New UI Elements:
1. **Button**: Added users icon button (🧑‍🤝‍🧑) next to each section's "View students" button
2. **Modal**: "Assign Teachers to Subjects" modal with:
   - Info alert explaining teacher profiling requirement
   - Table showing all subjects for grade level
   - Dropdown per subject (populated with eligible teachers)
   - Save button per subject

#### New JavaScript Functions:
1. `openSubjectTeacherAssignment(strandCode, section)` - Opens modal, loads subjects
2. `fetchTeachersForSubject(subjectId, rowId, currentTeacherId)` - Populates teacher dropdown
3. `saveSubjectTeacher(rowId, strandCode, gradeLevel, subjectId)` - Saves assignment
4. `saveAdvisersToDb(advisers)` - Helper to persist adviser

### Database Requirements

#### Tables Used:
1. `academic_years` - Must have 1 active record
2. `teacher_subject` - Pivot linking teachers to subjects they can teach
3. `academic_year_strand_advisers` - Stores adviser per strand (FK requirement)
4. `academic_year_strand_subjects` - Stores teacher assignments per subject

#### Constraints:
- `academic_year_strand_subjects.academic_year_strand_adviser_id` is REQUIRED (FK)
- Unique constraint: `teacher_id + academic_year_id + strand_id + subject_id`

## Testing Instructions

### 1. Navigate to Section Advisers
```
http://127.0.0.1:8000/admin/section-advisers
```

### 2. Assign an Adviser First
- Select a teacher from the "Adviser" dropdown for any section
- Click "Save All Adviser Assignments" (green button at top)

### 3. Assign Teachers to Subjects
- Click the **users icon** (🧑‍🤝‍🧑) button next to the section
- Modal opens showing all subjects for that grade level
- Each subject has:
  - Dropdown showing eligible teachers (only those profiled for that subject)
  - Current assignment displayed
  - Save button (💾)

### 4. Select and Save
- Choose a teacher from dropdown
- Click the save button (💾)
- Green alert confirms success
- "Assigned Teacher" column updates immediately

### 5. Verify
- Close and reopen the modal - assignments should persist
- Check different sections - each can have different teachers

## Expected Behavior

✅ **Success Cases:**
- Teachers profiled for subject appear in dropdown
- Save shows green success alert
- Assignment persists across page refreshes
- Can clear assignment by selecting "-- Select Teacher --"

❌ **Error Cases:**
- No active academic year → "No active academic year" error
- No adviser assigned → "Please assign an adviser first" error
- Teacher not profiled → "Teacher not profiled for this subject" error
- No teachers profiled → Dropdown shows "No teachers profiled for this subject"

## Troubleshooting

### If save fails:
1. Check browser console (F12) for errors
2. Ensure academic year is active: `php artisan tinker --execute="echo \App\Models\AcademicYear::where('is_active', true)->count();"`
3. Ensure teachers are profiled: `php scripts/profile_all_teachers.php`
4. Ensure adviser is saved first

### If dropdowns are empty:
- Run: `php scripts/profile_all_teachers.php`
- This creates links between teachers and subjects

## Key Files Modified

1. `app/Http/Controllers/Admin/SectionAdviserController.php` - Backend logic
2. `routes/web.php` - Added 2 POST routes
3. `resources/views/admin/section_advisers/index.blade.php` - UI and JavaScript
4. `scripts/setup_academic_year.php` - Helper to create academic year
5. `scripts/profile_all_teachers.php` - Helper to profile teachers

## Feature Complete! ✅

The feature is now fully functional and ready to use.
